<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Orchestration;

defined( 'ABSPATH' ) || exit;

final class RepositoryOrchestrator {

	private $repositories = array();

	public function register( string $name, object $repository ): void {
		$this->repositories[ $name ] = $repository;
	}

	public function has( string $name ): bool {
		return isset( $this->repositories[ $name ] );
	}

	public function get( string $name ): object {
		if ( ! $this->has( $name ) ) {
			throw new \RuntimeException( 'Repository is not registered: ' . $name );
		}

		return $this->repositories[ $name ];
	}
}
