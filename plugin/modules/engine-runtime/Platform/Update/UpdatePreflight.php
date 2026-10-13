<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Platform\Update;

defined( 'ABSPATH' ) || exit;

final class UpdatePreflight {

	public function check( UpdateMetadata $metadata ): array {
		global $wp_version;

		$reasons = array();

		if ( version_compare( PHP_VERSION, $metadata->getRequiresPhp(), '<' ) ) {
			$reasons[] = 'php_incompatible';
		}

		if ( isset( $wp_version ) && version_compare( $wp_version, $metadata->getRequiresWordPress(), '<' ) ) {
			$reasons[] = 'wordpress_incompatible';
		}

		$plugin_dir = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : '';
		if ( '' === $plugin_dir || ! is_dir( $plugin_dir ) || ! is_writable( $plugin_dir ) ) {
			$reasons[] = 'plugin_directory_not_writable';
		}

		return array(
			'ready'   => empty( $reasons ),
			'reasons' => $reasons,
		);
	}
}
