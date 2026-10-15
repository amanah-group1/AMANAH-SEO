<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\Quality;

defined( 'ABSPATH' ) || exit;

final class PrePublishValidator {

	public function validate( QualityResult $result ): void {
		if ( 'passed' !== $result->getStatus() ) {
			throw new \DomainException( 'Content cannot be published before deterministic quality validation passes.' );
		}
	}
}
