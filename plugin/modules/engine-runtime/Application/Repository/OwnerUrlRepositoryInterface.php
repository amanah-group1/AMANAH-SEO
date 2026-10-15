<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\OwnerUrl\OwnerUrl;

defined( 'ABSPATH' ) || exit;

interface OwnerUrlRepositoryInterface {

	public function findById( int $id ): ?OwnerUrl;

	public function findByNormalizedUrl( string $normalized_url ): ?OwnerUrl;

	public function findAll( int $limit = 100 ): array;

	public function save( OwnerUrl $owner_url ): OwnerUrl;
}
