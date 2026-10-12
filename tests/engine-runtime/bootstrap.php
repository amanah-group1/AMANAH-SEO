<?php

declare( strict_types=1 );

defined( 'ABSPATH' ) || define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );

if ( ! class_exists( 'wpdb' ) ) {
	class wpdb {
		public $prefix = 'wp_';
		public $insert_id = 0;
		public $last_error = '';
		public function prepare( $query, ...$args ) { return $query; }
		public function get_var( $query ) { return null; }
		public function insert( $table, $data ) { return 1; }
		public function update( $table, $data, $where ) { return 1; }
		public function query( $query ) { return 1; }
	}
}

if ( ! function_exists( 'current_time' ) ) {
	function current_time( $type, $gmt = false ) { return '2026-01-01 00:00:00'; }
}
if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $value ) { return json_encode( $value ); }
}

$root = dirname( __DIR__, 2 );
require_once $root . '/plugin/modules/engine-runtime/Core/Autoload/ClassLoader.php';
$autoloader = new \Amanah\SEOEngine\Core\Autoload\ClassLoader(
	'Amanah\\SEOEngine\\',
	$root . '/plugin/modules/engine-runtime'
);
$autoloader->register();

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		return strtolower( preg_replace( '/[^a-z0-9_\\-]/i', '', (string) $key ) );
	}
}
if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $text ) {
		return strip_tags( (string) $text );
	}
}
