<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Job;

defined( 'ABSPATH' ) || exit;

final class JobHandlerRegistry {
	private $handlers = array();

	public function register( JobHandlerInterface $handler ): void {
		$this->handlers[ $handler->getJobType() ] = $handler;
	}

	public function get( string $jobType ): ?JobHandlerInterface {
		return isset( $this->handlers[ $jobType ] ) ? $this->handlers[ $jobType ] : null;
	}
}
