<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_AI_Response_Handler {
	private $validator;
	public function __construct( $validator ) { $this->validator = $validator; }
	public function handle( $task, AMANAH_SEO_AI_AI_Response $response ) {
		$content = trim( str_replace( array( chr(96).chr(96).chr(96).'json', chr(96).chr(96).chr(96) ), '', $response->get_content() ) );
		$decoded = json_decode( $content, true );
		if ( JSON_ERROR_NONE !== json_last_error() ) throw new AMANAH_SEO_AI_AI_Exception( 'AI response could not be parsed.', 'validation' );
		return $this->validator->validate( $task, $decoded );
	}
}
