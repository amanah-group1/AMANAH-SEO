<?php
/**
 * CORE-007 Schema generation orchestration.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Schema_Generator {
	private $ai_client;
	private $repository;
	private $validator;
	private $allowed_types = array( 'Organization', 'Person', 'Article', 'FAQ', 'LocalBusiness', 'Product' );

	public function __construct( $ai_client, $repository, $validator ) {
		$this->ai_client  = $ai_client;
		$this->repository = $repository;
		$this->validator  = $validator;
	}

	public function generate( $type, $entity_id, $post_id, $user_id ) {
		$type      = sanitize_text_field( $type );
		$entity_id = absint( $entity_id );
		$post_id   = absint( $post_id );
		$user_id   = absint( $user_id );

		if ( ! in_array( $type, $this->allowed_types, true ) || ! $entity_id || ! $user_id ) {
			return new WP_Error( 'invalid_parameter', __( 'Schema type, Entity, and user are required.', 'amanah-seo-ai' ), array( 'status' => 400 ) );
		}

		global $wpdb;
		$entity_table = $wpdb->prefix . 'amanah_entities';
		$entity       = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$entity_table} WHERE id = %d AND status = %s LIMIT 1", $entity_id, 'active' ),
			ARRAY_A
		);

		if ( ! $entity ) {
			return new WP_Error( 'entity_not_found', __( 'The Entity was not found.', 'amanah-seo-ai' ), array( 'status' => 404 ) );
		}

		if ( absint( $entity['user_id'] ) !== $user_id && ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'permission_denied', __( 'You are not allowed to use this Entity.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}

		$post = $post_id ? get_post( $post_id ) : null;
		if ( $post_id && ( ! $post || ! current_user_can( 'edit_post', $post_id ) ) ) {
			return new WP_Error( 'permission_denied', __( 'You are not allowed to use this content.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}

		$input = array(
			'data' => wp_json_encode(
				array(
					'type'    => $type,
					'entity'  => $entity,
					'content' => $post ? array(
						'title' => sanitize_text_field( get_the_title( $post_id ) ),
						'body'  => wp_kses_post( $post->post_content ),
					) : null,
				)
			),
		);

		try {
			$response = $this->ai_client->generate( 'schema_generation', $input, $user_id );
		} catch ( Exception $error ) {
			return new WP_Error( 'ai_execution_failure', __( 'Schema generation could not be completed by the AI service.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
		}

		$raw       = isset( $response['result']['schema'] ) && is_array( $response['result']['schema'] ) ? $response['result']['schema'] : array();
		$validated = $this->validator->validate( $raw, $type );
		if ( is_wp_error( $validated ) ) {
			return $validated;
		}

		$created_at = function_exists( 'current_time' ) ? current_time( 'mysql', true ) : gmdate( 'Y-m-d H:i:s' );
		$schema_id  = $this->repository->save( $entity_id, $post_id, $type, $validated );

		if ( false === $schema_id || ! $schema_id ) {
			return new WP_Error( 'storage_error', __( 'The generated Schema could not be saved.', 'amanah-seo-ai' ), array( 'status' => 500 ) );
		}

		return array(
			'entity_id'    => $entity_id,
			'post_id'      => $post_id ?: null,
			'schema_type'  => $type,
			'schema'       => $validated,
			'traceability' => array(
				'generation_ref' => 'schema:' . absint( $schema_id ),
				'user_id'        => $user_id,
				'entity_id'      => $entity_id,
				'post_id'        => $post_id ?: null,
				'provider'       => isset( $response['provider'] ) ? sanitize_text_field( $response['provider'] ) : '',
				'model'          => isset( $response['model'] ) ? sanitize_text_field( $response['model'] ) : '',
				'status'         => 'completed',
				'created_at'     => $created_at,
			),
		);
	}
}
