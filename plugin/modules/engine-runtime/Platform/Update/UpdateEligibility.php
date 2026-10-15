<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Platform\Update;

defined( 'ABSPATH' ) || exit;

final class UpdateEligibility {

	public function check( UpdateMetadata $metadata, bool $entitled ): array {
		$reasons = array();

		if ( ! $entitled ) {
			$reasons[] = 'update_not_entitled';
		}

		if ( version_compare( PHP_VERSION, $metadata->getRequiresPhp(), '<' ) ) {
			$reasons[] = 'php_incompatible';
		}

		global $wp_version;
		if ( isset( $wp_version ) && version_compare( $wp_version, $metadata->getRequiresWordPress(), '<' ) ) {
			$reasons[] = 'wordpress_incompatible';
		}

		return array(
			'eligible' => empty( $reasons ),
			'reasons'  => $reasons,
		);
	}
}
