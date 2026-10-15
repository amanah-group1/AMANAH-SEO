<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version12ContentRevision implements MigrationInterface {
	public function getVersion(): int {
		return 12;
	}

	public function getName(): string {
		return 'content_revision';
	}

	public function up(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();
		$table = $wpdb->prefix . 'amanah_seo_content_revisions';

		dbDelta(
			"CREATE TABLE {$table} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				content_id bigint(20) unsigned NOT NULL,
				wordpress_post_id bigint(20) unsigned NOT NULL,
				revision_number int(10) unsigned NOT NULL,
				source varchar(30) NOT NULL,
				title text NOT NULL,
				content longtext NOT NULL,
				excerpt text NULL,
				content_hash char(64) NOT NULL,
				wordpress_modified_at datetime NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY(id),
				KEY content_id_revision(content_id,revision_number),
				KEY wordpress_post_id(wordpress_post_id),
				KEY content_hash(content_hash)
			) {$charset};"
		);
	}
}
