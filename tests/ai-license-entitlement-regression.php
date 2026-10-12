<?php
declare( strict_types=1 );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'AUTH_KEY', 'ai-license-regression-test-key' );
define( 'DAY_IN_SECONDS', 86400 );

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
function wp_generate_uuid4() { static $i = 0; $i++; return sprintf( '123e4567-e89b-42d3-a456-%012d', $i ); }
function home_url() { return 'https://example.test'; }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function wp_json_encode( $value ) { return json_encode( $value ); }
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
$valid_response = static function ( $status = 'ACTIVE', $entitlements = array( 'content_analyzer' ) ) {
	$data = array( 'status' => strtoupper( $status ), 'entitlements' => $entitlements, 'plan' => 'test', 'expires_at' => 'LIFETIME' );
	if ( 'GRACE' === strtoupper( $status ) ) {
		$data['grace_until'] = gmdate( 'c', time() + 3600 );
	}
	return array(
		'response' => array( 'code' => 200 ),
		'body' => json_encode( array( 'success' => true, 'data' => $data ) ),
	);
};
$checks = array();

$GLOBALS['amanah_license_mock_response'] = $valid_response();
$GLOBALS['amanah_license_mock_error'] = false;
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Valid service response activates the AI product'] = 'active' === $state['status'] && in_array( 'content_analyzer', $state['entitlements'], true );
$payload = json_decode( $GLOBALS['amanah_license_last_request']['args']['body'] ?? '', true );
$checks['Activation follows the signed v1 service contract'] = 'https://api.amanahseoai.my.id/v1/activate' === $GLOBALS['amanah_license_last_request']['url'] && 'amanah-seo-ai' === ( $payload['product_id'] ?? '' ) && 'ai-license-regression-key' === ( $payload['license_key'] ?? '' ) && ! empty( $payload['site_id'] ) && ! empty( $payload['domain'] ) && ! empty( $payload['request_id'] ) && ! empty( $payload['timestamp'] ) && hash_equals( hash_hmac( 'sha256', $payload['request_id'] . "\n" . $payload['timestamp'] . "\\n/V1/ACTIVATE\\n" . $payload['site_id'], 'ai-license-regression-key' ), $payload['signature'] ?? '' );

$GLOBALS['amanah_license_mock_error'] = true;
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Network failure fails closed without exposing credentials'] = 'unknown' === $state['status'] && 'service_unavailable' === $state['reason'] && false === strpos( serialize( $state ), 'ai-license-regression-key' );
$GLOBALS['amanah_license_mock_error'] = false;

$GLOBALS['amanah_license_mock_response'] = $valid_response( 'grace' );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Grace-period status preserves the established AI license behavior'] = 'grace' === $state['status'] && in_array( 'content_analyzer', $state['entitlements'], true );

$GLOBALS['amanah_license_mock_response'] = $valid_response( 'invalid' );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Invalid license status is not treated as active'] = 'invalid' === $state['status'];

$GLOBALS['amanah_license_test_options']['amanah_seo_license_api_endpoint'] = 'https://attacker.example';
$GLOBALS['amanah_license_mock_response'] = $valid_response();
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Configured endpoint cannot redirect license key away from the trusted service host'] = 'active' === $state['status'] && 'https://api.amanahseoai.my.id/v1/activate' === $GLOBALS['amanah_license_last_request']['url'];
unset( $GLOBALS['amanah_license_test_options']['amanah_seo_license_api_endpoint'] );

$GLOBALS['amanah_license_mock_response'] = $valid_response( 'active', array() );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Active response without entitlements is rejected'] = 'unknown' === $state['status'] && 'invalid_response' === $state['reason'];

$GLOBALS['amanah_license_mock_response'] = array( 'response' => array( 'code' => 400 ), 'body' => json_encode( array( 'success' => false, 'error' => array( 'code' => 'PRODUCT_MISMATCH', 'message' => 'Product mismatch.' ) ) ) );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Service product-mismatch error is treated as a rejected license'] = 'invalid' === $state['status'] && 'license_rejected' === $state['reason'];

$GLOBALS['amanah_license_mock_response'] = array( 'response' => array( 'code' => 503 ), 'body' => '{}' );
$state = $activate->invoke( null, 'ai-license-regression-key' );
$checks['Non-success HTTP response fails closed'] = 'unknown' === $state['status'];

$GLOBALS['amanah_license_mock_response'] = $valid_response();
$GLOBALS['amanah_license_test_options']['amanah_seo_module_license_statuses'] = array( 'ai' => array( 'status' => 'active', 'entitlements' => array( 'content_analyzer' ), 'validated_at' => gmdate( 'c' ), 'expires_at' => 'LIFETIME' ) );
$checks['Active entitlement grants the matching AI capability'] = AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', 'content_analyzer' );
$checks['Active entitlement does not grant a different capability'] = ! AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', 'schema_generation' );
$GLOBALS['amanah_license_test_options']['amanah_seo_module_license_statuses']['ai']['status'] = 'grace';
$GLOBALS['amanah_license_test_options']['amanah_seo_module_license_statuses']['ai']['grace_until'] = gmdate( 'c', time() + 3600 );
$checks['Grace-period entitlement remains available when explicitly granted'] = AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', 'content_analyzer' );
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
