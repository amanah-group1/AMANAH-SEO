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

if ( ! class_exists( 'AMANAH_SEO_Module_Licenses' ) || ! class_exists( '\\Amanah\\SEOEngine\\Platform\\License\\LicenseSecretStore' ) ) {
	$failures[] = 'Core license manager or ENGINE license adapter is not loaded.';
} else {
	$test_key = 'engine-runtime-smoke-test-temporary-key';
	$engine_store = new \Amanah\SEOEngine\Platform\License\LicenseSecretStore();
	try {
		$engine_store->set( $test_key );
		if ( $engine_store->get() !== $test_key || AMANAH_SEO_Module_Licenses::get_license( 'engine' ) !== $test_key ) {
			$failures[] = 'ENGINE license adapter does not round-trip through Core-managed encrypted storage.';
		}
	} catch ( Throwable $exception ) {
		$failures[] = 'ENGINE license adapter failed: ' . $exception->getMessage();
	} finally {
		$engine_store->delete();
	}
}

$captured_ai_activation_payload = null;
$mock_ai_license_response = static function ( $preempt, $args, $url ) use ( &$captured_ai_activation_payload ) {
	if ( false === strpos( (string) $url, '/activate' ) ) {
		return $preempt;
	}
	$captured_ai_activation_payload = json_decode( (string) ( $args['body'] ?? '' ), true );
	return array(
		'headers' => array( 'content-type' => 'application/json' ),
		'body' => wp_json_encode( array(
			'success' => true,
			'data' => array(
				'status' => 'ACTIVE',
				'plan' => 'test',
				'expires_at' => 'LIFETIME',
				'entitlements' => array( 'content_analyzer' ),
				'activation_id' => 'runtime-smoke',
			),
		) ),
		'response' => array( 'code' => 200, 'message' => 'OK' ),
		'cookies' => array(),
		'filename' => null,
	);
};
add_filter( 'pre_http_request', $mock_ai_license_response, 10, 3 );
try {
	$activation_method = new ReflectionMethod( 'AMANAH_SEO_Module_Licenses', 'activate_ai_license' );
	$activation_method->setAccessible( true );
	$ai_license_state = $activation_method->invoke( null, 'runtime-smoke-ai-license-key' );
	if ( 'active' !== ( $ai_license_state['status'] ?? '' ) || ! in_array( 'content_analyzer', $ai_license_state['entitlements'] ?? array(), true ) ) {
		$failures[] = 'Core AI license activation did not accept a valid AI product entitlement response.';
	}
	$status_method = new ReflectionMethod( 'AMANAH_SEO_Module_Licenses', 'save_license_status' );
	$status_method->setAccessible( true );
	$status_method->invoke( null, 'ai', $ai_license_state );
	if ( ! AMANAH_SEO_Module_Licenses::has_entitlement( 'ai', 'content_analyzer' ) ) {
		$failures[] = 'Validated AI entitlement was not persisted for runtime enforcement.';
	}
	if ( 'amanah-seo-ai' !== ( $captured_ai_activation_payload['product_id'] ?? '' ) || 'runtime-smoke-ai-license-key' !== ( $captured_ai_activation_payload['license_key'] ?? '' ) ) {
		$failures[] = 'Core AI license activation did not send the expected product and key payload.';
	}
} catch ( Throwable $exception ) {
	$failures[] = 'Core AI license activation contract smoke test failed: ' . $exception->getMessage();
} finally {
	remove_filter( 'pre_http_request', $mock_ai_license_response, 10 );
}

$secret_store = new \Amanah\SEOEngine\Infrastructure\AI\SecretStore();
$legacy_secret = 'dataforseo-preservation-smoke-test';
try {
	$secret_store->set( 'dataforseo', $legacy_secret );
	$migration = new \Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version17RemoveLegacySerpCredentials();
	$migration->up();
	if ( $secret_store->get( 'dataforseo' ) !== $legacy_secret ) {
		$failures[] = 'ENGINE Version 17 migration did not preserve the existing DataForSEO credential.';
	}
} catch ( Throwable $exception ) {
	$failures[] = 'ENGINE credential preservation smoke test failed: ' . $exception->getMessage();
} finally {
	$secret_store->delete( 'dataforseo' );
}

