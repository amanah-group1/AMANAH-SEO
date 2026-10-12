<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

defined( 'ABSPATH' ) || exit;

final class AdminScreenRegistry {

	public static function all(): array {
		return array(
			'dashboard' => self::screen( 'Dashboard', 'Dashboard', 'dashboard' ),

			'live-scan' => self::screen( 'Live Website', 'Live Website', 'website' ),

			'keywords' => self::screen( 'Keywords', 'Keywords', 'seo' ),
			'seo-strategy' => self::screen( 'Strategy', 'Strategy', 'seo' ),
			'url-mapping' => self::screen( 'URL Mapping', 'URL Mapping', 'seo' ),

			'content-plan' => self::screen( 'Content Plan', 'Content Plan', 'content' ),
			'content-scanning' => self::screen( 'Content Scanning', 'Content Scanning', 'content' ),
			'content-production' => self::screen( 'Content Production', 'Content Production', 'content' ),
			'published-content' => self::screen( 'Published Content', 'Published Content', 'content' ),

			'internal-links' => self::screen( 'Internal Links', 'Internal Links', 'links' ),
			'orphan-pages' => self::screen( 'Orphan Pages', 'Orphan Pages', 'links' ),

			'seo-issues' => self::screen( 'SEO Issues', 'SEO Issues', 'issues' ),
			'cannibalization' => self::screen( 'Cannibalization', 'Cannibalization', 'issues' ),
			'recommendations' => self::screen( 'Recommendations', 'Recommendations', 'issues' ),

			'monitoring-performance' => self::screen( 'Performance', 'Performance', 'monitoring' ),
			'monitoring-keywords' => self::screen( 'Keywords', 'Keywords', 'monitoring' ),
			'monitoring-site-health' => self::screen( 'Site Health', 'Site Health', 'monitoring' ),

			'ai-configuration' => self::screen( 'AI Configuration', 'AI Configuration', 'ai' ),
			'ai-usage' => self::screen( 'Usage', 'Usage', 'ai' ),


			'import-export' => self::screen( 'Import / Export', 'Import / Export', 'administration' ),
			'settings' => self::screen( 'Settings', 'Settings', 'administration' ),
			'license' => self::screen( 'License', 'License', 'administration' ),
			'updates' => self::screen( 'Updates', 'Updates', 'administration' ),
			'system' => self::screen( 'System', 'System', 'administration' ),
		);
	}

	private static function screen( string $title, string $menu_title, string $group ): array {
		return array(
			'title' => \function_exists( '__' ) ? __( $title, 'amanah-seo-engine' ) : $title,
			'menu_title' => \function_exists( '__' ) ? __( $menu_title, 'amanah-seo-engine' ) : $menu_title,
			'parent' => 'amanah-seo-engine',
			'group' => $group,
			'capability' => 'manage_options',
		);
	}

	public static function get( string $screen ): ?array {
		$screens = self::all();

		return $screens[ $screen ] ?? null;
	}
}
