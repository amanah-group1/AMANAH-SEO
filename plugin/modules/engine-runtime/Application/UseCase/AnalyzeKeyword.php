<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\KeywordAnalysisData;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Domain\KeywordAnalysis\KeywordAnalysisService;

defined( 'ABSPATH' ) || exit;

final class AnalyzeKeyword {

	private $repository;
	private $analysis_service;

	public function __construct(
		KeywordRepositoryInterface $repository,
		KeywordAnalysisService $analysis_service
	) {
		$this->repository = $repository;
		$this->analysis_service = $analysis_service;
	}

	public function execute( int $keyword_id ): ?KeywordAnalysisData {
		$keyword = $this->repository->findById( $keyword_id );

		if ( null === $keyword ) {
			return null;
		}

		return KeywordAnalysisData::fromDomain(
			$this->analysis_service->analyze( $keyword )
		);
	}
}
