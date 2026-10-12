<?php
declare( strict_types=1 );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'AUTH_KEY', 'ai-license-regression-test-key' );

$GLOBALS['amanah_license_test_options'] = array();
$GLOBALS['amanah_license_mock_response'] = null;
$GLOBALS['amanah_license_mock_error'] = false;
$GLOBALS['amanah_license_last_request'] = null;

class AMANAH_SEO_Test_WP_Error {}
function is_wp_error( $value ) { return $value instanceof AMANAH_SEO_Test_WP_Error; }
function get_option( $key, $default = false ) { return $GLOBALS['amanah_license_test_options'][ $key ] ?? $default; }
function update_option( $key, $value, $autoload = null ) {
	if ( array_key_exists( $key, $GLOBALS['amanah_license_test_options'] ) && $GLOBALS['amanah_license_test_options'][ $key ] === $value ) { return false; }
	$GLOBALS['amanah_license_test_options'][ $key ] = $value;
	return true;
}
function delete_option( $key ) { unset( $GLOBALS['amanah_license_test_options'][ $key ] ); return true; }
function sanitize_key( $value ) { return strtolower( preg_replace( '/[^a-z0-9_\\-]/', '', (string) $value ) ); }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function wp_remote_post( $url, $args = array() ) {
	$GLOBALS['amanah_license_last_request'] = array( 'url' => $url, 'args' => $args );
	return $GLOBALS['amanah_license_mock_error'] ? new AMANAH_SEO_Test_WP_Error() : $GLOBALS['amanah_license_mock_response'];
}
function wp_remote_retrieve_response_code( $response ) { return $response['response']['code'] ?? 0; }
function wp_remote_retrieve_body( $response ) { return $response['body'] ?? ''; }

require_once __DIR__ . '/../plugin/includes/class-amanah-seo-module-licenses.php';
require_once __DIR__ . '/../plugin/modules/ai-runtime/modules/ai/class-ai-exception.php';
require_once __DIR__ . '/../plugin/modules/ai-runtime/modules/ai/class-ai-client.php';

$activate = new ReflectionMethod( 'AMANAH_SEO_Module_Licenses', 'activate_ai_license' );
$activate->setAccessible( true );
$valid_response = static function ( $status = 'active', $entitlements = array( 'content_analyzer' ), $product = 'amanah-seo-ai' ) {
	return array(
		'response' => array( 'code' => 200 ),
		'body' => json_encode( array( 'product' => $product, 'status' => $status, 'entitlements' => $entitlements, 'validated_at' => '2026-10-12T00:00:00Z' ) ),
	);
};
$checks = array();

$GLOBALS['amanah_license_mock_response'] = $valid_response();
$GLOBALS['amanah_license_mock_error'] = false;
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Valid service response activates the AI product'] = 'active' === $state['status'] && in_array( 'content_analyzer', $state['entitlements'], true );
$payload = json_decode( $GLOBALS['amanah_license_last_request']['args']['body'] ?? '', true );
$checks['Activation sends expected product and supplied key to the documented service host'] = 'https://api.amanahseoai.my.id/activate' === $GLOBALS['amanah_license_last_request']['url'] && 'amanah-seo-ai' === ( $payload['product'] ?? '' ) && 'ai-license-regression-key' === ( $payload['license_key'] ?? '' );

$GLOBALS['amanah_license_mock_error'] = true;
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Network failure fails closed without exposing credentials'] = 'unknown' === $state['status'] && 'service_unavailable' === $state['reason'] && false === strpos( serialize( $state ), 'ai-license-regression-key' );
$GLOBALS['amanah_license_mock_error'] = false;

$GLOBALS['amanah_license_mock_response'] = $valid_response( 'invalid' );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Invalid license status is not treated as active'] = 'invalid' === $state['status'];

