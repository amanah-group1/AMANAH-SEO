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
		 * Remove any legacy top-level Core or standalone module entries.
		 * The canonical parent is now the single AMANAH SEO menu.
		 */
		$legacy_top_level_slugs = array(
			'amanah-seo-core',
			'amanah-seo-ai',
			'amanah-seo-ai-tools',
			'amanah-seo-engine',
			'amanah-seo-engine-workflow',
		);

		foreach ( $legacy_top_level_slugs as $slug ) {
			remove_menu_page( $slug );
		}

		global $submenu;

		/*
		 * WordPress automatically inserts the parent page as the first
		 * submenu item. Remove that duplicate so Sales Page is only reached
		 * by clicking the parent menu itself, not through a submenu link.
		 */
		if ( isset( $submenu['amanah-seo'] ) && is_array( $submenu['amanah-seo'] ) ) {
			foreach ( $submenu['amanah-seo'] as $index => $item ) {
				if ( isset( $item[2] ) && 'amanah-seo' === $item[2] ) {
					unset( $submenu['amanah-seo'][ $index ] );
				}
			}

			$submenu['amanah-seo'] = array_values( $submenu['amanah-seo'] );
		}

		/*
		 * Feature pages remain registered for dashboard-card links, but their
		 * individual sidebar entries stay hidden under the three dashboards.
		 */
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
