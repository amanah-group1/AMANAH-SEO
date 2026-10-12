<?php
/** Data access for AMANAH SEO-owned records. */
defined( 'ABSPATH' ) || exit;
final class AMANAH_SEO_Store {
 private static function table( $suffix ) { global $wpdb; return $wpdb->prefix . 'amanah_seo_' . $suffix; }
 public static function create_finding( array $d ) {
  global $wpdb; $now = current_time( 'mysql' );
  $ok = $wpdb->insert( self::table('findings'), array('title'=>$d['title'],'description'=>$d['description'],'source'=>$d['source'],'evidence'=>$d['evidence'],'priority'=>$d['priority'],'uncertainty'=>$d['uncertainty'],'status'=>'open','created_by'=>get_current_user_id(),'created_at'=>$now,'updated_at'=>$now), array('%s','%s','%s','%s','%s','%s','%s','%d','%s','%s') );
  if ( false === $ok ) return new WP_Error('finding_save_failed','Finding could not be saved.');
  $id=(int)$wpdb->insert_id; self::event('finding',$id,'created','Finding created; source='.$d['source']); return $id;
 }
 public static function create_task( array $d ) {
  global $wpdb; $now=current_time('mysql');
  $ok=$wpdb->insert(self::table('tasks'),array('finding_id'=>$d['finding_id'],'title'=>$d['title'],'description'=>$d['description'],'owner'=>$d['owner'],'success_criteria'=>$d['success_criteria'],'status'=>$d['status'],'approval_status'=>'not_required','created_by'=>get_current_user_id(),'created_at'=>$now,'updated_at'=>$now),array('%d','%s','%s','%s','%s','%s','%s','%d','%s','%s'));
  if(false===$ok) return new WP_Error('task_save_failed','Task could not be saved.');
  $id=(int)$wpdb->insert_id; self::event('task',$id,'created','Task created; status='.$d['status']);
  if('awaiting_approval'===$d['status']) self::event('task',$id,'approval_requested','Owner approval is required; no website action was executed.');
  return $id;
 }
 public static function update_task_status($id,$status) {
  global $wpdb; $allowed=array('not_started','ready','in_progress','awaiting_approval','blocked','failed','completed','cancelled');
  if(!in_array($status,$allowed,true)) return new WP_Error('invalid_task_status','Invalid task status.');
  $table=self::table('tasks'); $old=$wpdb->get_var($wpdb->prepare("SELECT status FROM {$table} WHERE id = %d",$id));
  if(null===$old) return new WP_Error('task_not_found','Task not found.');
  $ok=$wpdb->update($table,array('status'=>$status,'updated_at'=>current_time('mysql')),array('id'=>$id),array('%s','%s'),array('%d'));
  if(false===$ok) return new WP_Error('task_update_failed','Task status could not be updated.');
  self::event('task',$id,'status_changed','from='.$old.'; to='.$status);
  if('awaiting_approval'===$status) self::event('task',$id,'approval_requested','Explicit approval is required before consequential action.');
  return true;
 }
 public static function findings($limit=50) { global $wpdb; $table=self::table('findings'); return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} ORDER BY updated_at DESC, id DESC LIMIT %d",absint($limit))); }
 public static function tasks($limit=50) { global $wpdb; $table=self::table('tasks'); return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} ORDER BY updated_at DESC, id DESC LIMIT %d",absint($limit))); }
 public static function events($limit=30) { global $wpdb; $table=self::table('events'); return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} ORDER BY id DESC LIMIT %d",absint($limit))); }
 public static function event($type,$id,$event,$detail) { global $wpdb; $wpdb->insert(self::table('events'),array('object_type'=>sanitize_key($type),'object_id'=>absint($id),'event_type'=>sanitize_key($event),'detail'=>sanitize_textarea_field($detail),'user_id'=>get_current_user_id(),'created_at'=>current_time('mysql')),array('%s','%d','%s','%s','%d','%s')); }
}
