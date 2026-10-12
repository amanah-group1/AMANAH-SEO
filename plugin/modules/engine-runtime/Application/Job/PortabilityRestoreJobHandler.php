<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Job;

use Amanah\SEOEngine\Application\Portability\PortabilityService;

defined( 'ABSPATH' ) || exit;

final class PortabilityRestoreJobHandler implements JobHandlerInterface {
	private $portability;

	public function __construct( PortabilityService $portability ) {
		$this->portability = $portability;
	}

	public function getJobType(): string {
		return 'portability_restore';
	}

	public function handle( JobRecord $job ): array {
		$payload = $job->getPayload();
		$backupId = isset( $payload['backup_id'] ) ? (string) $payload['backup_id'] : '';
		$mode = isset( $payload['mode'] ) ? (string) $payload['mode'] : 'merge';
		$resolutions = isset( $payload['resolutions'] ) && is_array( $payload['resolutions'] ) ? $payload['resolutions'] : array();

		if ( '' === $backupId ) {
			throw new JobExecutionException( 'validation', 'Backup ID is required for portability restore.', false );
		}

		$result = $this->portability->restoreBackup( $backupId, $mode, $resolutions );
		return array(
			'processed' => isset( $result['imported_records'] ) ? (int) $result['imported_records'] : 0,
			'total' => isset( $result['total_records'] ) ? (int) $result['total_records'] : 0,
			'result' => $result,
		);
	}
}
