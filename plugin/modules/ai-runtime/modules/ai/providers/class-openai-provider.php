<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_OpenAI_Provider implements AMANAH_SEO_AI_AI_Provider_Interface {
	private $credentials;
	private $model;
	private $user_id;
	public function __construct( $credentials, $model, $user_id = null ) { $this->credentials=$credentials; $this->model=$model; $this->user_id=$user_id; }
	public function get_name() { return 'openai'; }
	public function generate( AMANAH_SEO_AI_AI_Request $request ) {
		$key = $this->credentials->get( 'openai_api_key', $this->user_id );
		if ( ! $key ) throw new AMANAH_SEO_AI_AI_Exception( 'OpenAI credentials are not configured.', 'configuration' );
		$response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', array(
			'timeout'=>60,
			'headers'=>array( 'Content-Type'=>'application/json', 'Authorization'=>'Bearer '.$key ),
			'body'=>wp_json_encode( array( 'model'=>$this->model, 'messages'=>array( array( 'role'=>'user', 'content'=>$request->get_prompt() ) ) ) ),
		) );
		if ( is_wp_error( $response ) ) throw new AMANAH_SEO_AI_AI_Exception( $response->get_error_message(), 'transport' );
		$status = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $status < 200 || $status >= 300 ) {
			$category = 429 === $status || $status >= 500 ? 'retryable' : ( in_array( $status, array(401,403), true ) ? 'authentication' : 'provider' );
			throw new AMANAH_SEO_AI_AI_Exception( 'OpenAI provider request failed.', $category, $status );
		}
		$text = isset( $body['choices'][0]['message']['content'] ) ? $body['choices'][0]['message']['content'] : '';
		if ( '' === $text ) throw new AMANAH_SEO_AI_AI_Exception( 'OpenAI returned an empty response.', 'provider' );
		$usage = isset( $body['usage'] ) ? $body['usage'] : array();
		$input = isset( $usage['prompt_tokens'] ) ? $usage['prompt_tokens'] : null;
		$output = isset( $usage['completion_tokens'] ) ? $usage['completion_tokens'] : null;
		return new AMANAH_SEO_AI_AI_Response( $text, 'openai', $this->model, $input, $output );
	}
}
