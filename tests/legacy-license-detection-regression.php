<?php
declare( strict_types=1 );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'AUTH_KEY', 'legacy-license-regression-test-key' );
$GLOBALS['amanah_license_test_options'] = array();
$GLOBALS['amanah_license_test_queries'] = array();

function get_option( $key, $default = false ) {
	return $GLOBALS['amanah_license_test_options'][ $key ] ?? $default;
}
function update_option( $key, $value, $autoload = null ) {
	$GLOBALS['amanah_license_test_options'][ $key ] = $value;
	return true;
}
function get_current_user_id() { return 7; }

class AMANAH_SEO_Legacy_License_Test_DB {
	public $prefix = 'wp_';
	public function prepare( $query, ...$args ) {
		foreach ( $args as $arg ) {
			$replacement = is_numeric( $arg ) ? (string) $arg : "'" . str_replace( "'", "\\'", (string) $arg ) . "'";
			$query = preg_replace( '/%[sd]/', $replacement, $query, 1 );
		}
		return $query;
	}
	public function get_var( $query ) {
		$GLOBALS['amanah_license_test_queries'][] = $query;
		if ( false !== strpos( $query, 'SHOW TABLES LIKE' ) ) {
			return 'wp_amanah_license';
		}
		if ( false !== strpos( $query, 'SELECT id FROM wp_amanah_license' ) ) {
			return 19;
		}
		if ( false !== strpos( $query, 'SELECT license_key' ) ) {
			throw new RuntimeException( 'Legacy secret must not be read during detection.' );
		}
		return null;
	}
}
$GLOBALS['wpdb'] = new AMANAH_SEO_Legacy_License_Test_DB();

require_once __DIR__ . '/../plugin/includes/class-amanah-seo-module-licenses.php';

$checks = array();
$checks['Legacy AI record is detected without importing a key'] =
	true === AMANAH_SEO_Module_Licenses::legacy_ai_license_record_exists()
	&& null === AMANAH_SEO_Module_Licenses::get_license( 'ai' )
	&& ! isset( $GLOBALS['amanah_license_test_options']['amanah_seo_module_license_secrets'] );
$checks['Legacy detection queries record ID only and never reads the secret'] =
	2 === count( $GLOBALS['amanah_license_test_queries'] )
	&& false === strpos( implode( "\n", $GLOBALS['amanah_license_test_queries'] ), 'SELECT license_key' );
$checks['Legacy record remains untouched and encrypted Core storage remains absent'] =
	! isset( $GLOBALS['amanah_license_test_options']['amanah_seo_module_license_secrets'] )
	&& true === AMANAH_SEO_Module_Licenses::legacy_ai_license_record_exists();
$checks['Official key can still be explicitly saved and retrieved'] =
	true === AMANAH_SEO_Module_Licenses::set_license( 'ai', 'official-ai-key-test' )
	&& 'official-ai-key-test' === AMANAH_SEO_Module_Licenses::get_license( 'ai' );

$failures = 0;
foreach ( $checks as $label => $passed ) {
	echo ( $passed ? 'PASS: ' : 'FAIL: ' ) . $label . PHP_EOL;
	if ( ! $passed ) {
		$failures++;
	}
}
echo count( $checks ) . ' checks, ' . $failures . ' failures' . PHP_EOL;
exit( 0 === $failures ? 0 : 1 );
