<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\ClusterData;
use Amanah\SEOEngine\Application\Repository\ClusterRepositoryInterface;
use Amanah\SEOEngine\Domain\Cluster\Cluster;

defined( 'ABSPATH' ) || exit;

final class ResolveCluster {
	private $repository;

	public function __construct( ClusterRepositoryInterface $repository ) {
		$this->repository = $repository;
	}

	public function execute( string $slug, string $name, string $description = '' ): ClusterData {
		$slug = sanitize_title( $slug );
		$name = trim( $name );
		$description = trim( $description );

		if ( '' === $slug || '' === $name ) {
			throw new \InvalidArgumentException( 'Cluster identity is required.' );
		}

		$existing = $this->repository->findBySlug( $slug );

		if ( null !== $existing ) {
			return ClusterData::fromDomain( $existing );
		}

		$cluster = new Cluster( $name, $slug, 'active', $description );

		return ClusterData::fromDomain( $this->repository->save( $cluster ) );
	}
}
