<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Service;

use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\SEOScanRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class SEOIntelligenceService {

	private $keywords;
	private $owner_urls;
	private $scans;

	public function __construct(
		KeywordRepositoryInterface $keywords,
		OwnerUrlRepositoryInterface $owner_urls,
		SEOScanRepositoryInterface $scans
	) {
		$this->keywords  = $keywords;
		$this->owner_urls = $owner_urls;
		$this->scans      = $scans;
	}

	public function build(): array {
		$latest = $this->scans->findLatest();
		$result = is_array( $latest['result'] ?? null ) ? $latest['result'] : array();
		$items  = is_array( $result['website_inventory']['items'] ?? null ) ? $result['website_inventory']['items'] : array();
		$findings = is_array( $result['findings'] ?? null ) ? $result['findings'] : array();

		$keyword_records = $this->keywords->findAll( 100 );
		$owner_records   = $this->owner_urls->findAll( 100 );
		$owner_map       = array();

		foreach ( $owner_records as $owner ) {
			$owner_map[ (int) $owner->getId() ] = $owner;
		}

		$url_map = array();
		foreach ( $keyword_records as $keyword ) {
			$owner_id = $keyword->getOwnerUrlId();
			$url = null !== $owner_id && isset( $owner_map[ $owner_id ] ) ? $owner_map[ $owner_id ]->getUrl() : '';
			$key = $this->normalizeUrl( $url );
			if ( '' !== $key ) {
				$url_map[ $key ][] = $keyword->getPhrase();
			}
		}

		$technical = 0;
		$onpage = 0;
		$content = 0;
		$links = 0;
		$indexability = 0;
		$issues = array();

		foreach ( $findings as $finding ) {
			$code = (string) ( $finding['code'] ?? '' );
			if ( 0 !== strpos( $code, 'live_' ) ) {
				continue;
			}
			$group = $this->groupForCode( $code );
			if ( 'technical' === $group ) {
				++$technical;
			} elseif ( 'onpage' === $group ) {
				++$onpage;
			} elseif ( 'content' === $group ) {
				++$content;
			} elseif ( 'links' === $group ) {
				++$links;
			} elseif ( 'indexability' === $group ) {
				++$indexability;
			}
			$issues[] = array(
				'group' => $group,
				'severity' => (string) ( $finding['severity'] ?? 'info' ),
				'code' => $code,
				'url' => (string) ( $finding['url'] ?? '' ),
				'message' => (string) ( $finding['message'] ?? '' ),
				'action' => $this->actionForCode( $code ),
			);
		}

		usort( $issues, static function ( $a, $b ) {
			$weight = array( 'critical' => 3, 'warning' => 2, 'info' => 1 );
			return ( $weight[ $b['severity'] ] ?? 0 ) <=> ( $weight[ $a['severity'] ] ?? 0 );
		} );

		$keyword_gaps = array();
		foreach ( $keyword_records as $keyword ) {
			if ( null === $keyword->getOwnerUrlId() ) {
				$keyword_gaps[] = array(
					'keyword' => $keyword->getPhrase(),
					'action' => 'Tentukan Owner URL untuk keyword ini.',
				);
			}
		}

		$url_opportunities = array();
		foreach ( $items as $item ) {
			$url = (string) ( $item['url'] ?? '' );
			$key = $this->normalizeUrl( $url );
			$signals = is_array( $item['live_seo']['findings'] ?? null ) ? $item['live_seo']['findings'] : array();
			$url_opportunities[] = array(
				'url' => $url,
				'keywords' => $url_map[ $key ] ?? array(),
				'issues' => count( $signals ),
				'edit_url' => $this->editUrlFor( $url ),
			);
		}

		$recommendations = array();
		foreach ( array_slice( $issues, 0, 30 ) as $issue ) {
			$recommendations[] = array(
				'priority' => $issue['severity'],
				'url' => $issue['url'],
				'recommendation' => $issue['action'],
			);
		}
		foreach ( array_slice( $keyword_gaps, 0, 10 ) as $gap ) {
			$recommendations[] = array(
				'priority' => 'warning',
				'url' => '',
				'recommendation' => $gap['keyword'] . ': ' . $gap['action'],
			);
		}

		$history = $this->scans->findRecent( 8 );
		$comparison = array();
		if ( count( $history ) >= 2 ) {
			$current = $history[0]['result'];
			$previous = $history[1]['result'];
			$comparison = array(
				'findings_delta' => (int) ( $current['findings_count'] ?? 0 ) - (int) ( $previous['findings_count'] ?? 0 ),
				'pages_delta' => (int) ( $current['website_inventory']['total_pages'] ?? 0 ) - (int) ( $previous['website_inventory']['total_pages'] ?? 0 ),
				'last_scan' => (string) ( $history[0]['completed_at'] ?? '' ),
			);
		}

		return array(
			'has_scan' => null !== $latest,
			'last_scan' => (string) ( $latest['completed_at'] ?? '' ),
			'pages' => count( $items ),
			'discovered_pages' => (int) ( $result['website_inventory']['discovered_pages'] ?? count( $items ) ),
			'issues' => $issues,
			'issue_counts' => array(
				'technical' => $technical,
				'onpage' => $onpage,
				'content' => $content,
				'links' => $links,
				'indexability' => $indexability,
			),
			'keyword_total' => count( $keyword_records ),
			'keyword_gaps' => $keyword_gaps,
			'url_opportunities' => $url_opportunities,
			'recommendations' => $recommendations,
			'comparison' => $comparison,
			'history' => $history,
		);
	}

	private function groupForCode( string $code ): string {
		if ( in_array( $code, array( 'live_http_status', 'live_canonical_mismatch', 'live_missing_canonical' ), true ) ) {
			return 'technical';
		}
		if ( in_array( $code, array( 'live_missing_title', 'live_title_length', 'live_missing_meta_description', 'live_meta_description_length', 'live_missing_h1', 'live_multiple_h1' ), true ) ) {
			return 'onpage';
		}
		if ( 'live_images_missing_alt' === $code ) {
			return 'content';
		}
		if ( 'live_no_internal_links' === $code ) {
			return 'links';
		}
		if ( in_array( $code, array( 'live_noindex', 'live_nofollow' ), true ) ) {
			return 'indexability';
		}
		return 'onpage';
	}

	private function actionForCode( string $code ): string {
		$map = array(
			'live_http_status' => 'Periksa URL dan respons server.',
			'live_missing_title' => 'Tambahkan title unik dan relevan.',
			'live_title_length' => 'Sesuaikan panjang title.',
			'live_missing_meta_description' => 'Tambahkan meta description yang relevan.',
			'live_meta_description_length' => 'Sesuaikan panjang meta description.',
			'live_missing_h1' => 'Tambahkan satu H1 utama yang jelas.',
			'live_multiple_h1' => 'Review struktur heading dan tetapkan H1 utama.',
			'live_canonical_mismatch' => 'Periksa canonical dan pastikan menunjuk ke URL utama.',
			'live_missing_canonical' => 'Tambahkan canonical bila halaman membutuhkannya.',
			'live_noindex' => 'Pastikan noindex memang disengaja.',
			'live_nofollow' => 'Pastikan nofollow memang disengaja.',
			'live_no_internal_links' => 'Tambahkan internal link yang relevan.',
			'live_images_missing_alt' => 'Tambahkan alt text pada gambar yang relevan.',
		);
		return $map[ $code ] ?? 'Review masalah SEO pada halaman ini.';
	}

	private function normalizeUrl( string $url ): string {
		if ( '' === $url ) {
			return '';
		}
		$url = strtok( $url, '#' );
		return rtrim( strtolower( (string) $url ), '/' );
	}

	private function editUrlFor( string $url ): string {
		$post_id = $url ? url_to_postid( $url ) : 0;
		return $post_id > 0 ? get_edit_post_link( $post_id, 'raw' ) : '';
	}
}
