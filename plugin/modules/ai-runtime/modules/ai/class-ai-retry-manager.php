<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Retry_Manager {
	const MAX_RETRIES = 2;

	/**
	 * Run the primary provider with the locked retry policy, then optionally
	 * try the configured fallback once after retryable failures are exhausted.
	 *
	 * MAX_RETRIES counts retries after the initial request, not total attempts.
	 */
	public function run( callable $primary, ?callable $fallback = null ) {
		$last = null;

		for ( $attempt = 0; $attempt <= self::MAX_RETRIES; $attempt++ ) {
			try {
				return call_user_func( $primary );
			} catch ( AMANAH_SEO_AI_AI_Exception $error ) {
				$last = $error;

				if ( ! in_array( $error->get_category(), array( 'retryable', 'transport' ), true ) ) {
					throw $error;
				}
			}
		}

		if ( is_callable( $fallback ) ) {
			return call_user_func( $fallback );
		}

		throw $last;
	}
}
