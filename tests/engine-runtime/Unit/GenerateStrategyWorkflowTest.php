<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\DTO\ContentPlanData;
use Amanah\SEOEngine\Application\DTO\ContentStrategyData;
use Amanah\SEOEngine\Application\UseCase\ContentPlanGeneratorInterface;
use Amanah\SEOEngine\Application\UseCase\StrategyGeneratorInterface;
use Amanah\SEOEngine\Application\UseCase\GenerateStrategyWorkflow;
use PHPUnit\Framework\TestCase;

final class GenerateStrategyWorkflowTest extends TestCase {

	public function test_new_page_continues_automatically_to_content_plan(): void {
		$strategy = new ContentStrategyData(
			18,
			7,
			array(
				'owner_url' => array(
					'id' => 6,
					'url' => 'http://amanah-seo-engine.test/kopi-liong-bulan/',
					'wordpress_post_id' => null,
					'post_type' => null,
				),
			),
			4,
			'draft'
		);

		$generate_strategy = $this->createMock( StrategyGeneratorInterface::class );
		$generate_content_plan = $this->createMock( ContentPlanGeneratorInterface::class );

		$generate_strategy->expects( $this->once() )
			->method( 'execute' )
			->with( 7 )
			->willReturn( $strategy );

		$generate_content_plan->expects( $this->once() )
			->method( 'execute' )
			->with( 7 )
			->willReturn( $this->createMock( ContentPlanData::class ) );

		$result = ( new GenerateStrategyWorkflow(
			$generate_strategy,
			$generate_content_plan
		) )->execute( 7 );

		$this->assertSame( 18, $result->id );
		$this->assertSame( 7, $result->keyword_id );
	}

	public function test_existing_page_stops_before_content_plan(): void {
		$strategy = new ContentStrategyData(
			18,
			7,
			array(
				'owner_url' => array(
					'id' => 6,
					'url' => 'http://amanah-seo-engine.test/kopi-liong-bulan/',
					'wordpress_post_id' => 123,
					'post_type' => 'post',
				),
			),
			4,
			'draft'
		);

		$generate_strategy = $this->createMock( GenerateKeywordStrategy::class );
		$generate_content_plan = $this->createMock( GenerateContentPlan::class );

		$generate_strategy->expects( $this->once() )
			->method( 'execute' )
			->with( 7 )
			->willReturn( $strategy );

		$generate_content_plan->expects( $this->never() )->method( 'execute' );

		$result = ( new GenerateStrategyWorkflow(
			$generate_strategy,
			$generate_content_plan
		) )->execute( 7 );

		$this->assertSame( 123, $result->data['owner_url']['wordpress_post_id'] );
	}

	public function test_strategy_failure_does_not_call_content_plan(): void {
		$generate_strategy = $this->createMock( GenerateKeywordStrategy::class );
		$generate_content_plan = $this->createMock( GenerateContentPlan::class );

		$generate_strategy->expects( $this->once() )
			->method( 'execute' )
			->with( 7 )
			->willThrowException( new \RuntimeException( 'Strategy failed.' ) );

		$generate_content_plan->expects( $this->never() )->method( 'execute' );

		$this->expectException( \RuntimeException::class );

		( new GenerateStrategyWorkflow(
			$generate_strategy,
			$generate_content_plan
		) )->execute( 7 );
	}
}
