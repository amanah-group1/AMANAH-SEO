<?php
/**
 * Internal module bootstrap for the single AMANAH SEO plugin.
 *
 * Legacy AI class names and storage contracts are intentionally retained.
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Modules {
	private static $booted = false;

	public static function boot() {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		if ( ! defined( 'AMANAH_SEO_AI_PATH' ) ) {
			define( 'AMANAH_SEO_AI_PATH', AMANAH_SEO_PATH . 'modules/ai-runtime/' );
		}
		if ( ! defined( 'AMANAH_SEO_AI_URL' ) ) {
			define( 'AMANAH_SEO_AI_URL', plugin_dir_url( dirname( __DIR__ ) . '/amanah-seo.php' ) . 'modules/ai-runtime/' );
		}
		if ( ! defined( 'AMANAH_SEO_AI_PREFIX' ) ) {
			define( 'AMANAH_SEO_AI_PREFIX', 'amanah_seo_ai_' );
		}

		require_once AMANAH_SEO_AI_PATH . 'includes/class-plugin-loader.php';
		require_once AMANAH_SEO_AI_PATH . 'includes/class-plugin-core.php';
		require_once AMANAH_SEO_AI_PATH . 'includes/class-install.php';

		$core = new AMANAH_SEO_AI_Plugin_Core();
		$core->run();

		// The legacy installer uses dbDelta and preserves existing schema options.
		// Its legacy-table archival path is guarded against destructive duplicate archives.
		add_action( 'plugins_loaded', array( 'AMANAH_SEO_AI_Install', 'maybe_upgrade' ), 20 );
	}
}
