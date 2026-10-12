<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

defined( 'ABSPATH' ) || exit;

interface SEOScanRepositoryInterface {

	public function save( string $scan_type, array $result ): string;

	public function findLatest(): ?array;

	public function findRecent( int $limit = 10 ): array;
}
