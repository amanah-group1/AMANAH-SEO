<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Entity_REST_Controller {
	private $controller;
	private $model;

	public function __construct( $controller, $model ) {
		$this->controller = $controller;
		$this->model      = $model;
	}

	public function register() {
		register_rest_route(
			'amanah/v1',
			'/entity',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
			)
		);

		register_rest_route(
			'amanah/v1',
			'/entity/(?P<id>\d+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'read' ),
					'permission_callback' => array( $this, 'can_view' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
			)
		);
	}

	public function can_view() {
		return current_user_can( 'amanah_view_entity' );
	}

	public function can_manage() {
		return current_user_can( 'amanah_manage_entity' );
	}

	public function create( WP_REST_Request $request ) {
		$result = $this->controller->save( $request->get_params(), get_current_user_id() );
		return is_wp_error( $result )
			? $result
			: new WP_REST_Response( $this->model->get_entity( $result, get_current_user_id(), true ), 201 );
	}

	public function read( WP_REST_Request $request ) {
		$entity = $this->model->get_entity( $request['id'], get_current_user_id(), true );
		return $entity
			? rest_ensure_response( $entity )
			: new WP_Error( 'not_found', __( 'Entity not found.', 'amanah-seo-ai' ), array( 'status' => 404 ) );
	}

	public function update( WP_REST_Request $request ) {
		$id = absint( $request['id'] );
		$result = $this->controller->save(
			array_merge( $request->get_params(), array( 'id' => $id ) ),
			get_current_user_id()
		);
		return is_wp_error( $result )
			? $result
			: rest_ensure_response( $this->model->get_entity( $id, get_current_user_id(), true ) );
	}

	public function delete( WP_REST_Request $request ) {
		$result = $this->controller->delete( absint( $request['id'] ), get_current_user_id() );
		return is_wp_error( $result )
			? $result
			: new WP_REST_Response( array( 'deleted' => true ), 200 );
	}
}
