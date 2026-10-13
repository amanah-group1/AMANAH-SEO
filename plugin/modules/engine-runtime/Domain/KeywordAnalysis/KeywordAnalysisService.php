<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\KeywordAnalysis;

use Amanah\SEOEngine\Domain\Keyword\Keyword;

defined( 'ABSPATH' ) || exit;

final class KeywordAnalysisService {

	public function analyze( Keyword $keyword ): KeywordAnalysis {
		$phrase = $keyword->getPhrase();
		$normalized = Keyword::normalize( $phrase );
		$terms = '' === $normalized ? array() : preg_split( '/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY );

		if ( false === $terms ) {
			$terms = array();
		}

		return new KeywordAnalysis(
			$phrase,
			$normalized,
			array_values( array_unique( $terms ) ),
			count( $terms ),
			$this->length( $normalized ),
			(bool) preg_match( '/\d/u', $normalized ),
			(bool) preg_match( '/(?:\?|^)(apa|bagaimana|mengapa|kenapa|kapan|dimana|di mana|siapa)\b/u', $normalized )
		);
	}

	private function length( string $value ): int {
		return function_exists( 'mb_strlen' ) ? mb_strlen( $value, 'UTF-8' ) : strlen( $value );
	}
}
