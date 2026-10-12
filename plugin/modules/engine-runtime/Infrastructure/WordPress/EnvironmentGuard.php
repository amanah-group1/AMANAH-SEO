<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\WordPress;

defined( 'ABSPATH' ) || exit;

final class EnvironmentGuard {

	public function assertCompatible(): void {
		global $wp_version;

		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			wp_die( esc_html__( 'Amanah SEO Engine V2 requires PHP 7.4 or newer.', 'amanah-seo-engine' ) );
		}

		if ( isset( $wp_version ) && version_compare( $wp_version, '6.0', '<' ) ) {
			wp_die( esc_html__( 'Amanah SEO Engine V2 requires WordPress 6.0 or newer.', 'amanah-seo-engine' ) );
		}

		if ( ! function_exists( 'wp_get_current_user' ) || ! function_exists( 'get_option' ) ) {
			wp_die( esc_html__( 'Amanah SEO Engine V2 requires a complete WordPress environment.', 'amanah-seo-engine' ) );
		}
	}
}
