<?php

declare( strict_types=1 );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'AUTH_KEY', 'unit-test-auth-key-that-is-not-a-real-secret' );

$GLOBALS['amanah_license_test_options'] = array();

function get_option( $key, $default = false ) {
	return $GLOBALS['amanah_license_test_options'][ $key ] ?? $default;
}
function update_option( $key, $value, $autoload = null ) {
	if ( array_key_exists( $key, $GLOBALS['amanah_license_test_options'] ) && $GLOBALS['amanah_license_test_options'][ $key ] === $value ) {
		return false;
	}
	$GLOBALS['amanah_license_test_options'][ $key ] = $value;
	return true;
}
function delete_option( $key ) {
	unset( $GLOBALS['amanah_license_test_options'][ $key ] );
	return true;
}

require_once __DIR__ . '/../plugin/includes/class-amanah-seo-module-licenses.php';

$checks = array();
$checks['AI module license stores and decrypts'] = AMANAH_SEO_Module_Licenses::set_license( 'ai', 'ai-license-unit-test-123' ) && 'ai-license-unit-test-123' === AMANAH_SEO_Module_Licenses::get_license( 'ai' );
$checks['ENGINE module license stores and decrypts independently'] = AMANAH_SEO_Module_Licenses::set_license( 'engine', 'engine-license-unit-test-456' ) && 'engine-license-unit-test-456' === AMANAH_SEO_Module_Licenses::get_license( 'engine' );
$checks['Module license option does not contain plaintext keys'] = false === strpos( serialize( get_option( 'amanah_seo_module_license_secrets', array() ) ), 'ai-license-unit-test-123' ) && false === strpos( serialize( get_option( 'amanah_seo_module_license_secrets', array() ) ), 'engine-license-unit-test-456' );
$checks['Invalid module and empty key are rejected'] = false === AMANAH_SEO_Module_Licenses::set_license( 'other', 'bad-key' ) && false === AMANAH_SEO_Module_Licenses::set_license( 'engine', '' );
$checks['Deleting ENGINE key preserves AI key'] = AMANAH_SEO_Module_Licenses::delete_license( 'engine' ) && null === AMANAH_SEO_Module_Licenses::get_license( 'engine' ) && 'ai-license-unit-test-123' === AMANAH_SEO_Module_Licenses::get_license( 'ai' );

$failures = 0;
foreach ( $checks as $label => $passed ) {
	echo ( $passed ? 'PASS: ' : 'FAIL: ' ) . $label . PHP_EOL;
	if ( ! $passed ) {
		$failures++;
	}
}
echo count( $checks ) . ' checks, ' . $failures . ' failures' . PHP_EOL;
exit( 0 === $failures ? 0 : 1 );
