<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Database\Schema;
use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version5Portability implements MigrationInterface {

	public function getVersion(): int {
		return 5;
	}

	public function getName(): string {
		return 'portability';
	}

	public function up(): void {
		global $wpdb;

		Schema::installPortability( $wpdb );
	}
}