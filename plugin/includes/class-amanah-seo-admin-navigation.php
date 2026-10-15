<?php
/**
 * Keep AMANAH SEO navigation under one parent menu.
 *
 * The parent opens the Sales Page. Only Settings & Access, CORE, AI, and
 * ENGINE remain visible beneath it; feature pages are opened from dashboard
 * cards and are not listed as individual sidebar entries.
 *
 * @package AMANAH_SEO
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Admin_Navigation {
	public static function register(): void {
		// Run after Core and module menus have registered their pages.
		add_action( 'admin_menu', array( __CLASS__, 'enforce_parent_navigation' ), 9999 );
	}

	public static function enforce_parent_navigation(): void {
		if ( ! is_admin() ) {
			return;
		}

		/*
		 * Remove every legacy AMANAH SEO top-level entry except the canonical
		 * parent. This catches old feature slugs such as Article Generation too.
		 */
		global $menu, $submenu;
		if ( is_array( $menu ) ) {
			foreach ( $menu as $item ) {
				$slug = isset( $item[2] ) ? (string) $item[2] : '';
				if ( 'amanah-seo' !== $slug && 0 === strpos( $slug, 'amanah-seo' ) ) {
					remove_menu_page( $slug );
				}
			}
		}

		/*
		 * Keep WordPress's auto-inserted parent submenu row in the menu data:
		 * WordPress uses it to resolve clicks on the top-level parent. Hide it
		 * visually with CSS instead of deleting it, or the parent can open the
		 * first feature submenu (for example Article Generation).
		 */
		if ( isset( $submenu['amanah-seo'] ) && is_array( $submenu['amanah-seo'] ) ) {
			$allowed = array( 'amanah-seo', 'amanah-seo-access', 'amanah-seo-core', 'amanah-seo-ai-tools', 'amanah-seo-engine-workflow' );
			foreach ( $submenu['amanah-seo'] as $index => $item ) {
				$slug = isset( $item[2] ) ? (string) $item[2] : '';
				if ( ! in_array( $slug, $allowed, true ) ) {
					unset( $submenu['amanah-seo'][ $index ] );
				}
			}
			$submenu['amanah-seo'] = array_values( $submenu['amanah-seo'] );
		}

		/*
		 * Feature pages remain routable from dashboard cards, but do not expose
		 * their individual entries in the WordPress sidebar.
		 */
		foreach ( array( 'amanah-seo-core', 'amanah-seo-ai-tools', 'amanah-seo-engine-workflow' ) as $parent_slug ) {
			if ( isset( $submenu[ $parent_slug ] ) ) {
				unset( $submenu[ $parent_slug ] );
			}
		}
		add_action( 'admin_head', array( __CLASS__, 'hide_duplicate_sales_page_submenu' ) );
	}
	public static function hide_duplicate_sales_page_submenu(): void {
		if ( ! current_user_can( 'manage_amanah_seo' ) ) {
			return;
		}
		echo '<style>#adminmenu .wp-submenu li:has(> a[href="admin.php?page=amanah-seo"]) { display: none !important; }</style>';
	}

}
