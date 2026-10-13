<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\KeywordRelationshipData;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Domain\KeywordAnalysis\KeywordAnalysisService;
use Amanah\SEOEngine\Domain\KeywordRelationship\KeywordRelationshipService;

defined( 'ABSPATH' ) || exit;

final class AnalyzeKeywordRelationships {

	private $repository;
	private $analysis_service;
	private $relationship_service;

	public function __construct(
		KeywordRepositoryInterface $repository,
		KeywordAnalysisService $analysis_service,
		KeywordRelationshipService $relationship_service
	) {
		$this->repository          = $repository;
		$this->analysis_service   = $analysis_service;
		$this->relationship_service = $relationship_service;
	}

	public function execute( int $keyword_id, int $limit = 100 ): array {
		$source = $this->repository->findById( $keyword_id );

		if ( null === $source ) {
			return array();
		}

		$source_analysis = $this->analysis_service->analyze( $source );
		$keywords        = $this->repository->findAll( $limit );
		$relationships   = array();

		foreach ( $keywords as $target ) {
			if ( null === $target->getId() || $target->getId() === $source->getId() ) {
				continue;
			}

			$relationship = $this->relationship_service->compare(
				(int) $source->getId(),
				$source_analysis,
				(int) $target->getId(),
				$this->analysis_service->analyze( $target )
			);

			$relationships[] = KeywordRelationshipData::fromDomain( $relationship );
		}

		return $relationships;
	}
}
