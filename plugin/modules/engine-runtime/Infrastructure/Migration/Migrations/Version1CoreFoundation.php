<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Database\Schema;
use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version1CoreFoundation implements MigrationInterface {

	private $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function getVersion(): int {
		return 1;
	}

	public function getName(): string {
		return 'core_foundation';
	}

	public function up(): void {
		Schema::install( $this->wpdb );
	}
}
