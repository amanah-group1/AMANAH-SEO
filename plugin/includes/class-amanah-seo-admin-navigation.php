<?php
/**
 * Keep AMANAH SEO navigation under one parent menu without unregistering routes.
 *
 * Feature pages stay registered so WordPress can authorize direct links from
 * dashboard cards; CSS hides their sidebar rows only.
 *
 * @package AMANAH_SEO
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Admin_Navigation {
	public static function register(): void {
		add_action( 'admin_menu', array( __CLASS__, 'enforce_parent_navigation' ), 9999 );
	}

	public static function enforce_parent_navigation(): void {
		if ( ! is_admin() ) {
			return;
		}

		/*
		 * Do not call remove_menu_page(), unset($submenu), or remove submenu
		 * entries here. WordPress uses those menu structures to authorize
		 * admin.php?page=... requests, so deleting a hidden row can produce
		 * "Sorry, you are not allowed to access this page" for valid card links.
		 */
		add_action( 'admin_head', array( __CLASS__, 'hide_unwanted_navigation_rows' ), 99 );
	}

	public static function hide_unwanted_navigation_rows(): void {
		if ( ! current_user_can( 'manage_amanah_seo' ) && ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $menu, $submenu;
		$css = array();

		// Keep AMANAH SEO as the only visible top-level AMANAH SEO menu.
		if ( is_array( $menu ) ) {
			foreach ( $menu as $item ) {
				$slug = isset( $item[2] ) ? (string) $item[2] : '';
				if ( 'amanah-seo' !== $slug && 0 === strpos( $slug, 'amanah-seo' ) ) {
					$id = 'toplevel_page_' . sanitize_html_class( $slug );
					$css[] = '#adminmenu li#' . $id . '{display:none!important}';
				}
			}
		}

		// Hide feature-page rows, but retain them in WordPress's menu registry.
		$allowed = array( 'amanah-seo', 'amanah-seo-access', 'amanah-seo-core', 'amanah-seo-ai-tools', 'amanah-seo-engine-workflow' );
		if ( isset( $submenu['amanah-seo'] ) && is_array( $submenu['amanah-seo'] ) ) {
			foreach ( $submenu['amanah-seo'] as $item ) {
				$slug = isset( $item[2] ) ? (string) $item[2] : '';
				if ( ! in_array( $slug, $allowed, true ) ) {
					$href = 'admin.php?page=' . $slug;
					$css[] = '#adminmenu .wp-submenu a[href="' . esc_attr( $href ) . '"]{display:none!important}';
				}
			}
		}

		// The module landing pages do not expose their feature children in the sidebar.
		foreach ( array( 'amanah-seo-core', 'amanah-seo-ai-tools', 'amanah-seo-engine-workflow' ) as $parent_slug ) {
			if ( isset( $submenu[ $parent_slug ] ) && is_array( $submenu[ $parent_slug ] ) ) {
				foreach ( $submenu[ $parent_slug ] as $item ) {
					$slug = isset( $item[2] ) ? (string) $item[2] : '';
					$href = 'admin.php?page=' . $slug;
					$css[] = '#adminmenu .wp-submenu a[href="' . esc_attr( $href ) . '"]{display:none!important}';
				}
			}
		}

		// Hide WordPress's duplicate parent row without removing its route metadata.
		$css[] = '#adminmenu .wp-submenu a[href="admin.php?page=amanah-seo"]{display:none!important}';
		if ( $css ) {
			echo '<style>' . implode( "\n", array_unique( $css ) ) . '</style>';
		}
	}
}
