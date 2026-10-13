<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\ContentPlanData;
use Amanah\SEOEngine\Application\Repository\ContentPlanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentPlan\ContentPlan;
use Amanah\SEOEngine\Domain\ContentPlan\ContentPlanService;

defined( 'ABSPATH' ) || exit;

final class GenerateContentPlan implements ContentPlanGeneratorInterface {

	private $strategy_repository;
	private $plan_repository;
	private $plan_service;

	public function __construct(
		ContentStrategyRepositoryInterface $strategy_repository,
		ContentPlanRepositoryInterface $plan_repository,
		ContentPlanService $plan_service
	) {
		$this->strategy_repository = $strategy_repository;
		$this->plan_repository    = $plan_repository;
		$this->plan_service       = $plan_service;
	}

	public function execute( int $keyword_id ): ContentPlanData {
		$strategy = $this->strategy_repository->findByKeywordId( $keyword_id );

		if ( null === $strategy ) {
			throw new \RuntimeException( 'SEO strategy not found. Generate strategy before creating a Content Plan.' );
		}

		$existing = $this->plan_repository->findByKeywordId( $keyword_id );
		$version  = null !== $existing ? $existing->getVersion() + 1 : 1;

		$data = $this->plan_service->buildData( $strategy );

		$plan = new ContentPlan(
			$keyword_id,
			$data,
			$version,
			'planned',
			$strategy->getId()
		);

		$this->plan_service->validate( $plan, $strategy );

		return ContentPlanData::fromDomain( $this->plan_repository->save( $plan ) );
	}
}
