<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\WordPress\Lifecycle;

use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;

defined( 'ABSPATH' ) || exit;

final class WordPressLifecycle {

	private $configuration;

	public function __construct( Configuration $configuration ) {
		$this->configuration = $configuration;
	}

	public function deactivate(): void {
		wp_clear_scheduled_hook( 'amanah_seo_engine_run_jobs' );
	}

	public function uninstall(): void {
		if ( ! $this->configuration->get( 'delete_data_on_uninstall', false ) ) {
			return;
		}

		global $wpdb;

		$tables = array(
			'amanah_seo_diagnostics',
			'amanah_seo_search_intents',
			'amanah_seo_clusters',
			'amanah_seo_owner_urls',
			'amanah_seo_keywords',
			'amanah_seo_content_strategies',
			'amanah_seo_content_briefs',
			'amanah_seo_page_blueprints',
			'amanah_seo_content',
			'amanah_seo_quality_results',
			'amanah_seo_internal_link_opportunities',
			'amanah_seo_cannibalization',
			'amanah_seo_ai_providers',
			'amanah_seo_ai_models',
			'amanah_seo_ai_tasks',
			'amanah_seo_ai_usage',
			'amanah_seo_jobs',
			'amanah_seo_scan_results',
			'amanah_seo_issues',
			'amanah_seo_recommendations',
			'amanah_seo_portability_operations',
			'amanah_seo_portability_backups',
		);

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$table}" );
		}

		delete_option( 'amanah_seo_engine_configuration' );
		delete_option( 'amanah_seo_engine_database_version' );
		delete_option( 'amanah_seo_ai_credentials' );
		delete_option( 'amanah_seo_engine_google_connections' );
	}
}
