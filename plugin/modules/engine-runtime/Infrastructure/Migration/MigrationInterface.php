<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration;

defined( 'ABSPATH' ) || exit;

interface MigrationInterface {

	public function getVersion(): int;

	public function getName(): string;

	public function up(): void;
}
