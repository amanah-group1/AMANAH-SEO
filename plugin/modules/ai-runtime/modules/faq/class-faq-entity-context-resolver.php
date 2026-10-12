<?php
/**
 * CORE-007 Entity Context Resolution boundary.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_FAQ_Entity_Context_Resolver {
	private $wpdb;

	public function __construct( $db = null ) {
		global $wpdb;
		$this->wpdb = $db ? $db : $wpdb;
	}

	public function resolve_for_post( $post_id, $user_id ) {
		$post_id = absint( $post_id );
		$user_id = absint( $user_id );
		$table = $this->wpdb->prefix . 'amanah_content_analyses';
		$entity_table = $this->wpdb->prefix . 'amanah_entities';

		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT ca.input_context
				FROM {$table} ca
				WHERE ca.post_id = %d
				AND ca.status = %s
				ORDER BY ca.id DESC
				LIMIT 1",
				$post_id,
				'completed'
			),
			ARRAY_A
		);

		if ( ! $row || empty( $row['input_context'] ) ) {
			return new WP_Error( 'entity_context_unavailable', __( 'A valid Entity Context is required before FAQ generation.', 'amanah-seo-ai' ), array( 'status' => 409 ) );
		}

		$input_context = json_decode( $row['input_context'], true );
		$entity_name = is_array( $input_context ) && isset( $input_context['entity'] )
			? trim( (string) $input_context['entity'] )
			: '';

		if ( '' === $entity_name ) {
			return new WP_Error( 'entity_context_unavailable', __( 'A valid Entity Context is required before FAQ generation.', 'amanah-seo-ai' ), array( 'status' => 409 ) );
		}

		$entity = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT *
				FROM {$entity_table}
				WHERE entity_name = %s
				AND status = %s
				ORDER BY id DESC
				LIMIT 1",
				$entity_name,
				'active'
			),
			ARRAY_A
		);

		if ( ! $entity ) {
			return new WP_Error( 'entity_context_unavailable', __( 'The resolved Entity Context is no longer available.', 'amanah-seo-ai' ), array( 'status' => 409 ) );
		}

		if ( isset( $entity['user_id'] ) && absint( $entity['user_id'] ) !== $user_id && ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'permission_denied', __( 'You are not allowed to use the resolved Entity Context.', 'amanah-seo-ai' ), array( 'status' => 403 ) );
		}

		return $entity;
	}
}
