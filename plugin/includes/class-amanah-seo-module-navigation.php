<?php
/**
 * Adds navigation only for internal modules that are actually bootstrapped.
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Module_Navigation {
	public static function menu() {
		// Engine navigation is registered by the real Engine runtime when vendored.
		// Do not advertise an inactive or placeholder Engine screen.
		if ( class_exists( 'AMANAH_SEO_Engine_Runtime' ) && method_exists( 'AMANAH_SEO_Engine_Runtime', 'register_admin_menu' ) ) {
			AMANAH_SEO_Engine_Runtime::register_admin_menu();
		}
	}
}
