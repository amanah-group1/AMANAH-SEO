<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;
use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;
defined('ABSPATH') || exit;
final class Version18ContentIntelligenceFoundation implements MigrationInterface {
 public function getVersion():int{return 18;}
 public function getName():string{return 'content_intelligence_findings_requirements_traceability';}
 public function up():void{global $wpdb;$charset=$wpdb->get_charset_collate();require_once ABSPATH.'wp-admin/includes/upgrade.php';
  $tables=array(
   "CREATE TABLE {$wpdb->prefix}amanah_seo_intelligence_runs (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,keyword_id bigint(20) unsigned NOT NULL,source_type varchar(30) NOT NULL,source_reference varchar(191) NULL,status varchar(30) NOT NULL,version int(10) unsigned NOT NULL DEFAULT 1,started_at datetime NOT NULL,completed_at datetime NULL,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),KEY keyword_id (keyword_id),KEY keyword_status (keyword_id,status),KEY keyword_version (keyword_id,version)) {$charset};",
   "CREATE TABLE {$wpdb->prefix}amanah_seo_intelligence_findings (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,intelligence_run_id bigint(20) unsigned NOT NULL,finding_key varchar(191) NOT NULL,finding_type varchar(50) NOT NULL,subject text NOT NULL,description text NOT NULL,evidence longtext NULL,source varchar(50) NOT NULL,materiality varchar(30) NOT NULL,confidence varchar(30) NOT NULL,status varchar(30) NOT NULL,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY finding_key (finding_key),KEY intelligence_run_id (intelligence_run_id),KEY run_type_status (intelligence_run_id,finding_type,status),KEY materiality_status (materiality,status)) {$charset};",
   "CREATE TABLE {$wpdb->prefix}amanah_seo_content_requirements (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,keyword_id bigint(20) unsigned NOT NULL,finding_id bigint(20) unsigned NOT NULL,requirement_key varchar(191) NOT NULL,requirement_type varchar(50) NOT NULL,statement text NOT NULL,priority varchar(30) NOT NULL,mandatory tinyint(1) NOT NULL DEFAULT 0,verification_rule longtext NOT NULL,status varchar(30) NOT NULL,current_version int(10) unsigned NOT NULL DEFAULT 1,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY requirement_key (requirement_key),KEY keyword_id (keyword_id),KEY finding_id (finding_id),KEY status_priority (status,priority),KEY keyword_status (keyword_id,status)) {$charset};",
   "CREATE TABLE {$wpdb->prefix}amanah_seo_requirement_snapshots (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,requirement_id bigint(20) unsigned NOT NULL,version int(10) unsigned NOT NULL,snapshot_data longtext NOT NULL,created_at datetime NOT NULL,PRIMARY KEY (id),UNIQUE KEY requirement_version (requirement_id,version),KEY requirement_id (requirement_id)) {$charset};"
  );
  foreach($tables as $sql){
   dbDelta($sql);
  }
  $required=array(
   $wpdb->prefix.'amanah_seo_intelligence_runs',
   $wpdb->prefix.'amanah_seo_intelligence_findings',
   $wpdb->prefix.'amanah_seo_content_requirements',
   $wpdb->prefix.'amanah_seo_requirement_snapshots'
  );
  foreach($required as $table){
   $exists=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$table));
   if($exists!==$table){
    throw new \RuntimeException('CP-01 migration failed to create required table: '.$table);
   }
  }
 }
}