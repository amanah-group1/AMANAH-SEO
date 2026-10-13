<?php
/**
 * Plugin Activator Class
 *
 * Handles plugin activation requirements, database initialization,
 * and Entity Intelligence capabilities.
 *
 * @package AMANAH_SEO_AI
 */

defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Activator {
	public static function activate() {
		global $wp_version;

		if ( version_compare( PHP_VERSION, '8.1', '<' ) ) {
			wp_die( esc_html__( 'AMANAH SEO AI requires PHP 8.1 or higher.', 'amanah-seo-ai' ) );
		}

		if ( ! empty( $wp_version ) && version_compare( $wp_version, '6.0', '<' ) ) {
			wp_die( esc_html__( 'AMANAH SEO AI requires WordPress 6.x or higher.', 'amanah-seo-ai' ) );
		}

		if ( ! class_exists( 'AMANAH_SEO_AI_Install' ) || ! AMANAH_SEO_AI_Install::install() ) {
			wp_die( esc_html__( 'AMANAH SEO AI could not initialize its database.', 'amanah-seo-ai' ) );
		}

		$administrator = get_role( 'administrator' );
		if ( $administrator ) {
			$administrator->add_cap( 'amanah_view_entity' );
			$administrator->add_cap( 'amanah_manage_entity' );
			$administrator->add_cap( 'amanah_view_authority' );
			$administrator->add_cap( 'amanah_manage_authority' );
		}

		$editor = get_role( 'editor' );
		if ( $editor ) {
			$editor->add_cap( 'amanah_view_entity' );
			$editor->add_cap( 'amanah_view_authority' );
		}
	}
}
