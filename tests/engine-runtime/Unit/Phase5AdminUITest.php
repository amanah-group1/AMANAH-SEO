<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Presentation\Admin\AdminScreenRegistry;
use PHPUnit\Framework\TestCase;

final class Phase5AdminUITest extends TestCase {

	public function test_admin_navigation_contains_required_workflow_screens(): void {
		$screens = AdminScreenRegistry::all();

		foreach ( array( 'dashboard', 'keywords', 'seo-strategy', 'content-plan', 'content-production', 'internal-links', 'cannibalization', 'monitoring-performance', 'settings', 'system' ) as $screen ) {
			$this->assertArrayHasKey( $screen, $screens );
			$this->assertSame( 'manage_options', $screens[ $screen ]['capability'] );
		}
	}

	public function test_dashboard_is_the_root_screen(): void {
		$dashboard = AdminScreenRegistry::get( 'dashboard' );

		$this->assertIsArray( $dashboard );
		$this->assertSame( 'amanah-seo', $dashboard['parent'] );
	}

	public function test_unknown_screen_is_not_registered(): void {
		$this->assertNull( AdminScreenRegistry::get( 'unknown-screen' ) );
	}
}
