<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_AI_Settings {
	private $table;
	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'amanah_settings';
	}
	public function get( $key, $user_id = null ) {
		global $wpdb;
		$user_id = null === $user_id ? get_current_user_id() : absint( $user_id );
		$sql = "SELECT setting_value FROM {$this->table} WHERE setting_key = %s AND (user_id = %d OR user_id IS NULL) ORDER BY user_id DESC LIMIT 1";
		return $wpdb->get_var( $wpdb->prepare( $sql, sanitize_key( $key ), $user_id ) );
	}
	public function set( $key, $value, $user_id = null ) {
		global $wpdb;
		$user_id = null === $user_id ? get_current_user_id() : absint( $user_id );
		$key = sanitize_key( $key );
		$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->table} WHERE setting_key = %s AND user_id = %d LIMIT 1", $key, $user_id ) );
		if ( $id ) {
			return false !== $wpdb->update( $this->table, array( 'setting_value' => (string) $value, 'updated_at' => current_time( 'mysql', true ) ), array( 'id' => (int) $id ), array( '%s', '%s' ), array( '%d' ) );
		}
		return false !== $wpdb->insert( $this->table, array( 'user_id' => $user_id, 'setting_key' => $key, 'setting_value' => (string) $value ), array( '%d', '%s', '%s' ) );
	}
}
