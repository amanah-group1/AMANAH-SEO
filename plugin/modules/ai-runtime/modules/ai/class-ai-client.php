<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Client {
	private $settings;
	private $credentials;
	private $resolver;
	private $prompts;
	private $handler;
	private $retry;
	private $tokens;
	private $costs;
	private $logger;

	public function __construct() {
		$this->settings = new AMANAH_SEO_AI_AI_Settings();
		$this->credentials = new AMANAH_SEO_AI_AI_Credentials( $this->settings );
		$this->resolver = new AMANAH_SEO_AI_AI_Provider_Resolver( $this->settings, $this->credentials );
		$this->prompts = new AMANAH_SEO_AI_AI_Prompt_Manager();
		$this->handler = new AMANAH_SEO_AI_AI_Response_Handler( new AMANAH_SEO_AI_AI_Safety_Validator() );
		$this->retry = new AMANAH_SEO_AI_AI_Retry_Manager();
		$this->tokens = new AMANAH_SEO_AI_AI_Token_Manager();
		$this->costs = new AMANAH_SEO_AI_AI_Cost_Manager();
		$this->logger = new AMANAH_SEO_AI_AI_Logger();
	}

	private function required_entitlement_for_task( string $task ): ?string {
		$entitlements = array(
			'content_analysis'   => 'content_analyzer',
			'content_generation' => 'content_generation',
			'content_refinement' => 'content_generation',
			'faq_generation'     => 'faq_generation',
			'schema_generation'  => 'schema_generation',
			'entity_analysis'    => 'entity_analysis',
		);
		return $entitlements[ $task ] ?? null;
	}

	public function generate( $task, array $input = array(), $user_id = null ) {
		$task = sanitize_key( $task );
		$entitlement = $this->required_entitlement_for_task( $task );
		if ( null === $entitlement || ! class_exists( 'AMANAH_SEO_Module_Licenses' ) || ! AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', $entitlement ) ) {
			throw new AMANAH_SEO_AI_AI_Exception(
				'AI feature is not available under the current license.',
				'authentication',
				403,
				'ai_entitlement_missing'
			);
		}
		$prompt = $this->prompts->build( $task, $input );
		$provider = $this->resolver->resolve( $user_id );
		$fallback = $this->resolver->resolve_fallback( $user_id );

		$model = 'gemini' === $provider->get_name()
			? $this->settings->get( 'gemini_model', $user_id )
			: $this->settings->get( 'openai_model', $user_id );
		$request = new AMANAH_SEO_AI_AI_Request( $task, $model, $prompt, $input );

		$run = function () use ( $provider, $request ) {
			return $provider->generate( $request );
		};

		$fallback_run = null;
		if ( $fallback ) {
			$fallback_model = 'gemini' === $fallback->get_name()
				? $this->settings->get( 'gemini_model', $user_id )
				: $this->settings->get( 'openai_model', $user_id );
			$fallback_request = new AMANAH_SEO_AI_AI_Request(
				$request->get_task(),
				$fallback_model,
				$request->get_prompt(),
				$request->get_input()
			);
			$fallback_run = function () use ( $fallback, $fallback_request ) {
				return $fallback->generate( $fallback_request );
			};
		}

		try {
			$response = $this->retry->run( $run, $fallback_run );
			$result = $this->handler->handle( $task, $response );
			$usage = $this->tokens->usage( $response );
			$cost = $this->costs->calculate(
				$response->get_provider(),
				$response->get_model(),
				$usage['input_tokens'],
				$usage['output_tokens']
			);
			$this->logger->log( $task, $response, $prompt, $cost );

			return array(
				'result'        => $result,
				'provider'      => $response->get_provider(),
				'model'         => $response->get_model(),
				'input_tokens'  => $usage['input_tokens'],
				'output_tokens' => $usage['output_tokens'],
				'cost'          => $cost,
			);
		} catch ( AMANAH_SEO_AI_AI_Exception $error ) {
			error_log(
				sprintf(
					'[AMANAH SEO AI] AI failure: category=%s cause=%s code=%d',
					sanitize_key( $error->get_category() ),
					sanitize_key( $error->get_diagnostic_cause() ),
					(int) $error->getCode()
				)
			);

			throw new AMANAH_SEO_AI_AI_Exception(
				$error->getMessage(),
				$error->get_category(),
				$error->getCode(),
				$error->get_diagnostic_cause()
			);
		}
	}
}
