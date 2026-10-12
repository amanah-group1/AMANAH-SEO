<?php
/**
 * CORE-007 Schema repository.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Schema_Repository {
	public function save( $entity_id, $post_id, $type, array $schema ) {
		global $wpdb;
		$table = $wpdb->prefix . 'amanah_schema';

		$inserted = $wpdb->insert(
			$table,
			array(
				'entity_id'   => absint( $entity_id ),
				'post_id'     => absint( $post_id ),
				'schema_type' => sanitize_text_field( $type ),
				'schema_json' => wp_json_encode( $schema ),
				'status'      => 'active',
			),
			array( '%d', '%d', '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			return false;
		}

		return absint( $wpdb->insert_id );
	}

	public function find_latest( $entity_id, $post_id, $type ) {
		global $wpdb;
		$table = $wpdb->prefix . 'amanah_schema';
		$row   = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE entity_id = %d AND post_id = %d AND schema_type = %s ORDER BY id DESC LIMIT 1",
				absint( $entity_id ),
				absint( $post_id ),
				sanitize_text_field( $type )
			),
			ARRAY_A
		);

		if ( ! $row ) {
			return null;
		}

		$row['schema_json']    = json_decode( $row['schema_json'], true );
		$row['generation_ref'] = 'schema:' . absint( $row['id'] );
		return $row;
	}
}
