<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\DTO;

use Amanah\SEOEngine\Domain\KeywordRelationship\KeywordRelationship;

defined( 'ABSPATH' ) || exit;

final class KeywordRelationshipData {

	private $source_keyword_id;
	private $target_keyword_id;
	private $shared_terms;
	private $overlap_score;
	private $detection_method;

	private function __construct( KeywordRelationship $relationship ) {
		$this->source_keyword_id = $relationship->getSourceKeywordId();
		$this->target_keyword_id = $relationship->getTargetKeywordId();
		$this->shared_terms      = $relationship->getSharedTerms();
		$this->overlap_score     = $relationship->getOverlapScore();
		$this->detection_method  = $relationship->getDetectionMethod();
	}

	public static function fromDomain( KeywordRelationship $relationship ): self {
		return new self( $relationship );
	}

	public function toArray(): array {
		return array(
			'source_keyword_id' => $this->source_keyword_id,
			'target_keyword_id' => $this->target_keyword_id,
			'shared_terms' => $this->shared_terms,
			'overlap_score' => $this->overlap_score,
			'detection_method' => $this->detection_method,
		);
	}
}
