<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version9SEOScan implements MigrationInterface {

	public function getVersion(): int {
		return 10;
	}

	public function getName(): string {
		return 'seo_scan_results_repair';
	}

	public function up(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();
		$table   = $wpdb->prefix . 'amanah_seo_scan_results';

		dbDelta(
			"CREATE TABLE {$table} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				scan_id varchar(64) NOT NULL,
				scan_type varchar(30) NOT NULL,
				status varchar(30) NOT NULL,
				started_at datetime NOT NULL,
				completed_at datetime NULL,
				findings_count int(10) unsigned NOT NULL DEFAULT 0,
				result_data longtext NOT NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY(id),
				UNIQUE KEY scan_id(scan_id),
				KEY scan_type_created_at(scan_type,created_at),
				KEY status(status)
			) {$charset};"
		);
	}
}
