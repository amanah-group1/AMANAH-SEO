<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Service;

use Amanah\SEOEngine\Application\Port\WebsiteInventoryInterface;
use Amanah\SEOEngine\Application\Repository\SEOScanRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class SEOScanService {

	private $summary;
	private $repository;
	private $website_inventory;

	public function __construct( DashboardSummaryService $summary, SEOScanRepositoryInterface $repository, WebsiteInventoryInterface $website_inventory ) {
		$this->summary = $summary;
		$this->repository = $repository;
		$this->website_inventory = $website_inventory;
	}

	public function run( string $scan_type = 'full' ): array {
		return $this->runBatch( $scan_type, 0, 25 );
	}

	public function runBatch( string $scan_type = 'full', int $offset = 0, int $batch_size = 25 ): array {
		$scan_type = in_array( $scan_type, array( 'automatic', 'full' ), true ) ? $scan_type : 'full';
		$offset = max( 0, $offset );
		$batch_size = max( 1, $batch_size );
		$summary = $this->summary->build();
		$session = $this->getSession();
		$inventory = $this->website_inventory->collect( $offset, $batch_size );
		$items = isset( $inventory['items'] ) && is_array( $inventory['items'] ) ? $inventory['items'] : array();

		$session_items = isset( $session['items'] ) && is_array( $session['items'] ) ? $session['items'] : array();
		$session_items = array_merge( $session_items, $items );
		$session['items'] = $session_items;
		$session['scan_type'] = $scan_type;
		$session['summary'] = $summary;
		$session['discovered_pages'] = (int) ( $inventory['discovered_pages'] ?? count( $session_items ) );
		$session['next_offset'] = (int) ( $inventory['next_offset'] ?? ( $offset + count( $items ) ) );

		if ( ! empty( $inventory['has_more'] ) ) {
			$this->saveSession( $session );
			return array(
				'scan_type' => $scan_type,
				'status' => 'partial',
				'website_inventory' => array(
					'discovered_pages' => $session['discovered_pages'],
					'total_pages' => count( $session_items ),
					'coverage_complete' => false,
					'coverage_status' => 'partial',
					'next_offset' => $session['next_offset'],
				),
				'processed_pages' => count( $session_items ),
				'total_pages' => $session['discovered_pages'],
				'has_more' => true,
			);
		}

		$live_findings = array();
		foreach ( $session_items as $item ) {
			foreach ( $item['live_seo']['findings'] ?? array() as $finding ) {
				$live_findings[] = $finding;
			}
		}

		$findings = $summary['priority_findings'];
		$all_findings = array_merge( $findings, $live_findings );
		$severity_counts = array( 'critical' => 0, 'warning' => 0, 'info' => 0 );

		foreach ( $all_findings as $finding ) {
			$severity = isset( $finding['severity'] ) ? $finding['severity'] : 'info';
			if ( isset( $severity_counts[ $severity ] ) ) {
				++$severity_counts[ $severity ];
			}
		}

		$website_inventory = array_merge(
			$inventory,
			array(
			'items' => $session_items,
			'total_pages' => count( $session_items ),
			'discovered_pages' => $session['discovered_pages'],
			'coverage_complete' => true,
			'coverage_status' => 'complete',
		)
		);

		$result = array(
			'scan_type' => $scan_type,
			'website_inventory' => $website_inventory,
			'status' => 'completed',
			'completed_at' => current_time( 'mysql', true ),
			'findings_count' => count( $all_findings ),
			'severity_counts' => $severity_counts,
			'summary' => $summary,
			'findings' => $all_findings,
			'live_findings_count' => count( $live_findings ),
		);

		$result['scan_id'] = $this->repository->save( $scan_type, $result );
		$this->clearSession();
		return $result;
	}

	public function hasActiveBatch(): bool {
		$session = $this->getSession();
		return ! empty( $session['items'] ) && isset( $session['next_offset'] );
	}

	public function getBatchProgress(): array {
		$session = $this->getSession();
		return array(
			'active' => $this->hasActiveBatch(),
			'processed_pages' => isset( $session['items'] ) && is_array( $session['items'] ) ? count( $session['items'] ) : 0,
			'total_pages' => (int) ( $session['discovered_pages'] ?? 0 ),
			'next_offset' => (int) ( $session['next_offset'] ?? 0 ),
		);
	}

	public function buildActionCenter( array $result ): array {
		$findings = array();
		foreach ( $result['findings'] ?? array() as $finding ) {
			if ( is_array( $finding ) && 0 === strpos( (string) ( $finding['code'] ?? '' ), 'live_' ) ) {
				$findings[] = $finding;
			}
		}

		$groups = array(
			'technical' => array( 'title' => 'Technical SEO', 'codes' => array( 'live_http_status', 'live_canonical_mismatch', 'live_missing_canonical' ) ),
			'onpage' => array( 'title' => 'On-Page SEO', 'codes' => array( 'live_missing_title', 'live_title_length', 'live_missing_meta_description', 'live_meta_description_length', 'live_missing_h1', 'live_multiple_h1' ) ),
			'content' => array( 'title' => 'Content SEO', 'codes' => array( 'live_images_missing_alt' ) ),
			'links' => array( 'title' => 'Internal Linking', 'codes' => array( 'live_no_internal_links' ) ),
			'indexability' => array( 'title' => 'Indexability', 'codes' => array( 'live_noindex', 'live_nofollow' ) ),
		);

		$action_map = array(
			'live_http_status' => 'Periksa URL dan respons server.',
			'live_missing_title' => 'Tambahkan title yang relevan dan unik.',
			'live_title_length' => 'Sesuaikan panjang title agar lebih efektif.',
			'live_missing_meta_description' => 'Tambahkan meta description yang relevan.',
			'live_meta_description_length' => 'Sesuaikan panjang meta description.',
			'live_missing_h1' => 'Tambahkan satu H1 utama yang jelas.',
			'live_multiple_h1' => 'Review struktur heading dan pastikan H1 utama jelas.',
			'live_canonical_mismatch' => 'Periksa apakah canonical menunjuk ke URL yang benar.',
			'live_missing_canonical' => 'Tambahkan canonical bila halaman membutuhkannya.',
			'live_noindex' => 'Pastikan noindex memang disengaja.',
			'live_nofollow' => 'Pastikan nofollow memang disengaja.',
			'live_no_internal_links' => 'Tambahkan internal link yang relevan.',
			'live_images_missing_alt' => 'Tambahkan alt text pada gambar yang relevan.',
		);

		$issues = array();
		foreach ( $findings as $finding ) {
			$code = (string) ( $finding['code'] ?? '' );
			$group_key = '';
			$group_title = '';
			foreach ( $groups as $key => $group ) {
				if ( in_array( $code, $group['codes'], true ) ) {
					$group_key = $key;
					$group_title = $group['title'];
					break;
				}
			}
			if ( '' === $group_key ) {
				continue;
			}
			$severity = (string) ( $finding['severity'] ?? 'info' );
			$weight = 'critical' === $severity ? 3 : ( 'warning' === $severity ? 2 : 1 );
			$issues[] = array(
				'group' => $group_key,
				'group_title' => $group_title,
				'code' => $code,
				'severity' => $severity,
				'priority_weight' => $weight,
				'url' => (string) ( $finding['url'] ?? '' ),
				'message' => (string) ( $finding['message'] ?? '' ),
				'action' => $action_map[ $code ] ?? 'Review halaman dan perbaiki masalah SEO yang terdeteksi.',
			);
		}

		usort( $issues, static function ( $first, $second ) {
			if ( $first['priority_weight'] !== $second['priority_weight'] ) {
				return $second['priority_weight'] <=> $first['priority_weight'];
			}
			return strcmp( $first['url'], $second['url'] );
		} );

		$group_counts = array();
		foreach ( $issues as $issue ) {
			$key = $issue['group'];
			if ( ! isset( $group_counts[ $key ] ) ) {
				$group_counts[ $key ] = array( 'title' => $issue['group_title'], 'critical' => 0, 'warning' => 0, 'info' => 0, 'urls' => array() );
			}
			if ( isset( $group_counts[ $key ][ $issue['severity'] ] ) ) {
				++$group_counts[ $key ][ $issue['severity'] ];
			}
			if ( '' !== $issue['url'] ) {
				$group_counts[ $key ]['urls'][ $issue['url'] ] = true;
			}
		}
		foreach ( $group_counts as $key => $group ) {
			$group_counts[ $key ]['affected_pages'] = count( $group['urls'] );
			unset( $group_counts[ $key ]['urls'] );
		}

		return array(
			'issues' => $issues,
			'groups' => $group_counts,
			'total_issues' => count( $issues ),
			'critical' => count( array_filter( $issues, static function ( $issue ) { return 'critical' === $issue['severity']; } ) ),
			'warning' => count( array_filter( $issues, static function ( $issue ) { return 'warning' === $issue['severity']; } ) ),
			'info' => count( array_filter( $issues, static function ( $issue ) { return 'info' === $issue['severity']; } ) ),
		);
	}

	public function getHistory( int $limit = 10 ): array {
		return $this->repository->findRecent( max( 1, min( 20, $limit ) ) );
	}

	public function compareScans( array $current, array $previous ): array {
		$current_result = isset( $current['result'] ) && is_array( $current['result'] ) ? $current['result'] : $current;
		$previous_result = isset( $previous['result'] ) && is_array( $previous['result'] ) ? $previous['result'] : $previous;
		$current_inventory = isset( $current_result['website_inventory'] ) && is_array( $current_result['website_inventory'] ) ? $current_result['website_inventory'] : array();
		$previous_inventory = isset( $previous_result['website_inventory'] ) && is_array( $previous_result['website_inventory'] ) ? $previous_result['website_inventory'] : array();
		$current_counts = isset( $current_result['severity_counts'] ) && is_array( $current_result['severity_counts'] ) ? $current_result['severity_counts'] : array();
		$previous_counts = isset( $previous_result['severity_counts'] ) && is_array( $previous_result['severity_counts'] ) ? $previous_result['severity_counts'] : array();
		return array(
			'findings_delta' => (int) ( $current_result['findings_count'] ?? 0 ) - (int) ( $previous_result['findings_count'] ?? 0 ),
			'pages_delta' => (int) ( $current_inventory['total_pages'] ?? 0 ) - (int) ( $previous_inventory['total_pages'] ?? 0 ),
			'critical_delta' => (int) ( $current_counts['critical'] ?? 0 ) - (int) ( $previous_counts['critical'] ?? 0 ),
			'warning_delta' => (int) ( $current_counts['warning'] ?? 0 ) - (int) ( $previous_counts['warning'] ?? 0 ),
			'info_delta' => (int) ( $current_counts['info'] ?? 0 ) - (int) ( $previous_counts['info'] ?? 0 ),
		);
	}

	public function getLatest(): ?array {
		return $this->repository->findLatest();
	}

	private function getSession(): array {
		$value = get_option( 'amanah_seo_scan_batch_session', array() );
		return is_array( $value ) ? $value : array();
	}

	private function saveSession( array $session ): void {
		update_option( 'amanah_seo_scan_batch_session', $session, false );
	}

	private function clearSession(): void {
		delete_option( 'amanah_seo_scan_batch_session' );
	}
}
