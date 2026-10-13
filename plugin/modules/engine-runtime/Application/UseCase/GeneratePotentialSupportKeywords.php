<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\KeywordIntelligence\KeywordIntelligenceEngine;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Domain\Keyword\Keyword;

defined( 'ABSPATH' ) || exit;

final class GeneratePotentialSupportKeywords {

	private $repository;
	private $intelligence;

	public function __construct( KeywordRepositoryInterface $repository, KeywordIntelligenceEngine $intelligence ) {
		$this->repository = $repository;
		$this->intelligence = $intelligence;
	}

	public function execute( int $pillar_keyword_id ): array {
		$pillar = $this->repository->findById( $pillar_keyword_id );

		if ( null === $pillar ) {
			throw new \InvalidArgumentException( 'Pillar keyword was not found.' );
		}

		if ( 'pillar' !== $pillar->getArticleType() ) {
			throw new \DomainException( 'Potential support keywords can only be generated for a pillar keyword.' );
		}

		$existing = array();
		foreach ( $this->repository->findAll( 100 ) as $keyword ) {
			$existing[ $keyword->getNormalizedPhrase() ] = true;
		}

		$suggestions = array();
		foreach ( $this->intelligence->discover( $pillar->getPhrase() ) as $candidate ) {
			$keyword = trim( (string) ( $candidate['keyword'] ?? '' ) );

			if ( '' === $keyword ) {
				continue;
			}

			$normalized = Keyword::normalize( $keyword );

			if ( $normalized === $pillar->getNormalizedPhrase() || isset( $existing[ $normalized ] ) ) {
				continue;
			}

			$suggestion = $candidate;
			$suggestion['keyword'] = $keyword;
			$suggestion['source'] = (string) ( $candidate['source'] ?? 'unknown' );
			$suggestions[] = $suggestion;
		}
		return $suggestions;
	}
}
