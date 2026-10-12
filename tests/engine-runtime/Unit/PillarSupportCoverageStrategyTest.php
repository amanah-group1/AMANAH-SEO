<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Domain\ContentPlan\ContentPlanService;
use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategy;
use PHPUnit\Framework\TestCase;

final class PillarSupportCoverageStrategyTest extends TestCase {

	public function test_pillar_support_coverage_is_propagated_into_content_plan(): void {
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
				'owner_url' => array( 'url' => 'http://amanah-seo-engine.test/kopi-liong-bulan/' ),
			),
			1,
			'draft',
			20
		);

		$data = ( new ContentPlanService() )->buildData( $strategy );

		$this->assertSame( 'pillar', $data['article_type'] );
		$this->assertCount( 3, $data['support_keywords'] );
		$this->assertSame( 3, $data['support_coverage_count'] );
		$this->assertSame( 'kopi liong bulan harga', $data['support_keywords'][0]['phrase'] );
		$this->assertSame( 'kopi liong bulan asli', $data['support_keywords'][2]['phrase'] );
	}

	public function test_support_article_does_not_inherit_pillar_coverage(): void {
		$strategy = new ContentStrategy(
			7,
			array(
				'keyword' => array(
					'phrase' => 'cara jual iphone bekas',
					'article_type' => 'support',
				),
				'support_keywords' => array(
					array( 'id' => 201, 'phrase' => 'tips jual iphone bekas' ),
				),
			),
			1,
			'draft',
			21
		);

		$data = ( new ContentPlanService() )->buildData( $strategy );

		$this->assertSame( 'support', $data['article_type'] );
		$this->assertSame( array(), $data['support_keywords'] );
		$this->assertSame( 0, $data['support_coverage_count'] );
	}
}
