<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\KeywordIntelligence;

defined( 'ABSPATH' ) || exit;

final class KeywordIntelligenceEngine {

	/**
	 * @var KeywordCandidateSourceInterface[]
	 */
	private $sources;

	/**
	 * @param KeywordCandidateSourceInterface[] $sources
	 */
	public function __construct( array $sources ) {
		$this->sources = $sources;
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	public function discover( string $pillar_phrase ): array {
		$merged = array();

		foreach ( $this->sources as $source ) {
			foreach ( $source->findCandidates( $pillar_phrase ) as $candidate ) {
				$keyword = trim( (string) ( $candidate['keyword'] ?? '' ) );

				if ( '' === $keyword ) {
					continue;
				}

				$normalized = $this->normalize( $keyword );

				if ( '' === $normalized ) {
					continue;
				}

				if ( ! isset( $merged[ $normalized ] ) ) {
					$candidate['keyword'] = $keyword;
					$candidate['source'] = (string) ( $candidate['source'] ?? 'unknown' );
					$candidate['sources'] = array( $candidate['source'] );
					$merged[ $normalized ] = $candidate;
					continue;
				}

				$existing_sources = isset( $merged[ $normalized ]['sources'] ) && is_array( $merged[ $normalized ]['sources'] )
					? $merged[ $normalized ]['sources']
					: array();

				if ( ! in_array( (string) ( $candidate['source'] ?? 'unknown' ), $existing_sources, true ) ) {
					$existing_sources[] = (string) ( $candidate['source'] ?? 'unknown' );
				}

				$merged[ $normalized ]['sources'] = $existing_sources;
				$merged[ $normalized ] = $this->mergeMetrics( $merged[ $normalized ], $candidate );
			}
		}

		return array_values( $merged );
	}

	private function normalize( string $keyword ): string {
		$keyword = trim( (string) preg_replace( '/\\s+/u', ' ', $keyword ) );

		return function_exists( 'mb_strtolower' )
			? mb_strtolower( $keyword, 'UTF-8' )
			: strtolower( $keyword );
	}

	/**
	 * Keep the first meaningful metric and enrich it with later sources.
	 *
	 * @param array<string,mixed> $current
	 * @param array<string,mixed> $candidate
	 * @return array<string,mixed>
	 */
	private function mergeMetrics( array $current, array $candidate ): array {
		$metric_keys = array(
			'search_volume',
			'impressions',
			'clicks',
			'trend_value',
			'trend_direction',
			'competition',
			'search_intent',
			'relevance',
			'source_reference',
		);

		foreach ( $metric_keys as $key ) {
			$current_value = $current[ $key ] ?? null;
			$candidate_value = $candidate[ $key ] ?? null;

			if ( null === $current_value || '' === $current_value ) {
				if ( null !== $candidate_value && '' !== $candidate_value ) {
					$current[ $key ] = $candidate_value;
				}
			}
		}

		return $current;
	}
}
