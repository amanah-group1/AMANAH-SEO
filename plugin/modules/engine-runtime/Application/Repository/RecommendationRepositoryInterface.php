<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\Recommendation\Recommendation;

defined( 'ABSPATH' ) || exit;

interface RecommendationRepositoryInterface {

	public function findByCanonicalKey( string $canonical_key ): ?Recommendation;

	public function findAll( int $limit = 100 ): array;

	public function save( Recommendation $recommendation ): Recommendation;

	public function updateStatus( int $id, string $status ): bool;
}
