<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\WordPress;

defined( 'ABSPATH' ) || exit;

final class LivePageSEOAnalyzer {

	public function analyze( string $url, string $body, int $http_status ): array {
		$title = $this->extractFirst( $body, '/<title[^>]*>\\s*(.*?)\\s*<\\/title>/is' );
		$description = $this->extractMeta( $body, 'description' );
		$canonical = $this->extractCanonical( $body );
		$robots = $this->extractMeta( $body, 'robots' );
		$h1_count = $this->countTags( $body, 'h1' );
		$h2_count = $this->countTags( $body, 'h2' );
		$image_count = $this->countTags( $body, 'img' );
		$images_without_alt = $this->countImagesWithoutAlt( $body );
		$internal_links = $this->countInternalLinks( $body );
		$findings = array();
		$title_length = $this->stringLength( $title );
		$description_length = $this->stringLength( $description );
		$robots_tokens = $this->parseRobots( $robots );

		if ( 200 !== $http_status ) {
			$findings[] = array(
				'code' => 'live_http_status',
				'severity' => 'critical',
				'url' => $url,
				'message' => 'Live URL returned HTTP status ' . $http_status . '.',
			);
		}

		if ( '' === $title ) {
			$findings[] = array(
				'code' => 'live_missing_title',
				'severity' => 'warning',
				'url' => $url,
				'message' => 'Live page is missing a title element.',
			);
		}

		if ( '' !== $title && ( $title_length < 30 || $title_length > 60 ) ) {
			$findings[] = array(
				'code' => 'live_title_length',
				'severity' => 'info',
				'url' => $url,
				'message' => 'Live page title is ' . $title_length . ' characters; recommended range is 30–60.',
			);
		}

		if ( '' === $description ) {
			$findings[] = array(
				'code' => 'live_missing_meta_description',
				'severity' => 'warning',
				'url' => $url,
				'message' => 'Live page is missing a meta description.',
			);
		}

		if ( '' !== $description && ( $description_length < 120 || $description_length > 160 ) ) {
			$findings[] = array(
				'code' => 'live_meta_description_length',
				'severity' => 'info',
				'url' => $url,
				'message' => 'Live page meta description is ' . $description_length . ' characters; recommended range is 120–160.',
			);
		}

		if ( 0 === $h1_count ) {
			$findings[] = array(
				'code' => 'live_missing_h1',
				'severity' => 'warning',
				'url' => $url,
				'message' => 'Live page has no H1 heading.',
			);
		} elseif ( $h1_count > 1 ) {
			$findings[] = array(
				'code' => 'live_multiple_h1',
				'severity' => 'info',
				'url' => $url,
				'message' => 'Live page has ' . $h1_count . ' H1 headings.',
			);
		}

		if ( '' !== $canonical && ! $this->sameUrl( $canonical, $url ) ) {
			$findings[] = array(
				'code' => 'live_canonical_mismatch',
				'severity' => 'info',
				'url' => $url,
				'message' => 'Live page canonical points to a different URL: ' . $canonical,
			);
		}

		if ( '' === $canonical ) {
			$findings[] = array(
				'code' => 'live_missing_canonical',
				'severity' => 'info',
				'url' => $url,
				'message' => 'Live page does not expose a canonical link element.',
			);
		}

		if ( in_array( 'noindex', $robots_tokens, true ) ) {
			$findings[] = array(
				'code' => 'live_noindex',
				'severity' => 'warning',
				'url' => $url,
				'message' => 'Live page is marked noindex by its robots meta directive.',
			);
		}

		if ( in_array( 'nofollow', $robots_tokens, true ) ) {
			$findings[] = array(
				'code' => 'live_nofollow',
				'severity' => 'info',
				'url' => $url,
				'message' => 'Live page is marked nofollow by its robots meta directive.',
			);
		}

		if ( 0 === $internal_links ) {
			$findings[] = array(
				'code' => 'live_no_internal_links',
				'severity' => 'info',
				'url' => $url,
				'message' => 'Live page contains no detected internal links.',
			);
		}

		if ( $image_count > 0 && $images_without_alt > 0 ) {
			$findings[] = array(
				'code' => 'live_images_missing_alt',
				'severity' => 'warning',
				'url' => $url,
				'message' => $images_without_alt . ' of ' . $image_count . ' images are missing alt attributes.',
			);
		}

		return array(
			'title' => $title,
			'meta_description' => $description,
			'canonical' => $canonical,
			'robots' => $robots,
			'h1_count' => $h1_count,
			'h2_count' => $h2_count,
			'image_count' => $image_count,
			'images_without_alt' => $images_without_alt,
			'internal_links' => $internal_links,
			'findings' => $findings,
		);
	}

