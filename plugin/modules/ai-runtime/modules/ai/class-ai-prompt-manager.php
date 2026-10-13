<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Prompt_Manager {
	private $path;

	private $tasks = array(
		'entity_analysis',
		'content_analysis',
		'content_generation',
		'content_generation_quality',
		'content_refinement',
		'faq_generation',
		'schema_generation',
		'audit_recommendation',
	);

	public function __construct( $path = null ) {
		$this->path = $path ? trailingslashit( $path ) : AMANAH_SEO_AI_PATH . 'prompts/';
	}

	public function supports( $task ) {
		return in_array( sanitize_key( $task ), $this->tasks, true );
	}

	public function build( $task, array $input ) {
		$task = sanitize_key( $task );
		if ( ! $this->supports( $task ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'Unsupported AI task.', 'validation' );
		}

		$file = $this->path . $task . '.txt';
		if ( ! is_readable( $file ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'AI prompt template is unavailable.', 'configuration' );
		}

		$template = file_get_contents( $file );
		if ( false === $template || '' === trim( $template ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'AI prompt template is empty.', 'configuration' );
		}

		preg_match_all( '/\{\{([a-z0-9_\-]+)\}\}/', $template, $matches );
		$required = array_unique( $matches[1] );

		foreach ( $required as $key ) {
			if ( ! array_key_exists( $key, $input ) ) {
				throw new AMANAH_SEO_AI_AI_Exception( 'AI prompt input is incomplete.', 'validation' );
			}
		}

		foreach ( $input as $key => $value ) {
			$key = sanitize_key( $key );
			$replacement = is_scalar( $value ) ? (string) $value : wp_json_encode( $value );
			$template = str_replace( '{{' . $key . '}}', $replacement, $template );
		}

		if ( preg_match( '/\{\{([a-z0-9_\-]+)\}\}/', $template ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'AI prompt contains unresolved inputs.', 'validation' );
		}

		return $template;
	}
}
