<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version6AiUsageCost implements MigrationInterface {

	private $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function getVersion(): int {
		return 6;
	}

	public function getName(): string {
		return 'ai_usage_cost';
	}

	public function up(): void {
		$table = $this->wpdb->prefix . 'amanah_seo_ai_usage';

		$exists = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SHOW COLUMNS FROM {$table} LIKE %s",
				'pricing_source'
			)
		);

		if ( null === $exists ) {
			$this->wpdb->query(
				"ALTER TABLE {$table} ADD pricing_source varchar(100) NULL AFTER currency"
			);
		}
	}
}