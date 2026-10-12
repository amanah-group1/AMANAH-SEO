<?php
/**
 * WordPress runtime smoke test; execute with:
 * wp eval-file tests/wordpress-runtime-smoke.php
 */
defined( 'ABSPATH' ) || exit;

global $wpdb;

$failures = array();
$required_tables = array(
	'amanah_seo_findings',
	'amanah_seo_tasks',
	'amanah_seo_events',
	'amanah_seo_diagnostics',
	'amanah_seo_keywords',
	'amanah_seo_search_intents',
	'amanah_seo_content_plans',
	'amanah_seo_ai_usage',
	'amanah_seo_intelligence_runs',
	'amanah_seo_intelligence_findings',
	'amanah_seo_content_requirements',
	'amanah_seo_requirement_snapshots',
);

foreach ( $required_tables as $suffix ) {
	$table = $wpdb->prefix . $suffix;
	$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
	if ( $found !== $table ) {
		$failures[] = 'Required table missing after activation: ' . $suffix;
	}
}

$engine_version = (int) get_option( 'amanah_seo_engine_database_version', 0 );
if ( 18 !== $engine_version ) {
	$failures[] = 'ENGINE migration version expected 18; found ' . $engine_version;
}

if ( ! class_exists( 'AMANAH_SEO_Module_Licenses' ) ) {
	$failures[] = 'Core module license manager is not loaded.';
} else {
	$test_key = 'engine-runtime-smoke-test-temporary-key';
	if ( ! AMANAH_SEO_Module_Licenses::set_license( 'engine', $test_key ) || AMANAH_SEO_Module_Licenses::get_license( 'engine' ) !== $test_key ) {
		$failures[] = 'Core-managed ENGINE license storage round-trip failed.';
	}
	AMANAH_SEO_Module_Licenses::delete_license( 'engine' );
}

wp_set_current_user( 1 );
do_action( 'admin_menu' );
global $submenu;
$core_submenus = isset( $submenu['amanah-seo'] ) && is_array( $submenu['amanah-seo'] ) ? $submenu['amanah-seo'] : array();
$core_slugs = array_map(
	static function ( $item ) {
		return isset( $item[2] ) ? (string) $item[2] : '';
	},
	$core_submenus
);
if ( ! in_array( 'amanah-seo-engine-dashboard', $core_slugs, true ) ) {
	$failures[] = 'ENGINE dashboard is not registered under the AMANAH SEO Core menu.';
}
$engine_submenus = array_filter(
	$core_slugs,
	static function ( $slug ) {
		return 0 === strpos( $slug, 'amanah-seo-engine-' );
	}
);
if ( count( $engine_submenus ) < 2 ) {
	$failures[] = 'ENGINE workflow submenus are not registered under the Core menu.';
}

if ( $failures ) {
	foreach ( $failures as $failure ) {
		fwrite( STDERR, 'FAIL: ' . $failure . PHP_EOL );
	}
	exit( 1 );
}

echo 'PASS: WordPress activation tables, ENGINE migration version, Core license storage, and Core-owned ENGINE menus.' . PHP_EOL;
