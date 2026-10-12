<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Database\Schema;
use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version4BackgroundJobs implements MigrationInterface {

	public function getVersion(): int {
		return 4;
	}

	public function getName(): string {
		return 'background_jobs';
	}

	public function up(): void {
		global $wpdb;

		Schema::installBackgroundJobs( $wpdb );
	}
}