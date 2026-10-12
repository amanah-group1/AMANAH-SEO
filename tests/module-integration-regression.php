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
$checks['AI runtime keeps legacy core implementation'] = strpos( $read( 'plugin/modules/ai-runtime/includes/class-plugin-core.php' ), 'class AMANAH_SEO_AI_Plugin_Core' ) !== false;
$checks['FAQ endpoint uses governed AI client'] = strpos( $read( 'plugin/modules/ai-runtime/modules/faq/class-faq-generator.php' ), "generate( 'faq_generation'" ) !== false;
$checks['Schema endpoint uses governed AI client'] = strpos( $read( 'plugin/modules/ai-runtime/modules/schema/class-schema-generator.php' ), "generate( 'schema_generation'" ) !== false;
$checks['Entity Profile is nested under Core menu'] = strpos( $read( 'plugin/modules/ai-runtime/modules/entity/class-entity-admin.php' ), "add_submenu_page('amanah-seo'" ) !== false;
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
