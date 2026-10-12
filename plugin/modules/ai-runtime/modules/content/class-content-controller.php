<?php
/**
 * Content Analyzer request and presentation boundary.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Content_Controller {
	private $analyzer;
	private $repository;
	private $entity_model;

	public function __construct( $analyzer, $repository, $entity_model = null ) {
		$this->analyzer     = $analyzer;
		$this->repository   = $repository;
		$this->entity_model = $entity_model ? $entity_model : ( class_exists( 'AMANAH_SEO_AI_Entity_Model' ) ? new AMANAH_SEO_AI_Entity_Model() : null );
	}

	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'admin_menu', array( $this, 'register_menu' ), 20 );
	}

	public function register_routes() {
		register_rest_route(
			'amanah/v1',
			'/content/analyze',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_analyze' ),
				'permission_callback' => array( $this, 'rest_permission' ),
				'args'                => array(
					'post_id' => array(
						'required' => true,
						'sanitize_callback' => 'absint',
						'validate_callback' => function ( $value ) { return absint( $value ) > 0; },
					),
				),
			)
		);
	}

	public function rest_permission( $request ) {
		$post_id = absint( $request->get_param( 'post_id' ) );
		return $post_id > 0 && current_user_can( 'edit_post', $post_id );
	}

	public function rest_analyze( $request ) {
		$post_id = absint( $request->get_param( 'post_id' ) );
		$context = $this->build_analysis_context( get_current_user_id(), $post_id );

		if ( is_wp_error( $context ) ) {
			return $this->error_response( $context );
		}

		$result = $this->analyzer->analyze( $post_id, get_current_user_id(), $context );

		if ( is_wp_error( $result ) ) {
			return $this->error_response( $result );
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Content analysis completed.', 'amanah-seo-ai' ),
				'data'    => $result,
			)
		);
	}

	public function register_menu() {
		add_submenu_page(
			'amanah-seo',
			__( 'Content Analyzer', 'amanah-seo-ai' ),
			__( 'Content Analyzer', 'amanah-seo-ai' ),
			'edit_posts',
			'amanah-content-analyzer',
			array( $this, 'render' )
		);
	}

	public function render() {
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : ( isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0 );
		$post = $post_id ? get_post( $post_id ) : null;
		$context = $post ? $this->build_analysis_context( get_current_user_id(), $post_id ) : array();
		$keyword = is_wp_error( $context ) ? '' : ( $context['keyword'] ?? '' );
		$entity = is_wp_error( $context ) ? '' : ( $context['entity'] ?? '' );
		$result = ( $post && current_user_can( 'edit_post', $post_id ) ) ? $this->repository->find_latest_by_content( $post_id ) : null;
		$error = '';
		$diagnostic = array();
		$analysis_result = null;

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_content_analyze'] ) ) {
			check_admin_referer( 'amanah_content_analyze' );

			$context = $this->build_analysis_context( get_current_user_id(), $post_id );
			$analysis = is_wp_error( $context ) ? $context : $this->analyzer->analyze( $post_id, get_current_user_id(), $context );

			if ( is_wp_error( $analysis ) ) {
				$error = $analysis->get_error_message();
				$error_data = $analysis->get_error_data();
				if ( is_array( $error_data ) && isset( $error_data['diagnostic'] ) && is_array( $error_data['diagnostic'] ) ) {
					$diagnostic = array(
						'category' => sanitize_key( $error_data['diagnostic']['category'] ),
						'cause' => sanitize_key( $error_data['diagnostic']['cause'] ),
						'message' => sanitize_text_field( $error_data['diagnostic']['message'] ?? '' ),
					);
				}
			} else {
				$analysis_result = $analysis;
				$result = $this->repository->find_latest_by_content( $post_id );
			}
		}

		require AMANAH_SEO_AI_PATH . 'modules/content/views/content-analysis.php';
	}

	private function build_analysis_context( $user_id, $post_id ) {
		$post = get_post( absint( $post_id ) );
		if ( ! $post ) {
			return new WP_Error( 'content_not_found', __( 'The selected WordPress content could not be found.', 'amanah-seo-ai' ), array( 'status' => 404 ) );
		}

		$keyword = '';
		$keyword_meta_keys = array( '_amanah_focus_keyword', '_yoast_wpseo_focuskw', 'rank_math_focus_keyword' );
		foreach ( $keyword_meta_keys as $meta_key ) {
			$value = get_post_meta( $post_id, $meta_key, true );
			if ( is_string( $value ) && '' !== trim( $value ) ) {
				$keyword = sanitize_text_field( $value );
				break;
			}
		}
		// Do not substitute the post title for a missing keyword. Keyword context must remain explicit.

		$entity = '';
		$entity_id = 0;
		if ( $this->entity_model ) {
			$entities = $this->entity_model->get_current_user_entities( absint( $user_id ) );
			if ( ! empty( $entities ) ) {
				$entity_id = absint( $entities[0]->id );
				$entity = sanitize_text_field( $entities[0]->entity_name );
			}
		}
		if ( '' === $entity ) {
			return new WP_Error( 'missing_entity_context', __( 'No AMANAH SEO AI Entity Profile is available for the current user.', 'amanah-seo-ai' ), array( 'status' => 400 ) );
		}

		return array(
			'keyword'   => $keyword,
			'entity'    => $entity,
			'entity_id' => $entity_id,
		);
	}

	private function error_response( $error ) {
		$status = $error->get_error_data();
		$status_code = is_array( $status ) && isset( $status['status'] ) ? absint( $status['status'] ) : 500;
		$payload = array(
			'success' => false,
			'message' => $error->get_error_message(),
			'error_code' => sanitize_key( $error->get_error_code() ),
		);

		if ( is_array( $status ) && isset( $status['diagnostic'] ) && is_array( $status['diagnostic'] ) ) {
			$payload['diagnostic'] = array(
				'category' => sanitize_key( $status['diagnostic']['category'] ),
				'cause' => sanitize_key( $status['diagnostic']['cause'] ),
			);
		}

		if ( is_array( $status ) && isset( $status['analysis_id'] ) ) {
			$payload['analysis_id'] = sanitize_text_field( $status['analysis_id'] );
		}

		return new WP_REST_Response( $payload, $status_code );
	}
}
