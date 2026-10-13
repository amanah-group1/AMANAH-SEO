<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\Issue\Issue;

defined( 'ABSPATH' ) || exit;

interface IssueRepositoryInterface {

	public function findByCanonicalKey( string $canonical_key ): ?Issue;

	public function findAll( int $limit = 100 ): array;

	public function save( Issue $issue ): Issue;

	public function updateStatus( int $id, string $status, ?string $resolved_at = null, ?string $dismissed_at = null ): bool;
}
