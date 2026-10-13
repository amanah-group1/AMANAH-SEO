<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Serp;

use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Google Search provider backed by Gemini's Google Search grounding.
 *
 * This provider intentionally returns grounded web sources rather than claiming
 * to provide a paid rank-tracking SERP dataset.
 */
final class GoogleSearchSerpProvider implements SerpProviderInterface {

	private const MODEL = 'gemini-3.5-flash-lite';

	private $http;

	public function __construct( HttpClientInterface $http ) {
		$this->http = $http;
	}

	public function search( string $keyword, array $options = array() ): array {
		$api_key = isset( $options['api_key'] ) ? trim( (string) $options['api_key'] ) : '';

		if ( '' === $api_key ) {
			throw new \RuntimeException( 'Gemini credentials are not configured for Google Search.' );
		}

		$keyword = trim( $keyword );
		if ( '' === $keyword ) {
			throw new \RuntimeException( 'Search keyword is empty.' );
		}

		$url = 'https://generativelanguage.googleapis.com/v1beta/interactions';
		$payload = array(
			'model' => self::MODEL,
			'store' => false,
			'input' => 'Use Google Search to find the most relevant organic web sources for the exact SEO keyword "' . $keyword . '". Search the Indonesian web where relevant. Do not invent sources. Cite the actual web sources used.',
			'tools' => array(
				array(
					'type' => 'google_search',
				),
			),
		);

		$response = $this->http->request(
			'POST',
			$url,
			array(
				'headers' => array(
					'Content-Type'   => 'application/json',
					'x-goog-api-key' => $api_key,
				),
				'body'    => wp_json_encode( $payload ),
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new \RuntimeException( 'Google Search network error: ' . $response->get_error_message() );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( $status >= 400 ) {
			$message = is_array( $body ) && isset( $body['error']['message'] ) ? (string) $body['error']['message'] : 'Unknown Gemini Search error.';
			throw new \RuntimeException( 'Google Search provider error: HTTP ' . $status . ' | ' . sanitize_text_field( $message ) );
		}

		if ( ! is_array( $body ) ) {
			throw new \RuntimeException( 'Google Search provider returned an invalid JSON response.' );
		}

		$out   = array();
		$seen  = array();
		$steps = isset( $body['steps'] ) && is_array( $body['steps'] ) ? $body['steps'] : array();

		foreach ( $steps as $step ) {
			if ( ! is_array( $step ) || 'model_output' !== ( $step['type'] ?? '' ) ) {
				continue;
			}

			$content = isset( $step['content'] ) && is_array( $step['content'] ) ? $step['content'] : array();
			foreach ( $content as $block ) {
				if ( ! is_array( $block ) || 'text' !== ( $block['type'] ?? '' ) ) {
					continue;
				}

				$annotations = isset( $block['annotations'] ) && is_array( $block['annotations'] ) ? $block['annotations'] : array();
				foreach ( $annotations as $annotation ) {
					if ( ! is_array( $annotation ) || 'url_citation' !== ( $annotation['type'] ?? '' ) ) {
						continue;
					}

					$uri   = isset( $annotation['url'] ) ? trim( (string) $annotation['url'] ) : '';
					$title = isset( $annotation['title'] ) ? sanitize_text_field( (string) $annotation['title'] ) : '';

					if ( '' === $uri || isset( $seen[ strtolower( $uri ) ] ) ) {
						continue;
					}

					$resolved  = $this->resolveUrl( $uri );
					$url_value = '' !== $resolved ? $resolved : $uri;
					$key       = strtolower( $url_value );

					if ( isset( $seen[ $key ] ) ) {
						continue;
					}

					$seen[ strtolower( $uri ) ] = true;
					$seen[ $key ]               = true;

					$domain = wp_parse_url( $url_value, PHP_URL_HOST );
					$domain = is_string( $domain ) ? sanitize_text_field( preg_replace( '/^www\\./i', '', $domain ) ) : '';

					$out[] = array(
						'position'    => count( $out ) + 1,
						'page_number' => 1,
						'title'       => $title,
						'url'         => esc_url_raw( $url_value ),
						'domain'      => $domain,
						'snippet'     => '',
						'result_type' => 'google_grounded_source',
					);

					if ( count( $out ) >= 10 ) {
						break 3;
					}
				}
			}
		}

		if ( empty( $out ) ) {
			throw new \RuntimeException( 'Google Search returned no grounded web sources for this keyword.' );
		}

		return $out;
	}

	private function resolveUrl( string $url ): string {
		$current = $url;

		for ( $attempt = 0; $attempt < 4; $attempt++ ) {
			$response = $this->http->request(
				'GET',
				$current,
				array(
					'redirection' => 0,
					'timeout'     => 15,
				)
			);

			if ( is_wp_error( $response ) ) {
				return '';
			}

			$status = (int) wp_remote_retrieve_response_code( $response );
			if ( $status >= 300 && $status < 400 ) {
				$location = trim( (string) wp_remote_retrieve_header( $response, 'location' ) );
				if ( '' === $location ) {
					return '';
				}
				$current = esc_url_raw( $location );
				continue;
			}

			if ( $status >= 200 && $status < 300 ) {
				return esc_url_raw( $current );
			}

			return '';
		}

		return '';
	}
}
