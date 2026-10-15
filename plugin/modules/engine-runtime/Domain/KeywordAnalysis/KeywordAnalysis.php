<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\KeywordAnalysis;

defined( 'ABSPATH' ) || exit;

final class KeywordAnalysis {

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
		$this->phrase               = $phrase;
		$this->normalized_phrase    = $normalized_phrase;
		$this->terms                = $terms;
		$this->word_count           = $word_count;
		$this->character_count      = $character_count;
		$this->has_number           = $has_number;
		$this->has_question_signal  = $has_question_signal;
	}

	public function getPhrase(): string { return $this->phrase; }
	public function getNormalizedPhrase(): string { return $this->normalized_phrase; }
	public function getTerms(): array { return $this->terms; }
	public function getWordCount(): int { return $this->word_count; }
	public function getCharacterCount(): int { return $this->character_count; }
	public function hasNumber(): bool { return $this->has_number; }
	public function hasQuestionSignal(): bool { return $this->has_question_signal; }
}
