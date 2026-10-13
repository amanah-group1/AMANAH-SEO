<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\KeywordIntelligence;

defined( 'ABSPATH' ) || exit;

interface KeywordCandidateSourceInterface {

	/**
	 * @return array<int,array<string,mixed>>
	 */
	public function findCandidates( string $pillar_phrase ): array;
}
