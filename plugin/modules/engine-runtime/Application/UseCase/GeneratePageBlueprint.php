<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\PageBlueprintData;
use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Service\PageBlueprintBuilder;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprintService;

defined( 'ABSPATH' ) || exit;

final class GeneratePageBlueprint {

	private $brief_repository;
	private $blueprint_repository;
	private $blueprint_service;
	private $builder;

	public function __construct(
		ContentBriefRepositoryInterface $brief_repository,
		PageBlueprintRepositoryInterface $blueprint_repository,
		PageBlueprintService $blueprint_service,
		PageBlueprintBuilder $builder
	) {
		$this->brief_repository    = $brief_repository;
		$this->blueprint_repository = $blueprint_repository;
		$this->blueprint_service   = $blueprint_service;
		$this->builder              = $builder;
	}

	public function execute( int $keyword_id ): PageBlueprintData {
		$brief = $this->brief_repository->findByKeywordId( $keyword_id );

		if ( null === $brief ) {
			throw new \RuntimeException( 'Content Brief not found. Generate Content Brief before creating a Page Blueprint.' );
		}

		$existing = $this->blueprint_repository->findByKeywordId( $keyword_id );
		$version = null !== $existing ? $existing->getVersion() + 1 : 1;
		$data = $this->builder->build( $brief, $keyword_id );

		$blueprint = new PageBlueprint(
			$keyword_id,
			$data,
			$version,
			'draft',
			$brief->getId()
		);

		$this->blueprint_service->validate( $blueprint, $brief );

		return PageBlueprintData::fromDomain( $this->blueprint_repository->save( $blueprint ) );
	}
}
