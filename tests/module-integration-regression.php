<?php
/**
 * Static integration contract checks; run: php tests/module-integration-regression.php
 */
$root = dirname( __DIR__ );
$read = static function ( $relative ) use ( $root ) {
	$path = $root . '/' . $relative;
	if ( ! is_file( $path ) ) {
		echo "FAIL: required integration file exists: {$relative}\n";
		return '';
	}
	return file_get_contents( $path );
};
$checks = array();
$checks['single Core entry loads internal module bootstrap'] = strpos( $read( 'plugin/amanah-seo.php' ), "class-amanah-seo-modules.php" ) !== false;
$core_entry = $read( 'plugin/amanah-seo.php' );
$core_admin = $read( 'plugin/includes/class-amanah-seo-admin.php' );
$checks['Core navigation class is declared once and stale duplicate file is not loaded'] = strpos( $core_admin, 'final class AMANAH_SEO_Module_Navigation' ) !== false && strpos( $core_entry, 'class-amanah-seo-module-navigation.php' ) === false;
$checks['AI runtime keeps legacy core implementation'] = strpos( $read( 'plugin/modules/ai-runtime/includes/class-plugin-core.php' ), 'class AMANAH_SEO_AI_Plugin_Core' ) !== false;
$engine_bootstrap = $read( 'plugin/modules/engine-runtime/bootstrap.php' );
$checks['ENGINE internal bootstrap uses native PSR-4 loader'] = strpos( $engine_bootstrap, 'ClassLoader' ) !== false && strpos( $engine_bootstrap, 'Plugin::boot()' ) !== false;
$checks['Core loads ENGINE internally without a second plugin entry point'] = strpos( $read( 'plugin/includes/class-amanah-seo-modules.php' ), "modules/engine-runtime/bootstrap.php" ) !== false && ! is_file( $root . '/plugin/modules/engine-runtime/amanah-seo-engine.php' );
$engine_all_files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/plugin/modules/engine-runtime', FilesystemIterator::SKIP_DOTS ) );
$engine_php_count = 0;
foreach ( $engine_all_files as $engine_file ) {
	if ( $engine_file->isFile() && 'php' === strtolower( $engine_file->getExtension() ) ) { $engine_php_count++; }
}
$checks['ENGINE production source and internal bootstrap are present'] = $engine_php_count >= 297 && is_file( $root . '/plugin/modules/engine-runtime/Infrastructure/Repository/WordPressKeywordRepository.php' ) && is_file( $root . '/plugin/modules/engine-runtime/Presentation/Admin/PageBlueprintAdmin.php' ) && is_file( $root . '/plugin/modules/engine-runtime/Platform/License/LicenseClient.php' );
$engine_menu = $read( 'plugin/modules/engine-runtime/Presentation/Admin/AdminMenu.php' );
$checks['ENGINE screens are nested under Core and do not create a second top-level menu'] = preg_match( "/add_submenu_page\\s*\\(\\s*'amanah-seo'/", $engine_menu ) === 1 && strpos( $engine_menu, 'add_menu_page(' ) === false;
$checks['All ENGINE workflow submenu parents resolve to the Core menu'] = preg_match( "/add_submenu_page\\s*\\(\\s*'amanah-seo-engine'\\s*,/", $engine_menu ) === 0 && substr_count( $engine_menu, "'amanah-seo'," ) >= 3;
$checks['ENGINE AI configuration and license screens remain Core-owned'] = strpos( $engine_menu, "array( 'license', 'updates', 'ai-configuration', 'ai-usage' )" ) !== false;
$engine_license_store = $read( 'plugin/modules/engine-runtime/Platform/License/LicenseSecretStore.php' );
$checks['ENGINE license adapter calls Core license manager in the global namespace'] = strpos( $engine_license_store, '\\AMANAH_SEO_Module_Licenses::set_license' ) !== false && strpos( $engine_license_store, '\\AMANAH_SEO_Module_Licenses::get_license' ) !== false && strpos( $engine_license_store, '\\AMANAH_SEO_Module_Licenses::delete_license' ) !== false;
$core_licenses = $read( 'plugin/includes/class-amanah-seo-module-licenses.php' );
$checks['Core activates AI licenses using the AI product identifier over the signed HTTPS v1 API'] = strpos( $core_licenses, "wp_remote_post(" ) !== false && strpos( $core_licenses, "'https://api.amanahseoai.my.id'" ) !== false && strpos( $core_licenses, "'/v1/activate'" ) !== false && strpos( $core_licenses, "'product_id' => 'amanah-seo-ai'" ) !== false && strpos( $core_licenses, 'hash_hmac( ' ) !== false;
$checks['Core validates AI activation success, status, and entitlements before recording state'] = strpos( $core_licenses, "isset( \$body['data'] )" ) !== false && strpos( $core_licenses, "isset( \$data['status'], \$data['entitlements'] )" ) !== false && strpos( $core_licenses, "in_array( \$service_status, \$allowed_statuses, true )" ) !== false && strpos( $core_licenses, 'save_license_status' ) !== false;
$legacy_migration = $read( 'plugin/modules/engine-runtime/Infrastructure/Migration/Migrations/Version17RemoveLegacySerpCredentials.php' );
$checks['ENGINE legacy credential migration preserves existing DataForSEO secrets'] = strpos( $legacy_migration, "delete( 'dataforseo' )" ) === false;

