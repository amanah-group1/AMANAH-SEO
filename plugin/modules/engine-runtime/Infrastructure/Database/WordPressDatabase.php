<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Database;

defined( 'ABSPATH' ) || exit;

final class WordPressDatabase implements DatabaseInterface {

 private $wpdb;

 public function __construct( \wpdb $wpdb ) {
  $this->wpdb = $wpdb;
 }

 public function query( string $sql ): int {
  $result = $this->wpdb->query( $sql );
  return false === $result ? 0 : (int) $result;
 }

 public function insert( string $table, array $data ): bool {
  return false !== $this->wpdb->insert( $table, $data );
 }

 public function getLastError(): string {
  return (string) $this->wpdb->last_error;
 }

 public function getPrefix(): string {
  return (string) $this->wpdb->prefix;
 }
}
