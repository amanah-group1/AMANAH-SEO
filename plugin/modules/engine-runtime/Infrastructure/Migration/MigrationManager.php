<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration;

use Amanah\SEOEngine\Application\Port\MigrationRunnerInterface;
use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;
use Amanah\SEOEngine\Infrastructure\Database\Schema;

defined( 'ABSPATH' ) || exit;

final class MigrationManager implements MigrationRunnerInterface {
	private const OPTION_KEY = 'amanah_seo_engine_database_version';
	private $configuration; private $migrations = array();
	public function __construct( Configuration $configuration ) { $this->configuration = $configuration; }
	public function register( MigrationInterface $migration ): void { $this->migrations[ $migration->getVersion() ] = $migration; }
	public function getCurrentVersion(): int { return (int) get_option( self::OPTION_KEY, 0 ); }
	public function migrate(): void {
		$current = $this->getCurrentVersion();
		$pending = $this->migrations;
		ksort( $pending, SORT_NUMERIC );

		foreach ( $pending as $version => $migration ) {
			if ( $version <= $current ) {
				continue;
			}
			$migration->up();
			update_option( self::OPTION_KEY, (int) $version, false );
		}

		$this->repairKnownSchemaDrift();

		if ( 1 !== $this->configuration->getVersion() ) {
			$this->configuration->set( 'configuration_version', 1 );
		}
	}

	private function repairKnownSchemaDrift(): void {
		global $wpdb;

		if ( isset( $this->migrations[11] ) ) {
			$issues_table = $wpdb->prefix . 'amanah_seo_issues';
			$recommendations_table = $wpdb->prefix . 'amanah_seo_recommendations';
			$issues_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $issues_table ) ) === $issues_table;
			$recommendations_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $recommendations_table ) ) === $recommendations_table;

			if ( ! $issues_exists || ! $recommendations_exists ) {
				$this->migrations[11]->up();
				update_option( self::OPTION_KEY, 11, false );
			}
		}
		if ( isset( $this->migrations[18] ) && $this->getCurrentVersion() >= 18 ) {
			$required_tables = array(
				$wpdb->prefix . 'amanah_seo_intelligence_runs',
				$wpdb->prefix . 'amanah_seo_intelligence_findings',
				$wpdb->prefix . 'amanah_seo_content_requirements',
				$wpdb->prefix . 'amanah_seo_requirement_snapshots',
			);

			foreach ( $required_tables as $table ) {
				$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
				if ( ! $exists ) {
					$this->migrations[18]->up();
					break;
				}
			}
		}

		}
}