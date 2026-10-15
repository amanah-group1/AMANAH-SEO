<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version8ContentPlan implements MigrationInterface {
	public function getVersion(): int { return 8; }
	public function getName(): string { return 'content_plan'; }

	public function up(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();
		$table = $wpdb->prefix . 'amanah_seo_content_plans';

		dbDelta(
			"CREATE TABLE {$table} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				keyword_id bigint(20) unsigned NOT NULL,
				strategy_id bigint(20) unsigned NULL,
				plan_data longtext NOT NULL,
				version int(10) unsigned NOT NULL,
				status varchar(30) NOT NULL,
				created_at datetime NOT NULL,
				updated_at datetime NOT NULL,
				PRIMARY KEY(id),
				KEY keyword_id(keyword_id),
				KEY strategy_id(strategy_id),
				KEY status(status)
			) {$charset};"
		);
	}
}