$keyword_table = $wpdb->prefix . 'amanah_seo_keywords';
$sentinel_keyword = 'migration-preservation-smoke';
$inserted = $wpdb->insert(
	$keyword_table,
	array(
		'keyword' => $sentinel_keyword,
		'normalized_keyword' => $sentinel_keyword,
		'keyword_type' => 'primary',
		'status' => 'active',
		'created_at' => current_time( 'mysql', true ),
		'updated_at' => current_time( 'mysql', true ),
	)
);
if ( false === $inserted ) {
	$failures[] = 'Could not seed an existing ENGINE keyword row for the migration-preservation check.';
} else {
	$sentinel_id = (int) $wpdb->insert_id;
	try {
		$schema_migration = new \Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version2CoreDomain( $wpdb );
		$schema_migration->up();
		$preserved_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$keyword_table} WHERE id = %d", $sentinel_id ) );
		if ( (int) $preserved_id !== $sentinel_id ) {
			$failures[] = 'Existing ENGINE keyword data was not preserved by the idempotent schema migration.';
		}
	} catch ( Throwable $exception ) {
		$failures[] = 'Existing ENGINE data migration smoke test failed: ' . $exception->getMessage();
	} finally {
		$wpdb->delete( $keyword_table, array( 'id' => $sentinel_id ), array( '%d' ) );
	}
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
$entity_submenu = null;
foreach ( $core_submenus as $submenu_item ) {
	if ( isset( $submenu_item[2] ) && 'amanah-entity-profile' === $submenu_item[2] ) {
		$entity_submenu = $submenu_item;
		break;
	}
}
if ( ! current_user_can( 'manage_amanah_seo' ) || ! current_user_can( 'amanah_view_entity' ) || ! current_user_can( 'amanah_manage_entity' ) ) {
	$failures[] = 'Administrator lacks the expected Core or entity capabilities.';
}
if ( ! is_array( $entity_submenu ) || ! isset( $entity_submenu[1] ) || 'manage_amanah_seo' !== $entity_submenu[1] ) {
	$failures[] = 'Entity Profile submenu is missing or does not use the Core page capability.';
}

// Verify Core activation/upgrade checks leave an existing Entity Profile row untouched.
$entities_table = $wpdb->prefix . 'amanah_entities';
$sentinel_name = 'Entity Profile capability regression sentinel';
$sentinel = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id, entity_name, entity_type, industry, status FROM {$entities_table} WHERE entity_name = %s LIMIT 1", $sentinel_name ) );
if ( ! $sentinel ) {
	$inserted_entity = $wpdb->insert(
		$entities_table,
		array(
			'user_id' => 1,
			'entity_name' => $sentinel_name,
			'entity_type' => 'Organization',
			'industry' => 'Regression test',
			'status' => 'active',
		),
		array( '%d', '%s', '%s', '%s', '%s' )
	);
	if ( false === $inserted_entity ) {
		$failures[] = 'Could not create Entity Profile preservation sentinel.';
	} else {
		$sentinel = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id, entity_name, entity_type, industry, status FROM {$entities_table} WHERE id = %d", $wpdb->insert_id ) );
	}
}
if ( $sentinel ) {
	$before = array( (int) $sentinel->id, (int) $sentinel->user_id, $sentinel->entity_name, $sentinel->entity_type, $sentinel->industry, $sentinel->status );
	AMANAH_SEO_Activator::maybe_upgrade();
	$after = $wpdb->get_row( $wpdb->prepare( "SELECT id, user_id, entity_name, entity_type, industry, status FROM {$entities_table} WHERE id = %d", $sentinel->id ) );
	$after_values = $after ? array( (int) $after->id, (int) $after->user_id, $after->entity_name, $after->entity_type, $after->industry, $after->status ) : null;
	if ( $before !== $after_values ) {
		$failures[] = 'Entity Profile data changed during Core capability/upgrade regression check.';
	}
}

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

echo 'PASS: WordPress activation/reactivation, Core AI license API contract, ENGINE and Entity Profile data preservation, Core license storage, Entity Profile capability, and Core-owned menus.' . PHP_EOL;
