<?php
/**
 * Plugin Deactivator Class
 *
 * Handles plugin deactivation without removing persistent data.
 *
 * @package AMANAH_SEO_AI
 */

defined( 'ABSPATH' ) || exit;

/**
 * AMANAH_SEO_AI_Deactivator
 */
class AMANAH_SEO_AI_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Phase 1 has no persistent feature state to clean up.
	 *
	 * @return void
	 */
	public static function deactivate() {
	}
}
