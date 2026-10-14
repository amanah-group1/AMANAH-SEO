<?php
/**
 * Keep AMANAH SEO navigation under one parent menu.
 *
 * The parent opens the Sales Page. Keep only Settings & Access, CORE,
 * AMANAH SEO AI, and AMANAH SEO ENGINE visible beneath it. Feature pages
 * remain registered and routable; only their sidebar entries are filtered.
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
		 * Preserve the parent row internally so WordPress resolves the parent
		 * click to the Sales Page. Filter only visible submenu entries.
		 */
		if ( isset( $submenu['amanah-seo'] ) && is_array( $submenu['amanah-seo'] ) ) {
			$allowed = array(
				'amanah-seo',
				'amanah-seo-access',
				'amanah-seo-core',
				'amanah-seo-ai-tools',
				'amanah-seo-engine-workflow',
			);
			foreach ( $submenu['amanah-seo'] as $index => $item ) {
				$slug = isset( $item[2] ) ? (string) $item[2] : '';
				if ( ! in_array( $slug, $allowed, true ) ) {
					unset( $submenu['amanah-seo'][ $index ] );
				}
			}
			$submenu['amanah-seo'] = array_values( $submenu['amanah-seo'] );
		}

		/*
		 * Feature pages remain routable from cards and direct links. Remove
		 * duplicate child menus only; do not unregister pages or callbacks.
		 */
		foreach ( array( 'amanah-seo-core', 'amanah-seo-ai-tools', 'amanah-seo-engine-workflow' ) as $parent_slug ) {
			if ( isset( $submenu[ $parent_slug ] ) ) {
				unset( $submenu[ $parent_slug ] );
			}
		}
		add_action( 'admin_head', array( __CLASS__, 'style_sidebar_hierarchy' ) );
	}

	public static function style_sidebar_hierarchy(): void {
		if ( ! current_user_can( 'manage_amanah_seo' ) ) {
			return;
		}
		echo '<style>
			#adminmenu .wp-submenu li:has(> a[href="admin.php?page=amanah-seo"]) { display: none !important; }
			#adminmenu .wp-submenu li:has(> a[href*="page=amanah-seo-core"]) a,
			#adminmenu .wp-submenu li:has(> a[href*="page=amanah-seo-ai-tools"]) a,
			#adminmenu .wp-submenu li:has(> a[href*="page=amanah-seo-engine-workflow"]) a { padding-left: 36px !important; }
		</style>';
	}
}
