<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class ContentPlanBriefTransitionTest extends TestCase {

	public function test_content_plan_detail_exposes_generate_brief_transition(): void {
		$source = (string) file_get_contents( dirname( __DIR__, 3 ) . '/plugin/modules/engine-runtime/Presentation/Admin/ContentPlanAdmin.php' );

		self::assertStringContainsString( "value=\"generate_brief\"", $source );
		self::assertStringContainsString( "wp_nonce_field('amanah_generate_content_brief','amanah_content_brief_nonce')", $source );
		self::assertStringContainsString( 'Generate Content Brief', $source );
		self::assertStringContainsString( "if ( 'generate_brief' === \$action )", $source );
	}

	public function test_created_brief_exposes_content_production_transition(): void {
		$source = (string) file_get_contents( dirname( __DIR__, 3 ) . '/plugin/modules/engine-runtime/Presentation/Admin/ContentPlanAdmin.php' );

		self::assertStringContainsString( 'findByKeywordId( $plan->getKeywordId() )', $source );
		self::assertStringContainsString( 'Open Content Production', $source );
		self::assertStringContainsString( 'amanah-seo-engine-content-production', $source );
	}
}
