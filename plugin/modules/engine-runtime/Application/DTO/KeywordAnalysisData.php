<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\DTO;

use Amanah\SEOEngine\Domain\KeywordAnalysis\KeywordAnalysis;

defined( 'ABSPATH' ) || exit;

final class KeywordAnalysisData {

	private $phrase;
	private $normalized_phrase;
	private $terms;
	private $word_count;
	private $character_count;
	private $has_number;
	private $has_question_signal;

	public function __construct(
		string $phrase,
		string $normalized_phrase,
		array $terms,
		int $word_count,
		int $character_count,
		bool $has_number,
		bool $has_question_signal
	) {
		$this->phrase              = $phrase;
		$this->normalized_phrase   = $normalized_phrase;
		$this->terms               = $terms;
		$this->word_count          = $word_count;
		$this->character_count     = $character_count;
		$this->has_number          = $has_number;
		$this->has_question_signal = $has_question_signal;
	}

	public static function fromDomain( KeywordAnalysis $analysis ): self {
		return new self(
			$analysis->getPhrase(),
			$analysis->getNormalizedPhrase(),
			$analysis->getTerms(),
			$analysis->getWordCount(),
			$analysis->getCharacterCount(),
			$analysis->hasNumber(),
			$analysis->hasQuestionSignal()
		);
	}

	public function toArray(): array {
		return array(
			'phrase'              => $this->phrase,
			'normalized_phrase'   => $this->normalized_phrase,
			'terms'               => $this->terms,
			'word_count'          => $this->word_count,
			'character_count'     => $this->character_count,
			'has_number'          => $this->has_number,
			'has_question_signal' => $this->has_question_signal,
		);
	}
}
