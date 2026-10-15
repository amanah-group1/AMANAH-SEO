<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\KeywordData;
use Amanah\SEOEngine\Application\Repository\ClusterRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Domain\Cluster\ClusterService;

defined( 'ABSPATH' ) || exit;

final class AssignKeywordCluster {
	private $keyword_repository;
	private $cluster_repository;
	private $cluster_service;

	public function __construct( KeywordRepositoryInterface $keyword_repository, ClusterRepositoryInterface $cluster_repository, ClusterService $cluster_service ) {
		$this->keyword_repository = $keyword_repository;
		$this->cluster_repository = $cluster_repository;
		$this->cluster_service = $cluster_service;
	}

	public function execute( int $keyword_id, int $cluster_id ): KeywordData {
		$keyword = $this->keyword_repository->findById( $keyword_id );
		$cluster = $this->cluster_repository->findById( $cluster_id );

		if ( null === $keyword ) {
			throw new \RuntimeException( 'Keyword not found.' );
		}

		if ( null === $cluster ) {
			throw new \RuntimeException( 'Cluster not found.' );
		}

		$this->cluster_service->assign( $keyword, $cluster );
		$this->keyword_repository->save( $keyword );

		return KeywordData::fromDomain( $this->keyword_repository->findById( $keyword_id ) );
	}
}
