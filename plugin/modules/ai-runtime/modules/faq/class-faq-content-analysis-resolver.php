<?php
/**
 * CORE-007 Content Analysis context resolver.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_FAQ_Content_Analysis_Resolver {
	private $wpdb;

	public function __construct( $db = null ) {
		global $wpdb;
		$this->wpdb = $db ? $db : $wpdb;
	}

	public function resolve_for_post( $post_id ) {
		$post_id = absint( $post_id );
		$table = $this->wpdb->prefix . 'amanah_content_analyses';

		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT analysis_id, analysis, status, version
				FROM {$table}
				WHERE post_id = %d
				AND status = %s
				ORDER BY id DESC
				LIMIT 1",
				$post_id,
				'completed'
			),
			ARRAY_A
		);

		if ( ! $row || empty( $row['analysis_id'] ) || empty( $row['analysis'] ) ) {
			return new WP_Error(
				'content_analysis_unavailable',
				__( 'A completed Content Analysis is required before FAQ generation.', 'amanah-seo-ai' ),
				array( 'status' => 409 )
			);
		}

		$analysis = json_decode( $row['analysis'], true );
		if ( ! is_array( $analysis ) || empty( $analysis ) ) {
			return new WP_Error(
				'content_analysis_unavailable',
				__( 'The latest Content Analysis result is not valid for FAQ generation.', 'amanah-seo-ai' ),
				array( 'status' => 409 )
			);
		}

		return array(
			'analysis_id' => sanitize_text_field( $row['analysis_id'] ),
			'version'     => absint( $row['version'] ),
			'analysis'    => $analysis,
		);
	}
}
