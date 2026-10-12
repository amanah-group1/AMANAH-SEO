<?php
/**
 * CORE-007 Schema REST boundary.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Schema_Controller {
	private $generator;
	private $allowed_types = array( 'Organization', 'Person', 'Article', 'FAQ', 'LocalBusiness', 'Product' );
	public function __construct( $generator ) { $this->generator = $generator; }
	public function register() { add_action( 'rest_api_init', array( $this, 'register_routes' ) ); }
	public function register_routes() {
		register_rest_route( 'amanah/v1', '/schema/generate', array(
			'methods' => 'POST',
			'callback' => array( $this, 'rest_generate' ),
			'permission_callback' => array( $this, 'rest_permission' ),
			'args' => array(
				'type' => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field', 'validate_callback' => function ( $value ) { return in_array( sanitize_text_field( $value ), $this->allowed_types, true ); } ),
				'entity_id' => array( 'required' => true, 'sanitize_callback' => 'absint', 'validate_callback' => function ( $value ) { return absint( $value ) > 0; } ),
				'post_id' => array( 'required' => false, 'sanitize_callback' => 'absint' ),
			),
		) );
	}
	public function rest_permission( $request ) {
		$post_id = absint( $request->get_param( 'post_id' ) );
		return current_user_can( 'amanah_generate_schema' ) && ( ! $post_id || current_user_can( 'edit_post', $post_id ) );
	}
	public function rest_generate( $request ) {
		$result = $this->generator->generate( (string) $request->get_param( 'type' ), absint( $request->get_param( 'entity_id' ) ), absint( $request->get_param( 'post_id' ) ), get_current_user_id() );
		return is_wp_error( $result ) ? $this->error_response( $result ) : rest_ensure_response( array( 'success' => true, 'data' => $result ) );
	}
	private function error_response( $error ) {
		$data = $error->get_error_data();
		$status = is_array( $data ) && isset( $data['status'] ) ? absint( $data['status'] ) : 500;
		return new WP_REST_Response( array( 'success' => false, 'message' => $error->get_error_message(), 'error_code' => sanitize_key( $error->get_error_code() ) ), $status );
	}
}