$GLOBALS['amanah_license_test_options']['amanah_seo_license_api_endpoint'] = 'https://attacker.example';
$previous_request = $GLOBALS['amanah_license_last_request'];
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Untrusted endpoint is rejected before sending the license key'] = 'unknown' === $state['status'] && 'invalid_endpoint' === $state['reason'] && $previous_request === $GLOBALS['amanah_license_last_request'];
unset( $GLOBALS['amanah_license_test_options']['amanah_seo_license_api_endpoint'] );

$GLOBALS['amanah_license_mock_response'] = $valid_response( 'active', array(), 'amanah-seo-ai' );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Active response without entitlements is rejected'] = 'unknown' === $state['status'] && 'invalid_response' === $state['reason'];

$GLOBALS['amanah_license_mock_response'] = $valid_response( 'active', array( 'content_analyzer' ), 'another-product' );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Response for a different product is rejected'] = 'unknown' === $state['status'] && 'invalid_response' === $state['reason'];

$GLOBALS['amanah_license_mock_response'] = array( 'response' => array( 'code' => 503 ), 'body' => '{}' );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Non-success HTTP response fails closed'] = 'unknown' === $state['status'];

$GLOBALS['amanah_license_mock_response'] = $valid_response();
$GLOBALS['amanah_license_test_options']['amanah_seo_module_license_statuses'] = array( 'ai' => array( 'status' => 'active', 'entitlements' => array( 'content_analyzer' ) ) );
$checks['Active entitlement grants the matching AI capability'] = AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', 'content_analyzer' );
$checks['Active entitlement does not grant a different capability'] = ! AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', 'schema_generation' );
$GLOBALS['amanah_license_test_options']['amanah_seo_module_license_statuses']['ai']['status'] = 'expired';
$checks['Expired license denies previously stored entitlement'] = ! AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', 'content_analyzer' );
$GLOBALS['amanah_license_test_options']['amanah_seo_module_license_statuses']['ai']['status'] = 'active';
unset( $GLOBALS['amanah_license_test_options']['amanah_seo_module_license_statuses']['ai']['entitlements'] );
$checks['Malformed stored status denies entitlement'] = ! AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', 'content_analyzer' );

$client = ( new ReflectionClass( 'AMANAH_SEO_AI_AI_Client' ) )->newInstanceWithoutConstructor();
try {
	$client->generate( 'content_analysis', array(), 1 );
	$checks['AI execution is blocked when entitlement is absent'] = false;
} catch ( AMANAH_SEO_AI_AI_Exception $exception ) {
	$checks['AI execution is blocked when entitlement is absent'] = 403 === $exception->getCode() && 'ai_entitlement_missing' === $exception->get_diagnostic_cause();
}

$client_source = file_get_contents( __DIR__ . '/../plugin/modules/ai-runtime/modules/ai/class-ai-client.php' );
$checks['All AI execution passes through the central entitlement gate'] = strpos( $client_source, 'AMANAH_SEO_Module_Licenses::has_entitlement' ) !== false && strpos( $client_source, 'required_entitlement_for_task' ) !== false;
$core_source = file_get_contents( __DIR__ . '/../plugin/includes/class-amanah-seo-module-licenses.php' );
$checks['AI API key remains encrypted in Core storage'] = AMANAH_SEO_Module_Licenses::set_license( 'ai', 'ai-license-regression-key' ) && false === strpos( serialize( get_option( 'amanah_seo_module_license_secrets', array() ) ), 'ai-license-regression-key' );
$checks['Activation endpoint is pinned to the licensing service host'] = strpos( $core_source, "'api.amanahseoai.my.id'" ) !== false;

$failures = 0;
foreach ( $checks as $label => $passed ) {
	echo ( $passed ? 'PASS: ' : 'FAIL: ' ) . $label . PHP_EOL;
	if ( ! $passed ) { $failures++; }
}
echo count( $checks ) . ' checks, ' . $failures . ' failures' . PHP_EOL;
exit( 0 === $failures ? 0 : 1 );
