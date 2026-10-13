<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version14KeywordArticleStructure implements MigrationInterface {
	public function getVersion(): int { return 14; }
	public function getName(): string { return 'keyword_article_structure'; }

	public function up(): void {
		global $wpdb;

		$table = $wpdb->prefix . 'amanah_seo_keywords';

		$article_type_exists = $wpdb->get_var(
			$wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'article_type' )
		);

		if ( null === $article_type_exists ) {
			$wpdb->query( "ALTER TABLE {$table} ADD article_type varchar(20) NOT NULL DEFAULT 'pillar' AFTER keyword_type" );
		}

		$pillar_exists = $wpdb->get_var(
			$wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'pillar_keyword_id' )
		);

		if ( null === $pillar_exists ) {
			$wpdb->query( "ALTER TABLE {$table} ADD pillar_keyword_id bigint(20) unsigned NULL AFTER article_type" );
		}

		$article_key_exists = $wpdb->get_var(
			$wpdb->prepare( "SHOW INDEX FROM {$table} WHERE Key_name = %s", 'article_type' )
		);
		if ( null === $article_key_exists ) {
			$wpdb->query( "ALTER TABLE {$table} ADD KEY article_type(article_type)" );
		}

		$pillar_key_exists = $wpdb->get_var(
			$wpdb->prepare( "SHOW INDEX FROM {$table} WHERE Key_name = %s", 'pillar_keyword_id' )
		);
		if ( null === $pillar_key_exists ) {
			$wpdb->query( "ALTER TABLE {$table} ADD KEY pillar_keyword_id(pillar_keyword_id)" );
		}
	}
}
