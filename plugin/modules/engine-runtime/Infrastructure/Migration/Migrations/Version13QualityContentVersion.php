<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version13QualityContentVersion implements MigrationInterface {
 public function getVersion(): int {
  return 13;
 }

 public function getName(): string {
  return 'quality_content_version';
 }

 public function up(): void {
  global $wpdb;

  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  $charset = $wpdb->get_charset_collate();
  $table = $wpdb->prefix . 'amanah_seo_quality_results';

  dbDelta(
   "CREATE TABLE {$table} (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    content_id bigint(20) unsigned NOT NULL,
    content_version int(10) unsigned NOT NULL DEFAULT 1,
    deterministic_result longtext NULL,
    semantic_result longtext NULL,
    final_result longtext NOT NULL,
    score decimal(6,2) NULL,
    status varchar(30) NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY(id),
    KEY content_id(content_id),
    KEY content_version(content_id,content_version),
    KEY status(status)
   ) {$charset};"
  );
 }
}