	private function stringLength( string $value ): int {
		return function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
	}

	private function parseRobots( string $robots ): array {
		if ( '' === $robots ) {
			return array();
		}
		$tokens = array();
		foreach ( preg_split( '/[,\\s]+/', strtolower( $robots ) ) as $token ) {
			$token = trim( $token );
			if ( '' !== $token ) {
				$tokens[] = $token;
			}
		}
		return array_values( array_unique( $tokens ) );
	}

	private function sameUrl( string $first, string $second ): bool {
		$normalize = static function ( string $value ): string {
			$value = esc_url_raw( $value );
			$value = rtrim( $value, '/' );
			return strtolower( $value );
		};
		return $normalize( $first ) === $normalize( $second );
	}

	private function extractFirst( string $body, string $pattern ): string {
		if ( ! preg_match( $pattern, $body, $matches ) ) {
			return '';
		}

		return trim( html_entity_decode( wp_strip_all_tags( $matches[1] ) ) );
	}

	private function extractMeta( string $body, string $name ): string {
		if ( ! preg_match_all( '/<meta\\b[^>]*>/i', $body, $matches ) ) {
			return '';
		}

		foreach ( $matches[0] as $tag ) {
			if ( preg_match( '/\\bname\\s*=\\s*["\\x27]' . preg_quote( $name, '/' ) . '["\\x27]/i', $tag ) && preg_match( '/\\bcontent\\s*=\\s*["\\x27](.*?)["\\x27]/is', $tag, $content ) ) {
				return trim( html_entity_decode( wp_strip_all_tags( $content[1] ) ) );
			}
		}

		return '';
	}

	private function extractCanonical( string $body ): string {
		if ( ! preg_match_all( '/<link\\b[^>]*>/i', $body, $matches ) ) {
			return '';
		}

		foreach ( $matches[0] as $tag ) {
			if ( preg_match( '/\\brel\\s*=\\s*["\\x27]canonical["\\x27]/i', $tag ) && preg_match( '/\\bhref\\s*=\\s*["\\x27](.*?)["\\x27]/is', $tag, $href ) ) {
				return esc_url_raw( html_entity_decode( trim( $href[1] ) ) );
			}
		}

		return '';
	}

	private function countTags( string $body, string $tag ): int {
		return preg_match_all( '/<' . preg_quote( $tag, '/' ) . '\\b[^>]*>/i', $body, $matches ) ?: 0;
	}

	private function countImagesWithoutAlt( string $body ): int {
		$count = 0;
		if ( ! preg_match_all( '/<img\\b[^>]*>/i', $body, $matches ) ) {
			return 0;
		}

		foreach ( $matches[0] as $tag ) {
			if ( ! preg_match( '/\\balt\\s*=\\s*["\\x27]/i', $tag ) ) {
				++$count;
			}
		}

		return $count;
	}

	private function countInternalLinks( string $body ): int {
		$home_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
		if ( ! is_string( $home_host ) || ! preg_match_all( '/<a\\b[^>]*href\\s*=\\s*["\\x27](.*?)["\\x27]/is', $body, $matches ) ) {
			return 0;
		}

		$count = 0;
		foreach ( $matches[1] as $href ) {
			$href = html_entity_decode( trim( $href ) );
			if ( 0 === strpos( $href, '/' ) || strtolower( (string) wp_parse_url( $href, PHP_URL_HOST ) ) === strtolower( $home_host ) ) {
				++$count;
			}
		}

		return $count;
	}
}
