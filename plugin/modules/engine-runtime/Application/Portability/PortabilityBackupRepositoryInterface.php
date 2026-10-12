<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Portability;

defined( 'ABSPATH' ) || exit;

interface PortabilityBackupRepositoryInterface {
	public function create( string $backupId, array $document, array $metadata = array() ): array;
	public function find( string $backupId ): ?array;
	public function markStatus( string $backupId, string $status, array $metadata = array() ): bool;
	public function delete( string $backupId ): bool;
}
