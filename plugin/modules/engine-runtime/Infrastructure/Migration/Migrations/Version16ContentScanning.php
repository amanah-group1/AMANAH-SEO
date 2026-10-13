<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;
use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;
defined('ABSPATH') || exit;
final class Version16ContentScanning implements MigrationInterface {
 private $wpdb;
 public function __construct(\wpdb $wpdb){$this->wpdb=$wpdb;}
 public function getVersion():int{return 16;}
 public function getName():string{return 'content_scanning';}
 public function up():void{
  require_once ABSPATH.'wp-admin/includes/upgrade.php';$c=$this->wpdb->get_charset_collate();
  $sql=array(
   "CREATE TABLE {$this->wpdb->prefix}amanah_seo_content_scans (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,keyword_id bigint(20) unsigned NOT NULL,strategy_id bigint(20) unsigned NOT NULL,query varchar(500) NOT NULL,status varchar(30) NOT NULL DEFAULT 'draft',scan_data longtext NOT NULL,version int(11) unsigned NOT NULL DEFAULT 1,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),KEY keyword_id (keyword_id),KEY strategy_id (strategy_id),KEY status (status)) {$c};",
   "CREATE TABLE {$this->wpdb->prefix}amanah_seo_serp_results (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,scan_id bigint(20) unsigned NOT NULL,position int(11) unsigned NOT NULL,page_number int(11) unsigned NOT NULL,title text NOT NULL,url text NOT NULL,domain varchar(255) NOT NULL,snippet text NOT NULL,result_type varchar(30) NOT NULL DEFAULT 'organic',fetch_status varchar(30) NOT NULL DEFAULT 'pending',http_status int(11) NOT NULL DEFAULT 0,error_message text NOT NULL,page_data longtext NOT NULL,analyzed_at datetime NULL,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),KEY scan_id (scan_id),KEY position (position)) {$c};",
   "CREATE TABLE {$this->wpdb->prefix}amanah_seo_scan_analyses (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,scan_id bigint(20) unsigned NOT NULL,analysis_data longtext NOT NULL,version int(11) unsigned NOT NULL DEFAULT 1,status varchar(30) NOT NULL DEFAULT 'draft',created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY (id),KEY scan_id (scan_id)) {$c};"
  );foreach($sql as $statement)dbDelta($statement);
 }
}