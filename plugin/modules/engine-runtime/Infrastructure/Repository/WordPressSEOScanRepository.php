<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\SEOScanRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class WordPressSEOScanRepository implements SEOScanRepositoryInterface {

	private $wpdb;
	private $table;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'amanah_seo_scan_results';
	}

	public function save( string $scan_type, array $result ): string {
		$scan_id = wp_generate_uuid4();
		$now     = current_time( 'mysql', true );

		$inserted = $this->wpdb->insert(
			$this->table,
			array(
				'scan_id'       => $scan_id,
				'scan_type'     => sanitize_key( $scan_type ),
				'status'        => 'completed',
				'started_at'    => $now,
				'completed_at'  => $now,
				'findings_count'=> isset( $result['findings_count'] ) ? (int) $result['findings_count'] : 0,
				'result_data'   => wp_json_encode( $result ),
				'created_at'    => $now,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
		);

		if ( false === $inserted ) {
			throw new \RuntimeException( 'SEO scan result could not be stored.' );
		}

		return $scan_id;
	}

	public function findRecent( int $limit = 10 ): array {
		$limit = max( 1, min( 20, $limit ) );
		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare( "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d", $limit ),
			ARRAY_A
		);
		$history = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			$result = json_decode( (string) $row['result_data'], true );
			$history[] = array(
				'scan_id' => (string) $row['scan_id'],
				'scan_type' => (string) $row['scan_type'],
				'status' => (string) $row['status'],
				'completed_at' => (string) $row['completed_at'],
				'findings_count' => (int) $row['findings_count'],
				'result' => is_array( $result ) ? $result : array(),
			);
		}
		return $history;
	}

	public function findLatest(): ?array {
		$row = $this->wpdb->get_row(
			"SELECT * FROM {$this->table} ORDER BY id DESC LIMIT 1",
			ARRAY_A
		);

		if ( ! is_array( $row ) ) {
			return null;
		}

		$result = json_decode( (string) $row['result_data'], true );

		return array(
			'scan_id'        => (string) $row['scan_id'],
			'scan_type'      => (string) $row['scan_type'],
			'status'         => (string) $row['status'],
			'started_at'     => (string) $row['started_at'],
			'completed_at'   => (string) $row['completed_at'],
			'findings_count' => (int) $row['findings_count'],
			'result'         => is_array( $result ) ? $result : array(),
		);
	}
}
