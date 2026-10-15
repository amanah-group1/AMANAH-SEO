<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Token_Manager {
	public function input( AMANAH_SEO_AI_AI_Response $response ) {
		return $response->get_input_tokens();
	}

	public function output( AMANAH_SEO_AI_AI_Response $response ) {
		return $response->get_output_tokens();
	}

	public function usage( AMANAH_SEO_AI_AI_Response $response ) {
		return array(
			'input_tokens'  => $this->input( $response ),
			'output_tokens' => $this->output( $response ),
		);
	}
}
