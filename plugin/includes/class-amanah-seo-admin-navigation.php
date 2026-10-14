<?php
/**
 * Keep AMANAH SEO navigation to three clickable dashboard menus.
 *
 * Feature pages remain registered and accessible from their dashboard cards;
 * only their sidebar submenu entries are hidden.
 *
 * @package AMANAH_SEO
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Admin_Navigation {
	public static function register(): void {
		// Run after Core and module menus have registered their pages.
		add_action( 'admin_menu', array( __CLASS__, 'enforce_three_dashboard_menus' ), 9999 );
	}

	public static function enforce_three_dashboard_menus(): void {
		if ( ! is_admin() ) {
			return;
		}

		/*
		 * Remove legacy top-level entries so the old AMANAH SEO landing page
		 * and standalone module menus cannot appear beside the three dashboards.
		 */
		remove_menu_page( 'amanah-seo' );
		remove_menu_page( 'amanah-seo-ai' );
		remove_menu_page( 'amanah-seo-engine' );

		/*
		 * Keep only the three top-level dashboard links visible. WordPress
		 * page registrations and callbacks remain intact, so dashboard-card
		 * destinations still work even though their sidebar links are hidden.
		 */
		global $submenu;
		$dashboard_parents = array(
			'amanah-seo-core',
			'amanah-seo-ai-tools',
			'amanah-seo-engine-workflow',
		);

		foreach ( $dashboard_parents as $parent_slug ) {
			if ( isset( $submenu[ $parent_slug ] ) ) {
				unset( $submenu[ $parent_slug ] );
			}
		}
	}
}
