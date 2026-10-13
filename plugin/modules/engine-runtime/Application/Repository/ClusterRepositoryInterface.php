<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\Cluster\Cluster;

defined( 'ABSPATH' ) || exit;

interface ClusterRepositoryInterface {
	public function findById( int $id ): ?Cluster;
	public function findBySlug( string $slug ): ?Cluster;
	public function findAll( int $limit = 100 ): array;
	public function save( Cluster $cluster ): Cluster;
}
