<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Database\Schema;
use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version2CoreDomain implements MigrationInterface {
	private $wpdb;
	public function __construct( \wpdb $wpdb ) { $this->wpdb = $wpdb; }
	public function getVersion(): int { return 2; }
	public function getName(): string { return 'core_domain'; }
	public function up(): void { Schema::installCoreDomain( $this->wpdb ); }
}