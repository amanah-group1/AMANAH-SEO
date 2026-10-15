<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\KeywordRelationship;

use Amanah\SEOEngine\Domain\KeywordAnalysis\KeywordAnalysis;

defined( 'ABSPATH' ) || exit;

final class KeywordRelationshipService {

	public function compare(
		int $source_keyword_id,
		KeywordAnalysis $source,
		int $target_keyword_id,
		KeywordAnalysis $target
	): KeywordRelationship {
		$source_terms = array_values( array_unique( $source->getTerms() ) );
		$target_terms = array_values( array_unique( $target->getTerms() ) );

		$shared_terms = array_values( array_intersect( $source_terms, $target_terms ) );
		$union       = array_values( array_unique( array_merge( $source_terms, $target_terms ) ) );

		$score = empty( $union )
			? 0.0
			: ( count( $shared_terms ) / count( $union ) ) * 100;

		if ( $source->getNormalizedPhrase() === $target->getNormalizedPhrase() ) {
			$score = 100.0;
		}

		return new KeywordRelationship(
			$source_keyword_id,
			$target_keyword_id,
			$shared_terms,
			$score
		);
	}
}
