<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\PillarArticle;

use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;

defined( 'ABSPATH' ) || exit;

final class PillarArticleScanner {

	private $http;

	public function __construct( HttpClientInterface $http ) {
		$this->http = $http;
	}

	public function scan( string $url, string $support_keyword = '' ): array {
		$url = esc_url_raw( trim( $url ) );

		if ( '' === $url || ! wp_http_validate_url( $url ) ) {
			throw new \RuntimeException( 'Owner URL artikel Pilar tidak valid.' );
		}

		$site_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
		$url_host  = wp_parse_url( $url, PHP_URL_HOST );

		if ( ! $site_host || ! $url_host || strtolower( (string) $site_host ) !== strtolower( (string) $url_host ) ) {
			throw new \RuntimeException( 'Scanner hanya dapat membaca URL dari website ini.' );
		}

		$response = $this->http->request(
			'GET',
			$url,
			array(
				'timeout'    => 20,
				'redirection' => 3,
				'headers'    => array(
					'Accept'     => 'text/html,application/xhtml+xml',
					'User-Agent' => 'Amanah-SEO-Engine/1.0; +' . home_url( '/' ),
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new \RuntimeException( 'Artikel Pilar tidak dapat dibaca: ' . $response->get_error_message() );
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$body        = (string) wp_remote_retrieve_body( $response );
		$content_type = (string) wp_remote_retrieve_header( $response, 'content-type' );

		if ( $status_code < 200 || $status_code >= 300 ) {
			throw new \RuntimeException( 'Artikel Pilar mengembalikan HTTP ' . $status_code . '.' );
		}

		if ( '' !== $content_type && false === stripos( $content_type, 'text/html' ) && false === stripos( $content_type, 'application/xhtml+xml' ) ) {
			throw new \RuntimeException( 'URL Pilar tidak mengembalikan dokumen HTML.' );
		}

		if ( '' === trim( $body ) ) {
			throw new \RuntimeException( 'Konten artikel Pilar kosong.' );
		}

		$parsed = $this->parse( $body );
		$parsed['url']          = $url;
		$parsed['http_status']  = $status_code;
		$parsed['content_type'] = $content_type;
		$parsed['scanned_at']   = current_time( 'mysql', true );

		$keyword = trim( preg_replace( '/\\s+/', ' ', wp_strip_all_tags( $support_keyword ) ) );
		if ( '' !== $keyword ) {
			$parsed['support_keyword'] = $keyword;
			$parsed['keyword_occurrences'] = $this->countOccurrences( $parsed['text'], $keyword );
			$parsed['keyword_tokens_in_pillar'] = $this->countMatchingTokens( $parsed['text'], $keyword );
		}

		return $parsed;
	}

	private function parse( string $html ): array {
		$clean_html = preg_replace( '/<script\\b[^>]*>.*?<\\/script>|<style\\b[^>]*>.*?<\\/style>|<noscript\\b[^>]*>.*?<\\/noscript>/is', ' ', $html );
		$clean_html = is_string( $clean_html ) ? $clean_html : $html;

		$title = '';
		if ( preg_match( '/<title\\b[^>]*>(.*?)<\\/title>/is', $clean_html, $match ) ) {
			$title = $this->normalizeText( $match[1] );
		}

		$headings = array();
		if ( preg_match_all( '/<h([1-3])\\b[^>]*>(.*?)<\\/h\\1>/is', $clean_html, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$text = $this->normalizeText( $match[2] );
				if ( '' !== $text ) {
					$headings[] = array(
						'level' => (int) $match[1],
						'text'  => $text,
					);
				}
			}
		}

		$content_html = $clean_html;
		if ( preg_match( '/<(?:main|article)\\b[^>]*>(.*?)<\\/(?:main|article)>/is', $clean_html, $match ) ) {
			$content_html = $match[1];
		}

		$text = $this->normalizeText( wp_strip_all_tags( $content_html ) );
		$word_count = 0;
		if ( '' !== $text ) {
			$word_count = count( preg_split( '/\\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY ) );
		}

		$h1_count = 0;
		foreach ( $headings as $heading ) {
			if ( 1 === $heading['level'] ) {
				++$h1_count;
			}
		}

		return array(
			'title'       => $title,
			'headings'    => $headings,
			'h1_count'    => $h1_count,
			'text'        => $text,
			'word_count'  => $word_count,
			'content_hash'=> hash( 'sha256', $text ),
		);
	}

	private function normalizeText( string $text ): string {
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$text = preg_replace( '/\\s+/u', ' ', $text );
		return trim( is_string( $text ) ? $text : '' );
	}

	private function countOccurrences( string $text, string $keyword ): int {
		if ( '' === $keyword || '' === $text ) {
			return 0;
		}

		$count = 0;
		preg_match_all( '/(?<!\\pL)' . preg_quote( $keyword, '/' ) . '(?!\\pL)/iu', $text, $matches );
		$count = count( $matches[0] ?? array() );

		return $count;
	}

	private function countMatchingTokens( string $text, string $keyword ): int {
		$text_tokens = $this->tokens( $text );
		$keyword_tokens = $this->tokens( $keyword );
		$matches = 0;

		foreach ( $keyword_tokens as $token ) {
			if ( in_array( $token, $text_tokens, true ) ) {
				++$matches;
			}
		}

		return $matches;
	}

	private function tokens( string $text ): array {
		$tokens = preg_split( '/[^\\pL\\pN]+/u', strtolower( $text ), -1, PREG_SPLIT_NO_EMPTY );
		if ( ! is_array( $tokens ) ) {
			return array();
		}

		return array_values( array_unique( $tokens ) );
	}
}
