<?php
/**
 * WordPress CORE-006 persistence implementation.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_WordPress_Content_Analysis_Repository implements AMANAH_SEO_AI_Content_Analysis_Repository_Interface {
	private $wpdb;
	private $table;

	public function __construct( $db = null ) {
		global $wpdb;
		$this->wpdb  = $db ? $db : $wpdb;
		$this->table = $this->wpdb->prefix . 'amanah_content_analyses';
	}

	public function next_version( $post_id ) {
		$version = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT MAX(version) FROM {$this->table} WHERE post_id = %d",
				absint( $post_id )
			)
		);
		return max( 1, absint( $version ) + 1 );
	}

	public function save( AMANAH_SEO_AI_Content_Analysis $analysis ) {
		$data = $this->to_row( $analysis );
		$formats = array( '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' );
		$inserted = $this->wpdb->insert( $this->table, $data, $formats );
		return false === $inserted ? false : absint( $this->wpdb->insert_id );
	}

	public function update( AMANAH_SEO_AI_Content_Analysis $analysis ) {
		$data = $this->to_row( $analysis );
		unset( $data['analysis_id'], $data['post_id'], $data['version'], $data['created_at'] );

		$updated = $this->wpdb->update(
			$this->table,
			$data,
			array( 'analysis_id' => $analysis->get_analysis_id() ),
			array( '%s', '%s', '%s', '%s', '%s', '%s' ),
			array( '%s' )
		);
		return false === $updated ? false : true;
	}

	public function find_by_id( $analysis_id ) {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE analysis_id = %s LIMIT 1",
				sanitize_text_field( $analysis_id )
			),
			ARRAY_A
		);
		return $row ? $this->from_row( $row ) : null;
	}

	public function find_latest_by_content( $post_id ) {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE post_id = %d ORDER BY version DESC, id DESC LIMIT 1",
				absint( $post_id )
			),
			ARRAY_A
		);
		return $row ? $this->from_row( $row ) : null;
	}

	private function to_row( AMANAH_SEO_AI_Content_Analysis $analysis ) {
		$reference = $analysis->get_content_reference();
		return array(
			'analysis_id'        => $analysis->get_analysis_id(),
			'post_id'            => isset( $reference['content_id'] ) ? absint( $reference['content_id'] ) : 0,
			'content_version'   => isset( $reference['content_version'] ) ? sanitize_text_field( $reference['content_version'] ) : '',
			'input_context'     => wp_json_encode( $analysis->get_input_context() ),
			'analysis'           => wp_json_encode( $analysis->get_analysis() ),
			'status'             => sanitize_key( $analysis->get_status() ),
			'version'            => $analysis->get_version(),
			'traceability'      => wp_json_encode( $analysis->get_traceability() ),
			'created_at'        => current_time( 'mysql' ),
			'updated_at'        => current_time( 'mysql' ),
		);
	}

	private function from_row( array $row ) {
		return new AMANAH_SEO_AI_Content_Analysis(
			$row['analysis_id'],
			array(
				'content_id'      => absint( $row['post_id'] ),
				'content_version' => sanitize_text_field( $row['content_version'] ),
				'context'         => 'wordpress_post',
			),
			$this->decode_object( $row['input_context'] ),
			$this->decode_object( $row['analysis'] ),
			strtoupper( sanitize_key( $row['status'] ) ),
			absint( $row['version'] ),
			$this->decode_object( $row['traceability'] )
		);
	}

	private function decode_object( $value ) {
		$decoded = json_decode( (string) $value, true );
		return is_array( $decoded ) ? $decoded : array();
	}
}
