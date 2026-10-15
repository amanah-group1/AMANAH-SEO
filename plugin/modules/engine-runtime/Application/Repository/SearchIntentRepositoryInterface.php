<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\SearchIntent\SearchIntent;

defined( 'ABSPATH' ) || exit;

interface SearchIntentRepositoryInterface {
	public function findById( int $id ): ?SearchIntent;
	public function findBySlug( string $slug ): ?SearchIntent;
	public function findAll( int $limit = 100 ): array;
	public function save( SearchIntent $intent ): SearchIntent;
}
