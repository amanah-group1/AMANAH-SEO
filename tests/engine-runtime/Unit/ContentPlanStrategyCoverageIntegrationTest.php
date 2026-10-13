<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\Repository\ContentPlanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Application\UseCase\GenerateContentPlan;
use Amanah\SEOEngine\Domain\ContentPlan\ContentPlan;
use Amanah\SEOEngine\Domain\ContentPlan\ContentPlanService;
use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategy;
use PHPUnit\Framework\TestCase;

final class ContentPlanStrategyCoverageIntegrationTest extends TestCase {

	public function test_pillar_strategy_generates_cluster_content_plan_with_support_coverage(): void {
		$strategy = new ContentStrategy(
			5,
			array(
				'keyword' => array(
					'phrase' => 'kopi liong bulan',
					'article_type' => 'pillar',
				),
				'intent_coverage' => array(
					array( 'slug' => 'informational', 'name' => 'Informational' ),
				),
				'support_keywords' => array(
					array( 'id' => 101, 'phrase' => 'kopi liong bulan harga' ),
					array( 'id' => 102, 'phrase' => 'kopi liong bulan asal mana' ),
					array( 'id' => 103, 'phrase' => 'kopi liong bulan asli' ),
				),
				'owner_url' => array(
					'url' => 'http://amanah-seo-engine.test/kopi-liong-bulan/',
				),
			),
			1,
			'draft',
			20
		);

		$strategy_repository = $this->createMock( ContentStrategyRepositoryInterface::class );
		$plan_repository = $this->createMock( ContentPlanRepositoryInterface::class );

		$strategy_repository
			->expects( $this->once() )
			->method( 'findByKeywordId' )
			->with( 5 )
			->willReturn( $strategy );

		$plan_repository
			->expects( $this->once() )
			->method( 'findByKeywordId' )
			->with( 5 )
			->willReturn( null );

		$plan_repository
			->expects( $this->once() )
			->method( 'save' )
			->with( $this->callback(
				static function ( ContentPlan $plan ): bool {
					$data = $plan->getData();

					return 5 === $plan->getKeywordId()
						&& 20 === $plan->getStrategyId()
						&& 'pillar' === $data['article_type']
						&& 3 === $data['support_coverage_count']
						&& 'http://amanah-seo-engine.test/kopi-liong-bulan/' === $data['target_url'];
				}
			) )
			->willReturnCallback(
				static function ( ContentPlan $plan ): ContentPlan {
					return new ContentPlan(
						$plan->getKeywordId(),
						$plan->getData(),
						$plan->getVersion(),
						$plan->getStatus(),
						$plan->getStrategyId(),
						99
					);
				}
			);

		$result = ( new GenerateContentPlan(
			$strategy_repository,
			$plan_repository,
			new ContentPlanService()
		) )->execute( 5 );

		$data = $result->toArray();

		$this->assertSame( 99, $data['id'] );
		$this->assertSame( 5, $data['keyword_id'] );
		$this->assertSame( 20, $data['strategy_id'] );
		$this->assertSame( 3, $data['data']['support_coverage_count'] );
		$this->assertCount( 3, $data['data']['support_keywords'] );
	}

	public function test_existing_pillar_plan_refreshes_version_without_creating_support_plans(): void {
		$strategy = new ContentStrategy(
			5,
			array(
				'keyword' => array(
					'phrase' => 'kopi liong bulan',
					'article_type' => 'pillar',
				),
				'support_keywords' => array(
					array( 'id' => 101, 'phrase' => 'kopi liong bulan harga' ),
				),
				'owner_url' => array(
					'url' => 'http://amanah-seo-engine.test/kopi-liong-bulan/',
				),
			),
			2,
			'draft',
			21
		);

		$existing = new ContentPlan(
			5,
			array( 'article_type' => 'pillar' ),
			4,
			'planned',
			20,
			88
		);

		$strategy_repository = $this->createMock( ContentStrategyRepositoryInterface::class );
		$plan_repository = $this->createMock( ContentPlanRepositoryInterface::class );

		$strategy_repository
			->method( 'findByKeywordId' )
			->with( 5 )
			->willReturn( $strategy );

		$plan_repository
			->method( 'findByKeywordId' )
			->with( 5 )
			->willReturn( $existing );

		$plan_repository
			->expects( $this->once() )
			->method( 'save' )
			->with( $this->callback(
				static function ( ContentPlan $plan ): bool {
					return 5 === $plan->getKeywordId()
						&& 5 === $plan->getVersion()
						&& 21 === $plan->getStrategyId()
						&& 1 === $plan->getData()['support_coverage_count'];
				}
			) )
			->willReturnArgument( 0 );

		$result = ( new GenerateContentPlan(
			$strategy_repository,
			$plan_repository,
			new ContentPlanService()
		) )->execute( 5 );

		$this->assertSame( 5, $result->toArray()['version'] );
		$this->assertSame( 1, $result->toArray()['data']['support_coverage_count'] );
	}
}
