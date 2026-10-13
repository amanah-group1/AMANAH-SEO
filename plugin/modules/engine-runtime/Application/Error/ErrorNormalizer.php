<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Error;

defined( 'ABSPATH' ) || exit;

final class ErrorNormalizer {

	public function normalize( \Throwable $exception ): ApplicationError {
		if ( $exception instanceof \InvalidArgumentException ) {
			return new ApplicationError( 'invalid_argument', $exception->getMessage() );
		}

		if ( $exception instanceof \DomainException ) {
			return new ApplicationError( 'domain_error', $exception->getMessage() );
		}

		return new ApplicationError( 'application_error', 'The requested operation could not be completed.' );
	}
}
