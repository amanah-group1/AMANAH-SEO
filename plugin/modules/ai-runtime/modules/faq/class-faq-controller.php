<?php
/**
 * CORE-007 FAQ REST boundary.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_FAQ_Controller {
	private $generator;
	public function __construct( $generator ) { $this->generator = $generator; }
	public function register() { add_action( 'rest_api_init', array( $this, 'register_routes' ) ); }
	public function register_routes() {
		register_rest_route( 'amanah/v1', '/faq/generate', array(
			'methods' => 'POST',
			'callback' => array( $this, 'rest_generate' ),
			'permission_callback' => array( $this, 'rest_permission' ),
			'args' => array( 'post_id' => array( 'required' => true, 'sanitize_callback' => 'absint', 'validate_callback' => function ( $value ) { return absint( $value ) > 0; } ) ),
		) );
	}
	public function rest_permission( $request ) {
		$post_id = absint( $request->get_param( 'post_id' ) );
		return $post_id > 0 && current_user_can( 'edit_post', $post_id );
	}
	public function rest_generate( $request ) {
		$result = $this->generator->generate( absint( $request->get_param( 'post_id' ) ), get_current_user_id() );
		return is_wp_error( $result ) ? $this->error_response( $result ) : rest_ensure_response( array( 'success' => true, 'data' => $result ) );
	}
	private function error_response( $error ) {
		$data = $error->get_error_data();
		$status = is_array( $data ) && isset( $data['status'] ) ? absint( $data['status'] ) : 500;
		return new WP_REST_Response( array( 'success' => false, 'message' => $error->get_error_message(), 'error_code' => sanitize_key( $error->get_error_code() ) ), $status );
	}
}