$checks['FAQ endpoint uses governed AI client'] = strpos( $read( 'plugin/modules/ai-runtime/modules/faq/class-faq-generator.php' ), "generate( 'faq_generation'" ) !== false;
$checks['Schema endpoint uses governed AI client'] = strpos( $read( 'plugin/modules/ai-runtime/modules/schema/class-schema-generator.php' ), "generate( 'schema_generation'" ) !== false;
$entity_admin = $read( 'plugin/modules/ai-runtime/modules/entity/class-entity-admin.php' );
$checks['Entity Profile is nested under Core menu and uses the Core page capability'] = strpos( $entity_admin, "add_submenu_page('amanah-seo'" ) !== false && strpos( $entity_admin, "'manage_amanah_seo','amanah-entity-profile'" ) !== false && strpos( $entity_admin, "current_user_can('manage_amanah_seo')" ) !== false;
$activator = $read( 'plugin/includes/class-amanah-seo-activator.php' );
$checks['Administrator role receives Core page and legacy entity action capabilities'] = strpos( $activator, "'manage_amanah_seo', 'amanah_view_entity', 'amanah_manage_entity'" ) !== false;
$checks['Entity mutations retain their explicit legacy capability gate'] = substr_count( $read( 'plugin/modules/ai-runtime/modules/entity/class-entity-controller.php' ), "current_user_can('amanah_manage_entity')" ) >= 3;
$entity_controller = $read( 'plugin/modules/ai-runtime/modules/entity/class-entity-controller.php' );
$checks['Entity save redirects to registered Entity Profile slug'] = strpos( $entity_controller, "admin.php?page=amanah-entity-profile" ) !== false;
$checks['Entity controller has no stale unregistered page redirects'] = strpos( $entity_controller, "admin.php?page=amanah-seo-ai" ) === false;
$entity_view = $read( 'plugin/modules/ai-runtime/modules/entity/views/entity-form.php' );
$checks['Entity list links use the registered Entity Profile slug'] = strpos( $entity_view, "admin.php?page=amanah-entity-profile&entity_id=" ) !== false;
$checks['Entity list has no stale unregistered page links'] = strpos( $entity_view, "admin.php?page=amanah-seo-ai&entity_id=" ) === false;
$checks['Authority is nested under Core menu'] = strpos( $read( 'plugin/modules/ai-runtime/modules/authority/class-authority-admin.php' ), "'amanah-seo'," ) !== false;
$install = $read( 'plugin/modules/ai-runtime/includes/class-install.php' );
$checks['legacy table migration never drops duplicate archive source'] = strpos( $install, 'DROP TABLE {$legacy}' ) === false;
$menu = $read( 'plugin/modules/ai-runtime/admin/class-admin-menu.php' );
$checks['AI settings menu uses Core parent'] = strpos( $menu, "'amanah-seo'," ) !== false;
$checks['FAQ and Schema do not expose placeholder menu pages'] = strpos( $menu, 'render_unavailable_content_tool' ) === false;
$failed = 0;
foreach ( $checks as $name => $passed ) {
	echo ( $passed ? 'PASS: ' : 'FAIL: ' ) . $name . "\n";
	if ( ! $passed ) { $failed++; }
}
echo sprintf( "%d checks, %d failures\n", count( $checks ), $failed );
exit( $failed ? 1 : 0 );
