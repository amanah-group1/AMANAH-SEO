<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version11IssueRecommendation implements MigrationInterface {

	public function getVersion(): int { return 11; }

	public function getName(): string { return 'canonical_issues_and_recommendations'; }

	public function up(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();

		$issues = $wpdb->prefix . 'amanah_seo_issues';
		$recommendations = $wpdb->prefix . 'amanah_seo_recommendations';

		dbDelta( "CREATE TABLE {$issues} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			canonical_key varchar(191) NOT NULL,
			source varchar(50) NOT NULL,
			issue_type varchar(100) NOT NULL,
			severity varchar(30) NOT NULL,
			status varchar(30) NOT NULL,
			url text NULL,
			message text NOT NULL,
			evidence longtext NULL,
			first_seen_at datetime NOT NULL,
			last_seen_at datetime NOT NULL,
			resolved_at datetime NULL,
			dismissed_at datetime NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY canonical_key (canonical_key),
			KEY source_status (source,status),
			KEY severity_status (severity,status),
			KEY last_seen_at (last_seen_at)
		) {$charset};" );

		dbDelta( "CREATE TABLE {$recommendations} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			canonical_key varchar(191) NOT NULL,
			issue_id bigint(20) unsigned NOT NULL,
			priority varchar(30) NOT NULL,
			recommendation text NOT NULL,
			target_url text NULL,
			status varchar(30) NOT NULL,
			execution_mode varchar(30) NOT NULL,
			accepted_at datetime NULL,
			completed_at datetime NULL,
			dismissed_at datetime NULL,
			verified_at datetime NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY canonical_key (canonical_key),
			KEY issue_id (issue_id),
			KEY status_priority (status,priority),
			KEY updated_at (updated_at)
		) {$charset};" );
	}
}
