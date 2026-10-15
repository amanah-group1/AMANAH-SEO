<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Job;

defined( 'ABSPATH' ) || exit;

final class RetryPolicy {
	private $minimumDelay;
	private $maximumDelay;
	private $jitter;

	public function __construct( int $minimumDelay = 30, int $maximumDelay = 3600, int $jitter = 0 ) {
		$this->minimumDelay = max( 0, $minimumDelay );
		$this->maximumDelay = max( $this->minimumDelay, $maximumDelay );
		$this->jitter = max( 0, $jitter );
	}

	public function shouldRetry( string $category, int $attempt, int $maxAttempts ): bool {
		if ( $attempt >= $maxAttempts ) {
			return false;
		}
		return in_array( $category, array( 'timeout', 'network_error', 'provider_unavailable_error', 'rate_limit_error', 'system' ), true );
	}

	public function nextDelay( int $attempt ): int {
		$exponent = max( 0, $attempt - 1 );
		$delay = min( $this->maximumDelay, $this->minimumDelay * ( 2 ** $exponent ) );
		if ( $this->jitter > 0 ) {
			$delay += random_int( 0, $this->jitter );
		}
		return min( $this->maximumDelay, $delay );
	}
}
