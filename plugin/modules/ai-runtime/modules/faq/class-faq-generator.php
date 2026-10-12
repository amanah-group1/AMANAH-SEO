<?php
/**
 * CORE-007 FAQ generation orchestration.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_FAQ_Generator {
	private $ai_client;
	private $repository;
	private $validator;
	private $entity_resolver;
	private $analysis_resolver;

	public function __construct( $ai_client, $repository, $validator, $entity_resolver, $analysis_resolver ) {
		$this->ai_client         = $ai_client;
		$this->repository        = $repository;
		$this->validator         = $validator;
		$this->entity_resolver   = $entity_resolver;
		$this->analysis_resolver = $analysis_resolver;
	}

	public function generate( $post_id, $user_id ) {
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );

		if ( ! $post_id || ! $user_id ) {
			return new WP_Error( 'invalid_parameter', __( 'A valid post is required.', 'amanah-seo-ai' ), array( 'status' => 400 ) );
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'not_found', __( 'Content was not found.', 'amanah-seo-ai' ), array( 'status' => 404 ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new WP_Error( 'permission_denied', __( 'You are not allowed to generate FAQs for this content.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}

		$analysis_context = $this->analysis_resolver->resolve_for_post( $post_id );
		if ( is_wp_error( $analysis_context ) ) {
			return $analysis_context;
		}

		$entity = $this->entity_resolver->resolve_for_post( $post_id, $user_id );
		if ( is_wp_error( $entity ) ) {
			return $entity;
		}

		$input = array(
			'content' => wp_json_encode(
				array(
					'article' => array(
						'title'   => sanitize_text_field( get_the_title( $post_id ) ),
						'topic'   => sanitize_text_field( get_the_title( $post_id ) ),
						'content' => wp_kses_post( $post->post_content ),
					),
					'content_analysis' => array(
						'analysis_id' => $analysis_context['analysis_id'],
						'version'     => $analysis_context['version'],
						'result'      => $analysis_context['analysis'],
					),
					'entity' => $entity,
				)
			),
		);

		try {
			$response = $this->ai_client->generate( 'faq_generation', $input, $user_id );
		} catch ( Exception $error ) {
			return new WP_Error( 'ai_execution_failure', __( 'FAQ generation could not be completed by the AI service.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
		}

		$raw = isset( $response['result'] ) && is_array( $response['result'] ) ? $response['result'] : array();
		$validated = $this->validator->validate( $raw );
		if ( is_wp_error( $validated ) ) {
			return $validated;
		}

		$generation_id = wp_generate_uuid4();
		$previous = $this->repository->find( $post_id );
		$version = $previous && isset( $previous['version'] ) ? absint( $previous['version'] ) + 1 : 1;
		$payload = array(
			'version'          => $version,
			'status'           => 'active',
			'faq'              => $validated['faq'],
			'generation_id'    => $generation_id,
			'user_id'          => $user_id,
			'provider'         => isset( $response['provider'] ) ? sanitize_text_field( $response['provider'] ) : '',
			'model'            => isset( $response['model'] ) ? sanitize_text_field( $response['model'] ) : '',
			'created_at'       => current_time( 'mysql', true ),
			'entity_id'        => absint( $entity['id'] ),
			'post_id'          => $post_id,
			'analysis_id'      => $analysis_context['analysis_id'],
			'analysis_version' => $analysis_context['version'],
		);

		if ( false === $this->repository->save( $post_id, $payload ) ) {
			return new WP_Error( 'storage_error', __( 'The generated FAQ could not be saved.', 'amanah-seo-ai' ), array( 'status' => 500 ) );
		}

		return array(
			'generation_id' => $generation_id,
			'post_id'       => $post_id,
			'entity'        => array(
				'id'   => absint( $entity['id'] ),
				'name' => sanitize_text_field( $entity['entity_name'] ),
			),
			'version'       => $version,
			'status'        => 'active',
			'faq'           => $validated['faq'],
			'schema'        => $validated['schema'],
			'traceability'  => array(
				'user_id'        => $user_id,
				'provider'       => $payload['provider'],
				'model'          => $payload['model'],
				'analysis_id'    => $analysis_context['analysis_id'],
				'analysis_version' => $analysis_context['version'],
			),
		);
	}
}
