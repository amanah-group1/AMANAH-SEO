<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\KeywordRelationship;

defined( 'ABSPATH' ) || exit;

final class KeywordRelationship {

	private $source_keyword_id;
	private $target_keyword_id;
	private $shared_terms;
	private $overlap_score;
	private $detection_method;

	public function __construct(
		int $source_keyword_id,
		int $target_keyword_id,
		array $shared_terms,
		float $overlap_score,
		string $detection_method = 'lexical'
	) {
		$this->source_keyword_id = $source_keyword_id;
		$this->target_keyword_id = $target_keyword_id;
		$this->shared_terms      = array_values( array_unique( $shared_terms ) );
		$this->overlap_score     = max( 0.0, min( 100.0, $overlap_score ) );
		$this->detection_method  = $detection_method;
	}

	public function getSourceKeywordId(): int { return $this->source_keyword_id; }
	public function getTargetKeywordId(): int { return $this->target_keyword_id; }
	public function getSharedTerms(): array { return $this->shared_terms; }
	public function getOverlapScore(): float { return $this->overlap_score; }
	public function getDetectionMethod(): string { return $this->detection_method; }
}
