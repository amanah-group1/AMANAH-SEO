<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Portability;

use Amanah\SEOEngine\Application\Portability\PortabilityBackupRepositoryInterface;
use Amanah\SEOEngine\Application\Portability\PortabilityImportValidator;

defined( 'ABSPATH' ) || exit;

final class WordPressPortabilityBackupRepository implements PortabilityBackupRepositoryInterface {
	private $wpdb;
	private $table;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
		$this->table = $wpdb->prefix . 'amanah_seo_portability_backups';
	}

	public function create( string $backupId, array $document, array $metadata = array() ): array {
		$json = wp_json_encode( $document );
		if ( false === $json || strlen( $json ) > PortabilityImportValidator::MAX_DOCUMENT_BYTES ) {
			throw new \RuntimeException( 'Backup document exceeds the portability size limit.' );
		}

		$now = current_time( 'mysql', true );
		$result = $this->wpdb->insert(
			$this->table,
			array(
				'backup_id' => $backupId,
				'status' => 'ready',
				'metadata' => wp_json_encode( $metadata ),
				'document' => $json,
				'created_at' => $now,
				'updated_at' => $now,
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		if ( false === $result ) {
			throw new \RuntimeException( 'Backup storage operation failed.' );
		}

		return $this->find( $backupId );
	}

	public function find( string $backupId ): ?array {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE backup_id=%s LIMIT 1",
				$backupId
			),
			ARRAY_A
		);
		if ( ! is_array( $row ) ) {
			return null;
		}

		$document = json_decode( (string) $row['document'], true );
		$metadata = json_decode( (string) $row['metadata'], true );
		$row['document'] = is_array( $document ) ? $document : array();
		$row['metadata'] = is_array( $metadata ) ? $metadata : array();
		return $row;
	}

	public function markStatus( string $backupId, string $status, array $metadata = array() ): bool {
		$current = $this->find( $backupId );
		if ( null === $current ) {
			return false;
		}
		$merged = array_merge( $current['metadata'], $metadata );
		return 1 === (int) $this->wpdb->update(
			$this->table,
			array(
				'status' => sanitize_key( $status ),
				'metadata' => wp_json_encode( $merged ),
				'updated_at' => current_time( 'mysql', true ),
			),
			array( 'backup_id' => $backupId ),
			array( '%s', '%s', '%s' ),
			array( '%s' )
		);
	}

	public function delete( string $backupId ): bool {
		return 1 === (int) $this->wpdb->delete( $this->table, array( 'backup_id' => $backupId ), array( '%s' ) );
	}
}
