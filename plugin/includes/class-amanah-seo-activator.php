<?php
/** Creates only AMANAH SEO-owned tables; never runs legacy module installers. */
defined( 'ABSPATH' ) || exit;
final class AMANAH_SEO_Activator {
 public static function activate() {
  self::grant_admin_capability();
  global $wpdb;
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  $collate = $wpdb->get_charset_collate();
  $findings = $wpdb->prefix . 'amanah_seo_findings';
  $tasks = $wpdb->prefix . 'amanah_seo_tasks';
  $events = $wpdb->prefix . 'amanah_seo_events';
  dbDelta("CREATE TABLE {$findings} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,title varchar(255) NOT NULL,description text NOT NULL,source varchar(100) NOT NULL DEFAULT 'manual',evidence text NOT NULL,priority varchar(20) NOT NULL DEFAULT 'medium',uncertainty varchar(20) NOT NULL DEFAULT 'medium',status varchar(30) NOT NULL DEFAULT 'open',created_by bigint(20) unsigned NOT NULL DEFAULT 0,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY  (id),KEY status (status),KEY priority (priority)) {$collate};");
  dbDelta("CREATE TABLE {$tasks} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,finding_id bigint(20) unsigned NOT NULL DEFAULT 0,title varchar(255) NOT NULL,description text NOT NULL,owner varchar(190) NOT NULL DEFAULT '',success_criteria text NOT NULL,due_date date NULL,dependency_task_id bigint(20) unsigned NOT NULL DEFAULT 0,status varchar(30) NOT NULL DEFAULT 'not_started',approval_status varchar(30) NOT NULL DEFAULT 'not_required',completion_evidence text NULL,created_by bigint(20) unsigned NOT NULL DEFAULT 0,created_at datetime NOT NULL,updated_at datetime NOT NULL,PRIMARY KEY  (id),KEY finding_id (finding_id),KEY dependency_task_id (dependency_task_id),KEY status (status)) {$collate};");
  dbDelta("CREATE TABLE {$events} (id bigint(20) unsigned NOT NULL AUTO_INCREMENT,object_type varchar(30) NOT NULL,object_id bigint(20) unsigned NOT NULL DEFAULT 0,event_type varchar(60) NOT NULL,detail text NOT NULL,user_id bigint(20) unsigned NOT NULL DEFAULT 0,created_at datetime NOT NULL,PRIMARY KEY  (id),KEY object_lookup (object_type,object_id),KEY created_at (created_at)) {$collate};");
  update_option( 'amanah_seo_schema_version', '1.2.0', false );
  if ( ! get_option( 'amanah_seo_installed_at' ) ) update_option( 'amanah_seo_installed_at', current_time( 'mysql' ), false );
 }
 public static function grant_admin_capability() {
  $role = get_role( 'administrator' );
  if ( ! $role ) {
   return;
  }
  // Core owns the page-access boundary. Keep legacy entity action caps so
  // existing save/delete/analyze permission checks remain explicit.
  foreach ( array( 'manage_amanah_seo', 'amanah_view_entity', 'amanah_manage_entity' ) as $capability ) {
   if ( ! $role->has_cap( $capability ) ) {
    $role->add_cap( $capability );
   }
  }
 }
 public static function maybe_upgrade() {
  self::grant_admin_capability();
  if ( '1.2.0' !== get_option( 'amanah_seo_schema_version', '' ) ) {
   self::activate();
  }
 }
}
