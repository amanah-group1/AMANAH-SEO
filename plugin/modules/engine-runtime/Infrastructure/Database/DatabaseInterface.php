<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Database;

defined( 'ABSPATH' ) || exit;

interface DatabaseInterface {

 public function query( string $sql ): int;

 public function insert( string $table, array $data ): bool;

 public function getLastError(): string;

 public function getPrefix(): string;
}
