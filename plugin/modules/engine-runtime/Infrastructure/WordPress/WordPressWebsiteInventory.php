<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\WordPress;

use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;
use Amanah\SEOEngine\Application\Port\WebsiteInventoryInterface;

defined( 'ABSPATH' ) || exit;

final class WordPressWebsiteInventory implements WebsiteInventoryInterface {

	private const DEFAULT_BATCH_SIZE = 25;
	private const MAX_SITEMAPS = 10;

	private $http;
	private $analyzer;

	public function __construct( HttpClientInterface $http, ?LivePageSEOAnalyzer $analyzer = null ) {
		$this->http = $http;
		$this->analyzer = null !== $analyzer ? $analyzer : new LivePageSEOAnalyzer();
	}

	public function collect( int $offset = 0, int $limit = self::DEFAULT_BATCH_SIZE ): array {
		$home_url = home_url( '/' );
		$home_response = $this->request( $home_url );
		$robots_response = $this->request( home_url( '/robots.txt' ) );
		$sitemap_urls = $this->discoverSitemaps( $robots_response );
		$discovered_urls = array();

		if ( $this->isHttpResponse( $home_response ) ) {
			$discovered_urls[] = $home_url;
		}

		foreach ( $sitemap_urls as $sitemap_url ) {
			$this->collectSitemapUrls( $sitemap_url, $discovered_urls, array(), 0 );
		}

		$limit = max( 1, $limit );
		$offset = max( 0, $offset );
		$batch_urls = array_slice( $discovered_urls, $offset, $limit );
		$items = array();

		foreach ( $batch_urls as $url ) {
			$response = $this->sameUrl( $url, $home_url ) ? $home_response : $this->request( $url );

			if ( $this->isHttpResponse( $response ) ) {
				$items[] = $this->buildItem( $url, $response );
			}
		}

		$discovered_page_count = count( $discovered_urls );
		$next_offset = $offset + count( $batch_urls );

		return array(
			'status'            => 'completed',
			'source'            => 'live_website',
			'scanned_at'        => current_time( 'mysql', true ),
			'home_url'          => $home_url,
			'sitemaps'          => $sitemap_urls,
			'total_pages'       => count( $items ),
			'discovered_pages'  => $discovered_page_count,
			'offset'            => $offset,
			'batch_size'        => $limit,
			'next_offset'       => $next_offset,
			'has_more'          => $next_offset < $discovered_page_count,
			'coverage_complete' => $next_offset >= $discovered_page_count,
			'coverage_status'   => $next_offset >= $discovered_page_count ? 'complete' : 'partial',
			'items'             => $items,
		);
	}

	private function collectSitemapUrls( string $sitemap_url, array &$urls, array $visited, int $depth ): void {
		if ( $depth > 2 || in_array( $sitemap_url, $visited, true ) ) {
			return;
		}

		$visited[] = $sitemap_url;
		$response = $this->request( $sitemap_url );

		if ( ! $this->isSuccessfulResponse( $response ) ) {
			return;
		}

		foreach ( $this->extractLocations( wp_remote_retrieve_body( $response ) ) as $location ) {
			if ( $this->looksLikeSitemap( $location ) ) {
				$this->collectSitemapUrls( $location, $urls, $visited, $depth + 1 );
				continue;
			}

			if ( $this->isSameHost( $location ) && ! in_array( $location, $urls, true ) ) {
				$urls[] = $location;
			}
		}
	}

	private function discoverSitemaps( $robots_response ): array {
		$sitemaps = array();

		if ( $this->isSuccessfulResponse( $robots_response ) ) {
			$body = wp_remote_retrieve_body( $robots_response );

			if ( preg_match_all( '/^\\s*Sitemap:\\s*(.+?)\\s*$/im', $body, $matches ) ) {
				foreach ( $matches[1] as $url ) {
					$url = esc_url_raw( trim( $url ) );

					if ( '' !== $url && $this->isSameHost( $url ) ) {
						$sitemaps[] = $url;
					}
				}
			}
		}

		foreach ( array( home_url( '/wp-sitemap.xml' ), home_url( '/sitemap_index.xml' ), home_url( '/sitemap.xml' ) ) as $candidate ) {
			if ( ! in_array( $candidate, $sitemaps, true ) ) {
				$sitemaps[] = $candidate;
			}
		}

		return array_slice( $sitemaps, 0, self::MAX_SITEMAPS );
	}

	private function extractLocations( string $body ): array {
		if ( ! preg_match_all( '/<loc>\\s*(.*?)\\s*<\\/loc>/is', $body, $matches ) ) {
			return array();
		}

		$locations = array();

		foreach ( $matches[1] as $location ) {
			$location = esc_url_raw( html_entity_decode( trim( wp_strip_all_tags( $location ) ) ) );

			if ( '' !== $location && ! in_array( $location, $locations, true ) ) {
				$locations[] = $location;
			}
		}

		return $locations;
	}

	private function buildItem( string $url, $response ): array {
		$body = wp_remote_retrieve_body( $response );
		$title = '';

		if ( preg_match( '/<title[^>]*>\\s*(.*?)\\s*<\\/title>/is', $body, $matches ) ) {
			$title = trim( html_entity_decode( wp_strip_all_tags( $matches[1] ) ) );
		}

		return array(
			'url' => $url,
			'http_status' => (int) wp_remote_retrieve_response_code( $response ),
			'title' => $title,
			'content_type' => (string) wp_remote_retrieve_header( $response, 'content-type' ),
			'live_seo' => $this->analyzer->analyze( $url, $body, (int) wp_remote_retrieve_response_code( $response ) ),
		);
	}

	private function request( string $url ) {
		return $this->http->request( 'GET', $url, array(
			'timeout' => 10,
			'redirection' => 3,
			'user-agent' => 'Amanah SEO Engine Live Website Scanner',
		) );
	}

	private function isHttpResponse( $response ): bool {
		return ! is_wp_error( $response ) && 0 < (int) wp_remote_retrieve_response_code( $response );
	}

	private function isSuccessfulResponse( $response ): bool {
		return $this->isHttpResponse( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response );
	}

	private function isSameHost( string $url ): bool {
		$home_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
		$url_host = wp_parse_url( $url, PHP_URL_HOST );

		return is_string( $home_host ) && is_string( $url_host ) && strtolower( $home_host ) === strtolower( $url_host );
	}

	private function sameUrl( string $first, string $second ): bool {
		$normalize = static function ( string $value ): string {
			return strtolower( rtrim( esc_url_raw( $value ), '/' ) );
		};

		return $normalize( $first ) === $normalize( $second );
	}

	private function looksLikeSitemap( string $url ): bool {
		$path = wp_parse_url( $url, PHP_URL_PATH );

		return is_string( $path ) && ( false !== stripos( $path, 'sitemap' ) || preg_match( '/\\.xml$/i', $path ) );
	}
}
