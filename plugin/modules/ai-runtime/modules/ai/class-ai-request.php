<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Request {
	private $task;
	private $model;
	private $prompt;
	private $input;

	public function __construct( $task, $model, $prompt, array $input = array() ) {
		$this->task = sanitize_key( $task );
		$this->model = sanitize_text_field( $model );
		$this->prompt = (string) $prompt;
		$this->input = $input;

		if ( '' === $this->task || '' === $this->model || '' === trim( $this->prompt ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'AI request is invalid.', 'validation' );
		}
	}

	public function get_task() { return $this->task; }
	public function get_model() { return $this->model; }
	public function get_prompt() { return $this->prompt; }
	public function get_input() { return $this->input; }
}
