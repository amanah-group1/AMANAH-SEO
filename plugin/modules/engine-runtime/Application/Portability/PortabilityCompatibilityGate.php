<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Portability;

defined( 'ABSPATH' ) || exit;

final class PortabilityCompatibilityGate {
	public const MIN_SCHEMA_VERSION = 6;
	public const MAX_SCHEMA_VERSION = 7;

	public function validate( array $document ): void {
		$schema = isset( $document['schema_version'] ) ? (int) $document['schema_version'] : 0;
		if ( $schema < self::MIN_SCHEMA_VERSION || $schema > self::MAX_SCHEMA_VERSION ) {
			throw new \InvalidArgumentException( 'Backup schema version is not compatible with this release.' );
		}

		$pluginVersion = isset( $document['plugin_version'] ) ? (string) $document['plugin_version'] : '';
		$currentVersion = defined( 'AMANAH_SEO_ENGINE_VERSION' ) ? (string) AMANAH_SEO_ENGINE_VERSION : '';
		if ( '' !== $pluginVersion && '' !== $currentVersion ) {
			$sourceMajor = preg_replace( '/^v/i', '', $pluginVersion );
			$currentMajor = preg_replace( '/^v/i', '', $currentVersion );
			$sourceMajor = (int) explode( '.', $sourceMajor )[0];
			$currentMajor = (int) explode( '.', $currentMajor )[0];
			if ( $sourceMajor !== $currentMajor ) {
				throw new \InvalidArgumentException( 'Backup plugin major version is not compatible with this release.' );
			}
		}
	}
}
