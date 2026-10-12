<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Core\ServiceRegistry;

defined( 'ABSPATH' ) || exit;

/**
 * Minimal service registry for Phase 0 bootstrap.
 */
final class ServiceRegistry {

	/**
	 * Registered services.
	 *
	 * @var array<string, object>
	 */
	private $services = array();

	public function register( string $id, object $service ): void {
		$this->services[ $id ] = $service;
	}

	public function has( string $id ): bool {
		return isset( $this->services[ $id ] );
	}

	public function get( string $id ): object {
		if ( ! $this->has( $id ) ) {
			throw new \RuntimeException( 'Service is not registered: ' . $id );
		}

		return $this->services[ $id ];
	}
}
