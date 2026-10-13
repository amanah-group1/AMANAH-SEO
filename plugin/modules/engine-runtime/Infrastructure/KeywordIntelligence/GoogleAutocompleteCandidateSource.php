<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\KeywordIntelligence;

use Amanah\SEOEngine\Application\KeywordIntelligence\KeywordCandidateSourceInterface;
use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;

defined( 'ABSPATH' ) || exit;

final class GoogleAutocompleteCandidateSource implements KeywordCandidateSourceInterface {

	private $http;
	private $max_queries;

	public function __construct( HttpClientInterface $http, int $max_queries = 6 ) {
		$this->http = $http;
		$this->max_queries = max( 1, min( 10, $max_queries ) );
	}

	public function findCandidates( string $pillar_phrase ): array {
		$phrase = trim( $pillar_phrase );

		if ( '' === $phrase ) {
			return array();
		}

		$queries = array( $phrase );
		foreach ( array( 'a', 'e', 'i', 'o', 'u' ) as $suffix ) {
			$queries[] = $phrase . ' ' . $suffix;
		}
		$queries = array_slice( $queries, 0, $this->max_queries );

		$candidates = array();

		foreach ( $queries as $query ) {
			$url = add_query_arg(
				array(
					'client' => 'firefox',
					'hl'     => 'id',
					'q'      => $query,
				),
				'https://suggestqueries.google.com/complete/search'
			);

			try {
				$response = $this->http->request(
					'GET',
					$url,
					array(
						'timeout' => 8,
						'headers' => array(
							'Accept' => 'application/json',
						),
					)
				);

				if ( is_wp_error( $response ) ) {
					continue;
				}

				$status = (int) wp_remote_retrieve_response_code( $response );
				if ( $status < 200 || $status >= 300 ) {
					continue;
				}

				$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
				if ( ! is_array( $body ) || ! isset( $body[1] ) || ! is_array( $body[1] ) ) {
					continue;
				}

				foreach ( $body[1] as $suggestion ) {
					$keyword = trim( sanitize_text_field( (string) $suggestion ) );

					if ( '' === $keyword ) {
						continue;
					}

					$candidates[] = array(
						'keyword'         => $keyword,
						'source'          => 'google_autocomplete',
						'source_reference' => $query,
					);
				}
			} catch ( \Throwable $e ) {
				continue;
			}
		}

		return $this->uniqueCandidates( $candidates );
	}

	private function uniqueCandidates( array $candidates ): array {
		$unique = array();

		foreach ( $candidates as $candidate ) {
			$key = function_exists( 'mb_strtolower' )
				? mb_strtolower( trim( (string) $candidate['keyword'] ), 'UTF-8' )
				: strtolower( trim( (string) $candidate['keyword'] ) );

			if ( '' === $key || isset( $unique[ $key ] ) ) {
				continue;
			}

			$unique[ $key ] = $candidate;
		}

		return array_values( $unique );
	}
}
