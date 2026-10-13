<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_AI_Response {
	private $content;
	private $provider;
	private $model;
	private $input_tokens;
	private $output_tokens;
	public function __construct( $content, $provider, $model, $input_tokens = null, $output_tokens = null ) {
		$this->content = (string) $content;
		$this->provider = sanitize_key( $provider );
		$this->model = sanitize_text_field( $model );
		$this->input_tokens = null === $input_tokens ? null : absint( $input_tokens );
		$this->output_tokens = null === $output_tokens ? null : absint( $output_tokens );
	}
	public function get_content() { return $this->content; }
	public function get_provider() { return $this->provider; }
	public function get_model() { return $this->model; }
	public function get_input_tokens() { return $this->input_tokens; }
	public function get_output_tokens() { return $this->output_tokens; }
}
