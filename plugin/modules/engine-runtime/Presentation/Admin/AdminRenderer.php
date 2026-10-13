<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;
use Amanah\SEOEngine\Core\Bootstrap\Plugin;

defined( 'ABSPATH' ) || exit;

final class AdminRenderer {

	private $configuration;

	public function __construct( Configuration $configuration ) {
		$this->configuration = $configuration;
	}

	public function render( string $screen ): void {
		$definition = AdminScreenRegistry::get( $screen );

		if ( null === $definition ) {
			wp_die( esc_html__( 'The requested screen is not available.', 'amanah-seo-engine' ), 404 );
		}

		$this->renderHeader( $definition['title'] );

		if ( 'dashboard' === $screen ) {
			$this->renderDashboard();
		} elseif ( 'live-scan' === $screen ) {
			$this->renderLiveWebsite();
		} elseif ( 'settings' === $screen ) {
			$this->renderSettings();
		} elseif ( 'ai-configuration' === $screen ) {
			$this->renderAIConfiguration();
		} elseif ( in_array( $screen, array( 'google-search-console', 'google-analytics', 'google-tag-manager' ), true ) ) {
			$this->renderGoogleIntegration( $screen );
		} elseif ( in_array( $screen, array( 'seo-issues', 'recommendations', 'monitoring-performance', 'monitoring-keywords', 'monitoring-site-health', 'internal-links', 'orphan-pages', 'cannibalization' ), true ) ) {
			$this->renderIntelligenceScreen( $screen );
		} else {
			$this->renderPlaceholder( $definition );
		}
	}

	private function renderHeader( string $title ): void {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html( $title ) . '</h1>';
	}

	private function renderDashboard(): void {
		$services = Plugin::boot();
		$summary_service = $services->get( 'dashboard_summary_service' );
		$summary = $summary_service->build();
		$intelligence_service = $services->get( 'seo_intelligence_service' );
		$intelligence = $intelligence_service->build();


		$keyword_total = (int) $summary['keywords']['total'];
		$mapped = (int) $summary['keywords']['mapped'];
		$unassigned = (int) $summary['keywords']['unassigned'];
		$strategy_total = (int) $summary['strategy']['total'];
		$strategy_covered = (int) $summary['strategy']['covered'];
		$findings = $summary['priority_findings'];

		echo '<div class="amanah-seo-engine-dashboard" style="max-width:1200px;">';
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">' . esc_html__( 'SEO Overview', 'amanah-seo-engine' ) . '</h2>';
		echo '<p><strong>' . esc_html__( 'Keywords:', 'amanah-seo-engine' ) . '</strong> ' . esc_html( (string) $keyword_total ) . ' &nbsp; ';
		echo '<strong>' . esc_html__( 'Mapped:', 'amanah-seo-engine' ) . '</strong> ' . esc_html( (string) $mapped ) . ' &nbsp; ';
		echo '<strong>' . esc_html__( 'Unassigned:', 'amanah-seo-engine' ) . '</strong> ' . esc_html( (string) $unassigned ) . '</p>';
		echo '<p><strong>' . esc_html__( 'Strategy coverage:', 'amanah-seo-engine' ) . '</strong> ' . esc_html( $strategy_total > 0 ? (string) round( ( $strategy_covered / $strategy_total ) * 100 ) . '%' : '0%' ) . '</p>';
		echo '</div>';

		echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px;">';
		$this->renderCard( 'Keywords', sprintf( '%d total · %d mapped · %d unassigned', $keyword_total, $mapped, $unassigned ) );
		$this->renderCard( 'SEO Strategy', sprintf( '%d of %d keywords covered', $strategy_covered, $strategy_total ) );
		$this->renderCard( 'Content Pipeline', sprintf( 'Plan %d · Brief %d · Blueprint %d · Content %d', $summary['content_pipeline']['plan'], $summary['content_pipeline']['brief'], $summary['content_pipeline']['blueprint'], $summary['content_pipeline']['content'] ) );
		$this->renderCard( 'Quality', sprintf( 'Checked %d · Passed %d · Warning %d · Failed %d', $summary['quality']['checked'], $summary['quality']['passed'], $summary['quality']['warning'], $summary['quality']['failed'] ) );
		$active_ai_provider = sanitize_key( (string) get_option( 'amanah_seo_engine_active_ai_provider', (string) $this->configuration->get( 'ai_provider', 'gemini' ) ) );
		$ai_status = $this->configuration->get( 'ai_enabled', false ) ? strtoupper( $active_ai_provider ) . ' aktif.' : 'AI provider belum aktif.';
		$this->renderCard( 'AI Status', $ai_status );
		$this->renderCard( 'SEO Intelligence', $intelligence['has_scan'] ? sprintf( '%d live issues · %d keyword gaps', count( $intelligence['issues'] ), count( $intelligence['keyword_gaps'] ) ) : 'Run Live SEO Scan to analyze the website.' );
		echo '</div>';

		echo '<div style="background:#f6f7f7;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">' . esc_html__( 'Live Website Data', 'amanah-seo-engine' ) . '</h2>';
		if ( $intelligence['has_scan'] ) {
			echo '<p><strong>Source:</strong> Live Website Scan</p>';
			echo '<p><strong>Last scan:</strong> ' . esc_html( $intelligence['last_scan'] ) . ' &nbsp; <strong>Pages:</strong> ' . esc_html( (string) $intelligence['pages'] ) . ' / ' . esc_html( (string) $intelligence['discovered_pages'] ) . '</p>';
			echo '<p class="description">Data website menjadi sumber SEO Intelligence. Jalankan atau lihat detail scan di menu Live Website.</p>';
		} else {
			echo '<p>Belum ada data Live Website. Jalankan scan untuk mulai menganalisis website nyata.</p>';
		}
		echo '</div>';

		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">' . esc_html__( 'Priority Actions', 'amanah-seo-engine' ) . '</h2>';
		if ( empty( $findings ) ) {
			echo '<p>' . esc_html__( 'No priority findings were detected from the available SEO data.', 'amanah-seo-engine' ) . '</p>';
		} else {
			echo '<ol>';
			foreach ( array_slice( $findings, 0, 8 ) as $finding ) {
				echo '<li><strong>' . esc_html( $finding['entity'] ) . '</strong> — ' . esc_html( $finding['message'] ) . ' <em>' . esc_html( $finding['action'] ) . '</em></li>';
			}
			echo '</ol>';
		}
		echo '</div>';


		echo '</div>';
	}

	private function renderLiveWebsite(): void {
		$services = Plugin::boot();
		$scan_service = $services->get( 'seo_scan_service' );
		$latest = $scan_service->getLatest();
		$batch_progress = $scan_service->getBatchProgress();

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_seo_run_scan'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You are not allowed to run an SEO scan.', 'amanah-seo-engine' ) );
			}
			check_admin_referer( 'amanah_seo_run_scan', 'amanah_seo_scan_nonce' );
			try {
				$scan_service->runBatch( 'full', (int) $batch_progress['next_offset'], 25 );
				$latest = $scan_service->getLatest();
				$batch_progress = $scan_service->getBatchProgress();
				if ( $batch_progress['active'] ) {
					echo '<div class="notice notice-info is-dismissible"><p>' . esc_html__( 'Live Website scan batch completed. Continue the scan to process the remaining live pages.', 'amanah-seo-engine' ) . '</p></div>';
				} else {
					echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Live Website scan completed successfully.', 'amanah-seo-engine' ) . '</p></div>';
				}
			} catch ( \Throwable $e ) {
				echo '<div class="notice notice-error"><p>' . esc_html( $e->getMessage() ) . '</p></div>';
			}
		}

		echo '<div style="max-width:1200px;">';
		echo '<div style="background:#eef6ff;border:1px solid #b8d7f5;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">' . esc_html__( 'Live Website', 'amanah-seo-engine' ) . '</h2>';
		echo '<p>Area ini hanya berisi data yang diambil langsung dari website melalui Live Website Scan.</p>';
		echo '<p class="description">SEO Engine menggunakan hasil scan ini sebagai sumber analisis. Data keyword, strategy, content, dan recommendation tidak ditampilkan sebagai bagian dari scan.</p>';
		echo '<form method="post">';
		wp_nonce_field( 'amanah_seo_run_scan', 'amanah_seo_scan_nonce' );
		echo '<button type="submit" name="amanah_seo_run_scan" value="1" class="button button-primary">' . esc_html( $batch_progress['active'] ? 'Continue Live Website Scan' : 'Scan Live Website' ) . '</button>';
		echo '</form>';
		echo '</div>';

		if ( null === $latest ) {
			echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;"><p>No Live Website scan has been completed yet.</p></div>';
		} else {
			$result = isset( $latest['result'] ) && is_array( $latest['result'] ) ? $latest['result'] : array();
			echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
			echo '<p><strong>Last Live Website scan:</strong> ' . esc_html( (string) $latest['completed_at'] ) . '</p>';
			echo '<p><strong>Findings:</strong> ' . esc_html( (string) ( $result['findings_count'] ?? $latest['findings_count'] ?? 0 ) ) . '</p>';
			$inventory = isset( $result['website_inventory'] ) && is_array( $result['website_inventory'] ) ? $result['website_inventory'] : array();
			$inventory_count = isset( $inventory['total_pages'] ) ? (int) $inventory['total_pages'] : 0;
			$live_findings = array();
			foreach ( $result['findings'] ?? array() as $finding ) {
				if ( is_array( $finding ) && 0 === strpos( (string) ( $finding['code'] ?? '' ), 'live_' ) ) {
					$live_findings[] = $finding;
				}
			}
			$discovered_count = isset( $inventory['discovered_pages'] ) ? (int) $inventory['discovered_pages'] : $inventory_count;
			$coverage_complete = ! empty( $inventory['coverage_complete'] );
			$this->renderLiveScanSummary( $live_findings, $discovered_count, $inventory_count, $coverage_complete );
			$this->renderLiveScanActionCenter( $scan_service, $result );
			$this->renderLiveScanHistory( $scan_service );
			echo '</div>';
		}
		echo '</div>';
	}

	private function renderLiveScanSummary( array $findings, int $discovered_count, int $scanned_count, bool $coverage_complete ): void {
		$groups = $this->groupLiveFindings( $findings );
		$needs_attention = count( array_filter( $groups, static function ( $group ) { return 'good' !== $group['status']; } ) );
		$good = count( array_filter( $groups, static function ( $group ) { return 'good' === $group['status']; } ) );

		echo '<div style="background:#f6f7f7;border:1px solid #dcdcde;padding:16px;margin:16px 0;">';
		echo '<h3 style="margin-top:0;">' . esc_html__( 'Website Condition', 'amanah-seo-engine' ) . '</h3>';
		echo '<p>' . esc_html( $needs_attention . ' area' . ( 1 === $needs_attention ? '' : 's' ) . ' perlu perhatian · ' . $good . ' area sudah baik' ) . '</p>';
		echo '<p class="description">' . esc_html( $scanned_count . ' dari ' . $discovered_count . ' halaman dianalisis' . ( $coverage_complete ? '' : ' — scan belum selesai' ) ) . '</p>';
		echo '</div>';

		echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin:16px 0;">';
		foreach ( $groups as $group ) {
			$this->renderScanGroupCard( $group );
		}
		echo '</div>';

		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h3 style="margin-top:0;">' . esc_html__( 'Priority Actions', 'amanah-seo-engine' ) . '</h3>';
		$action_groups = array_filter( $groups, static function ( $group ) { return 'good' !== $group['status']; } );
		if ( empty( $action_groups ) ) {
			echo '<p>' . esc_html__( 'Tidak ada masalah utama yang perlu ditangani dari hasil live scan.', 'amanah-seo-engine' ) . '</p>';
		} else {
			echo '<ol>';
			foreach ( array_slice( $action_groups, 0, 5 ) as $group ) {
				echo '<li style="margin-bottom:10px;"><strong>' . esc_html( $group['title'] ) . '</strong> — ' . esc_html( $group['count'] . ' halaman terdampak.' ) . ' <span class="description">' . esc_html( $group['action'] ) . '</span></li>';
			}
			echo '</ol>';
		}
		echo '</div>';
	}

	private function renderScanGroupCard( array $group ): void {
		$status = $group['status'];
		$label = 'good' === $status ? 'Sudah Baik' : ( 'critical' === $status ? 'Perlu Diperbaiki' : 'Perlu Perhatian' );
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:16px;min-height:110px;">';
		echo '<p style="margin:0 0 8px;"><strong>' . esc_html( $group['title'] ) . '</strong></p>';
		echo '<p style="margin:0 0 8px;">' . esc_html( $label ) . '</p>';
		echo '<p style="margin:0;font-size:13px;">' . esc_html( $group['count'] > 0 ? $group['count'] . ' halaman terdampak' : 'Tidak ditemukan masalah utama' ) . '</p>';
		echo '</div>';
	}

	private function groupLiveFindings( array $findings ): array {
		$groups = array(
			'technical' => array( 'title' => 'Technical SEO', 'codes' => array( 'live_http_status', 'live_canonical_mismatch', 'live_missing_canonical' ), 'action' => 'Periksa status halaman, canonical, dan aturan indexability.', 'urls' => array(), 'status' => 'good' ),
			'onpage' => array( 'title' => 'On-Page SEO', 'codes' => array( 'live_missing_title', 'live_title_length', 'live_missing_meta_description', 'live_meta_description_length', 'live_missing_h1', 'live_multiple_h1' ), 'action' => 'Perbaiki title, meta description, dan struktur H1.', 'urls' => array(), 'status' => 'good' ),
			'content' => array( 'title' => 'Content SEO', 'codes' => array( 'live_images_missing_alt' ), 'action' => 'Perbaiki elemen konten yang belum lengkap.', 'urls' => array(), 'status' => 'good' ),
			'links' => array( 'title' => 'Internal Linking', 'codes' => array( 'live_no_internal_links' ), 'action' => 'Tambahkan internal link yang relevan.', 'urls' => array(), 'status' => 'good' ),
			'indexability' => array( 'title' => 'Indexability', 'codes' => array( 'live_noindex', 'live_nofollow' ), 'action' => 'Periksa apakah halaman memang seharusnya dibatasi dari indexing atau crawling.', 'urls' => array(), 'status' => 'good' ),
		);

		foreach ( $findings as $finding ) {
			$code = (string) ( $finding['code'] ?? '' );
			$url = (string) ( $finding['url'] ?? '' );
			foreach ( $groups as $key => $group ) {
				if ( in_array( $code, $group['codes'], true ) ) {
					if ( '' !== $url ) {
						$groups[ $key ]['urls'][ $url ] = true;
					}
					if ( 'critical' === ( $finding['severity'] ?? '' ) ) {
						$groups[ $key ]['status'] = 'critical';
					} elseif ( 'critical' !== $groups[ $key ]['status'] ) {
						$groups[ $key ]['status'] = 'attention';
					}
				}
			}
		}

		foreach ( $groups as $key => $group ) {
			$groups[ $key ]['count'] = count( $group['urls'] );
			unset( $groups[ $key ]['urls'], $groups[ $key ]['codes'] );
		}
		return array_values( $groups );
	}

	private function renderLiveScanActionCenter( $scan_service, array $result ): void {
		$action_center = $scan_service->buildActionCenter( $result );
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h3 style="margin-top:0;">' . esc_html__( 'SEO Action Center', 'amanah-seo-engine' ) . '</h3>';
		if ( 0 === $action_center['total_issues'] ) {
			echo '<p>' . esc_html__( 'Tidak ada masalah live yang perlu ditindaklanjuti.', 'amanah-seo-engine' ) . '</p>';
			echo '</div>';
		return;
		}
		echo '<p class="description">' . esc_html( sprintf( '%d masalah: %d perlu diperbaiki, %d perlu perhatian, %d informasi.', $action_center['total_issues'], $action_center['critical'], $action_center['warning'], $action_center['info'] ) ) . '</p>';

		if ( ! empty( $action_center['groups'] ) ) {
			echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px;margin:14px 0;">';
			foreach ( $action_center['groups'] as $group ) {
				echo '<div style="border:1px solid #dcdcde;padding:12px;">';
				echo '<strong>' . esc_html( $group['title'] ) . '</strong>';
				echo '<p style="margin:6px 0 0;font-size:13px;">' . esc_html( $group['affected_pages'] . ' halaman · ' . $group['critical'] . ' critical · ' . $group['warning'] . ' warning · ' . $group['info'] . ' info' ) . '</p>';
				echo '</div>';
			}
			echo '</div>';
		}

		echo '<h4>' . esc_html__( 'Detail & tindakan', 'amanah-seo-engine' ) . '</h4>';
		echo '<div style="overflow:auto;">';
		echo '<table class="widefat striped"><thead><tr><th>Prioritas</th><th>Area</th><th>Halaman</th><th>Masalah</th><th>Tindakan</th><th>Aksi</th></tr></thead><tbody>';
		$post_id_cache = array();
		$per_page = 20;
		$current_page = isset( $_GET['action_page'] ) ? max( 1, absint( $_GET['action_page'] ) ) : 1;
		$total_pages = max( 1, (int) ceil( $action_center['total_issues'] / $per_page ) );
		$current_page = min( $current_page, $total_pages );
		$page_offset = ( $current_page - 1 ) * $per_page;
		$current_issues = array_slice( $action_center['issues'], $page_offset, $per_page );
		foreach ( $current_issues as $issue ) {
			$url = $issue['url'];
			$severity_label = 'critical' === $issue['severity'] ? 'Perlu Diperbaiki' : ( 'warning' === $issue['severity'] ? 'Perlu Perhatian' : 'Info' );
			echo '<tr>';
			echo '<td><strong>' . esc_html( $severity_label ) . '</strong></td>';
			echo '<td>' . esc_html( $issue['group_title'] ) . '</td>';
			echo '<td>' . ( '' !== $url ? '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $url ) . '</a>' : '—' ) . '</td>';
			echo '<td>' . esc_html( $issue['message'] ) . '</td>';
			echo '<td>' . esc_html( $issue['action'] ) . '</td>';
			echo '<td>';
			if ( '' !== $url ) {
				if ( ! array_key_exists( $url, $post_id_cache ) ) {
					$post_id_cache[ $url ] = url_to_postid( $url );
				}
				$post_id = (int) $post_id_cache[ $url ];
				$edit_link = $post_id > 0 ? get_edit_post_link( $post_id ) : '';
				if ( $edit_link ) {
					echo '<a class="button button-small" href="' . esc_url( $edit_link ) . '">Edit WordPress</a>';
				} else {
					echo '<a class="button button-small" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">Buka</a>';
				}
			}
			echo '</td></tr>';
		}
		echo '</tbody></table></div>';
		if ( $action_center['total_issues'] > $per_page ) {
			$first_item = $page_offset + 1;
			$last_item = min( $page_offset + $per_page, $action_center['total_issues'] );
			echo '<p class="description">' . esc_html( sprintf( 'Menampilkan masalah %d–%d dari %d masalah.', $first_item, $last_item, $action_center['total_issues'] ) ) . '</p>';
			echo '<div style="display:flex;gap:6px;align-items:center;margin-top:12px;flex-wrap:wrap;">';
			if ( $current_page > 1 ) {
				echo '<a class="button" href="' . esc_url( add_query_arg( 'action_page', $current_page - 1 ) ) . '">← Kembali</a>';
			}
			for ( $page = 1; $page <= $total_pages; $page++ ) {
				$classes =  $page === $current_page ? 'button button-primary' : 'button';
				echo '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( add_query_arg( 'action_page', $page ) ) . '">' . esc_html( (string) $page ) . '</a>';
			}
			if ( $current_page < $total_pages ) {
				echo '<a class="button" href="' . esc_url( add_query_arg( 'action_page', $current_page + 1 ) ) . '">Selanjutnya →</a>';
			}
			echo '</div>';
		}
		echo '</div>';
	}

	private function renderLiveScanHistory( $scan_service ): void {
		$history = $scan_service->getHistory( 8 );
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h3 style="margin-top:0;">' . esc_html__( 'SEO Scan History', 'amanah-seo-engine' ) . '</h3>';
		if ( empty( $history ) ) {
			echo '<p>' . esc_html__( 'Belum ada riwayat scan tersimpan.', 'amanah-seo-engine' ) . '</p></div>';
		return;
		}

		if ( count( $history ) > 1 ) {
			$comparison = $scan_service->compareScans( $history[0], $history[1] );
			echo '<p><strong>Perubahan sejak scan sebelumnya:</strong> ' . esc_html( $this->formatDelta( $comparison['findings_delta'] ) ) . ' findings · ' . esc_html( $this->formatDelta( $comparison['pages_delta'] ) ) . ' halaman · ' . esc_html( $this->formatDelta( $comparison['critical_delta'] ) ) . ' critical · ' . esc_html( $this->formatDelta( $comparison['warning_delta'] ) ) . ' warning.</p>';
		}

		echo '<div style="overflow:auto;"><table class="widefat striped"><thead><tr><th>Waktu</th><th>Status</th><th>Halaman</th><th>Findings</th><th>Critical</th><th>Warning</th><th>Info</th></tr></thead><tbody>';
		foreach ( $history as $scan ) {
			$result = isset( $scan['result'] ) && is_array( $scan['result'] ) ? $scan['result'] : array();
			$inventory = isset( $result['website_inventory'] ) && is_array( $result['website_inventory'] ) ? $result['website_inventory'] : array();
			$counts = isset( $result['severity_counts'] ) && is_array( $result['severity_counts'] ) ? $result['severity_counts'] : array();
			echo '<tr>';
			echo '<td>' . esc_html( $scan['completed_at'] ) . '</td>';
			echo '<td>' . esc_html( 'completed' === $scan['status'] ? 'Selesai' : $scan['status'] ) . '</td>';
			echo '<td>' . esc_html( (string) ( $inventory['total_pages'] ?? 0 ) ) . '</td>';
			echo '<td>' . esc_html( (string) ( $scan['findings_count'] ?? 0 ) ) . '</td>';
			echo '<td>' . esc_html( (string) ( $counts['critical'] ?? 0 ) ) . '</td>';
			echo '<td>' . esc_html( (string) ( $counts['warning'] ?? 0 ) ) . '</td>';
			echo '<td>' . esc_html( (string) ( $counts['info'] ?? 0 ) ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table></div>';
		echo '</div>';
	}

	private function formatDelta( int $delta ): string {
		return ( $delta > 0 ? '+' : '' ) . (string) $delta;
	}

	private function renderSettings(): void {
		$settings_url = admin_url( 'options-general.php' );
		echo '<p>' . esc_html__( 'Operational settings are registered through the WordPress Settings API.', 'amanah-seo-engine' ) . '</p>';
		echo '<p><a class="button button-primary" href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Open WordPress Settings', 'amanah-seo-engine' ) . '</a></p>';
		echo '<h2>' . esc_html__( 'Current Safe Defaults', 'amanah-seo-engine' ) . '</h2>';
		echo '<ul>';
		$this->renderSettingValue( 'AI enabled', (bool) $this->configuration->get( 'ai_enabled', false ) );
		$this->renderSettingValue( 'Debug logging', (bool) $this->configuration->get( 'debug_logging', false ) );
		$this->renderSettingValue( 'Automatic publishing', (bool) $this->configuration->get( 'automatic_publishing', false ) );
		$this->renderSettingValue( 'Background processing', (bool) $this->configuration->get( 'background_processing', true ) );
		echo '</ul>';
		echo '</div>';
	}

	private function renderSettingValue( string $label, bool $value ): void {
		echo '<li><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $value ? 'Enabled' : 'Disabled' ) . '</li>';
	}

	private function renderIntelligenceScreen( string $screen ): void {
		$intelligence = Plugin::boot()->get( 'seo_intelligence_service' )->build();

		if ( ! $intelligence['has_scan'] ) {
			echo '<div class="notice notice-info"><p>Jalankan Live SEO Scan terlebih dahulu agar SEO Intelligence memiliki data website nyata.</p></div></div>';
			return;
		}

		$workflow = Plugin::boot()->get( 'issue_recommendation_service' );
		$workflow_data = null;
		if ( in_array( $screen, array( 'seo-issues', 'recommendations' ), true ) ) {
			$workflow_data = $workflow->build();
		}

		echo '<div style="max-width:1200px;">';
		echo '<p><strong>Data terakhir:</strong> ' . esc_html( $intelligence['last_scan'] ) . ' &nbsp; <strong>Halaman:</strong> ' . esc_html( (string) $intelligence['pages'] ) . ' / ' . esc_html( (string) $intelligence['discovered_pages'] ) . '</p>';

		if ( 'seo-issues' === $screen ) {
			$this->renderIntelligenceIssueTable( $workflow_data['issues'] );
		} elseif ( 'recommendations' === $screen ) {
			$this->renderRecommendationTable( $workflow_data['recommendations'] );
		} elseif ( 'monitoring-keywords' === $screen ) {
			$this->renderKeywordIntelligence( $intelligence );
		} elseif ( 'internal-links' === $screen || 'orphan-pages' === $screen ) {
			$this->renderUrlIntelligence( $intelligence, $screen );
		} elseif ( 'cannibalization' === $screen ) {
			$this->renderCannibalization( $intelligence );
		} else {
			$this->renderMonitoring( $intelligence, $screen );
		}

		echo '</div></div>';
	}

	private function renderIntelligenceIssueTable( array $issues ): void {
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">SEO Issues</h2>';
		echo '<p>Issue adalah record kanonik yang berasal dari data website dan dapat memiliki status Open, In Progress, Resolved, atau Dismissed.</p>';
		echo '<table class="widefat striped"><thead><tr><th>Prioritas</th><th>URL</th><th>Masalah</th><th>Status</th><th>Aksi</th></tr></thead><tbody>';
		foreach ( $issues as $issue ) {
			$status = $issue->getStatus();
			$edit_url = '' !== $issue->getUrl() && url_to_postid( $issue->getUrl() ) > 0 ? get_edit_post_link( url_to_postid( $issue->getUrl() ), 'raw' ) : '';
			echo '<tr>';
			echo '<td>' . esc_html( strtoupper( $issue->getSeverity() ) ) . '</td>';
			echo '<td>' . esc_html( $issue->getUrl() ) . '</td>';
			echo '<td>' . esc_html( $issue->getMessage() ) . '</td>';
			echo '<td><strong>' . esc_html( strtoupper( str_replace( '_', ' ', $status ) ) ) . '</strong></td>';
			echo '<td>';
			if ( $edit_url ) { echo '<a class="button button-small" href="' . esc_url( $edit_url ) . '">Edit WordPress</a> '; }
			$this->renderIssueStatusActions( $issue );
			echo '</td></tr>';
		}
		if ( empty( $issues ) ) { echo '<tr><td colspan="5">Tidak ada issue kanonik.</td></tr>'; }
		echo '</tbody></table></div>';
	}

	private function renderRecommendationTable( array $recommendations ): void {
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">Recommendations</h2>';
		echo '<p>Recommendation memiliki lifecycle terkontrol. Execution mode saat ini <strong>manual</strong>; engine tidak mengubah konten WordPress secara otomatis.</p>';
		echo '<table class="widefat striped"><thead><tr><th>Prioritas</th><th>Target URL</th><th>Rekomendasi</th><th>Status</th><th>Aksi</th></tr></thead><tbody>';
		foreach ( $recommendations as $item ) {
			echo '<tr><td>' . esc_html( strtoupper( $item->getPriority() ) ) . '</td><td>' . esc_html( $item->getTargetUrl() ) . '</td><td>' . esc_html( $item->getRecommendation() ) . '</td><td><strong>' . esc_html( strtoupper( str_replace( '_', ' ', $item->getStatus() ) ) ) . '</strong></td><td>';
			$this->renderRecommendationStatusActions( $item );
			echo '</td></tr>';
		}
		if ( empty( $recommendations ) ) { echo '<tr><td colspan="5">Belum ada recommendation kanonik.</td></tr>'; }
		echo '</tbody></table></div>';
	}

	private function renderIssueStatusActions( $issue ): void {
		$next = array(
			'open' => array( 'in_progress', 'Mulai' ),
			'in_progress' => array( 'resolved', 'Resolved' ),
			'resolved' => array( 'open', 'Buka Kembali' ),
			'dismissed' => array( 'open', 'Buka Kembali' ),
		);
		if ( ! isset( $next[ $issue->getStatus() ] ) ) { return; }
		$this->renderStatusForm( 'issue', (int) $issue->getId(), $next[ $issue->getStatus() ][0], $next[ $issue->getStatus() ][1] );
		if ( 'open' === $issue->getStatus() || 'in_progress' === $issue->getStatus() ) {
			$this->renderStatusForm( 'issue', (int) $issue->getId(), 'dismissed', 'Dismiss' );
		}
	}

	private function renderRecommendationStatusActions( $recommendation ): void {
		$next = array(
			'pending' => array( 'in_progress', 'Mulai' ),
			'in_progress' => array( 'completed', 'Tandai Selesai' ),
			'completed' => array( 'verified', 'Tandai Verified' ),
			'dismissed' => array( 'pending', 'Buka Kembali' ),
			'verified' => array( 'pending', 'Buka Kembali' ),
		);
		if ( ! isset( $next[ $recommendation->getStatus() ] ) ) { return; }
		$this->renderStatusForm( 'recommendation', (int) $recommendation->getId(), $next[ $recommendation->getStatus() ][0], $next[ $recommendation->getStatus() ][1] );
		if ( 'pending' === $recommendation->getStatus() || 'in_progress' === $recommendation->getStatus() ) {
			$this->renderStatusForm( 'recommendation', (int) $recommendation->getId(), 'dismissed', 'Dismiss' );
		}
	}

	private function renderStatusForm( string $type, int $id, string $status, string $label ): void {
		echo '<form method="post" style="display:inline-block;margin:2px 0;">';
		wp_nonce_field( 'amanah_seo_issue_recommendation_action', 'amanah_seo_issue_recommendation_nonce' );
		echo '<input type="hidden" name="amanah_issue_recommendation_type" value="' . esc_attr( $type ) . '" />';
		echo '<input type="hidden" name="amanah_issue_recommendation_id" value="' . esc_attr( (string) $id ) . '" />';
		echo '<input type="hidden" name="amanah_issue_recommendation_status" value="' . esc_attr( $status ) . '" />';
		echo '<button type="submit" class="button button-small">' . esc_html( $label ) . '</button>';
		echo '</form>';
	}

	public function handleIssueRecommendationAction( $workflow, string $screen ): void {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['amanah_issue_recommendation_type'] ) ) { return; }
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not authorized to change SEO issue or recommendation status.', 'amanah-seo-engine' ), 403 );
		}
		check_admin_referer( 'amanah_seo_issue_recommendation_action', 'amanah_seo_issue_recommendation_nonce' );
		$type = sanitize_key( (string) $_POST['amanah_issue_recommendation_type'] );
		$id = absint( $_POST['amanah_issue_recommendation_id'] ?? 0 );
		$status = sanitize_key( (string) $_POST['amanah_issue_recommendation_status'] );
		$success = 'issue' === $type ? $workflow->updateIssueStatus( $id, $status ) : ( 'recommendation' === $type ? $workflow->updateRecommendationStatus( $id, $status ) : false );
		add_settings_error( 'amanah_seo_issue_recommendation', $success ? 'updated' : 'failed', $success ? 'Status berhasil diperbarui.' : 'Status tidak dapat diperbarui.', $success ? 'success' : 'error' );
		$redirect = admin_url( 'admin.php?page=' . sanitize_key( (string) ( $_GET['page'] ?? 'amanah-seo-engine-' . $screen ) ) );
		wp_safe_redirect( add_query_arg( 'amanah_status_updated', '1', $redirect ) );
		exit;
	}

	private function renderKeywordIntelligence( array $data ): void {
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">Keyword & URL Intelligence</h2>';
		echo '<p><strong>Total keyword:</strong> ' . esc_html( (string) $data['keyword_total'] ) . ' &nbsp; <strong>Belum mapped:</strong> ' . esc_html( (string) count( $data['keyword_gaps'] ) ) . '</p>';
		echo '<table class="widefat striped"><thead><tr><th>Keyword</th><th>Tindakan</th></tr></thead><tbody>';
		foreach ( $data['keyword_gaps'] as $gap ) {
			echo '<tr><td>' . esc_html( $gap['keyword'] ) . '</td><td>' . esc_html( $gap['action'] ) . '</td></tr>';
		}
		if ( empty( $data['keyword_gaps'] ) ) {
			echo '<tr><td colspan="2">Semua keyword yang tersedia sudah memiliki Owner URL.</td></tr>';
		}
		echo '</tbody></table></div>';
	}

	private function renderUrlIntelligence( array $data, string $screen ): void {
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">' . esc_html( 'internal-links' === $screen ? 'Internal Links Intelligence' : 'Orphan / Unmapped URL Review' ) . '</h2>';
		echo '<table class="widefat striped"><thead><tr><th>URL</th><th>Keyword Owner</th><th>Live Issues</th><th>Action</th></tr></thead><tbody>';
		foreach ( $data['url_opportunities'] as $item ) {
			$needs_action = 'orphan-pages' === $screen ? empty( $item['keywords'] ) : ( (int) $item['issues'] > 0 );
			if ( ! $needs_action ) {
				continue;
			}
			$keywords = empty( $item['keywords'] ) ? '—' : implode( ', ', $item['keywords'] );
			$action = '' !== $item['edit_url'] ? '<a href="' . esc_url( $item['edit_url'] ) . '">Edit WordPress</a>' : 'Buka URL';
			echo '<tr><td>' . esc_html( $item['url'] ) . '</td><td>' . esc_html( $keywords ) . '</td><td>' . esc_html( (string) $item['issues'] ) . '</td><td>' . $action . '</td></tr>';
		}
		echo '</tbody></table></div>';
	}

	private function renderCannibalization( array $data ): void {
		$groups = array();
		foreach ( $data['url_opportunities'] as $item ) {
			foreach ( $item['keywords'] as $keyword ) {
				$groups[ strtolower( $keyword ) ][] = $item['url'];
			}
		}
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;">';
		echo '<h2 style="margin-top:0;">Cannibalization Review</h2>';
		echo '<table class="widefat striped"><thead><tr><th>Keyword</th><th>Owner URLs</th><th>Status</th></tr></thead><tbody>';
		$found = false;
		foreach ( $groups as $keyword => $urls ) {
			$urls = array_values( array_unique( $urls ) );
			if ( count( $urls ) < 2 ) {
				continue;
			}
			$found = true;
			echo '<tr><td>' . esc_html( $keyword ) . '</td><td>' . esc_html( implode( ' | ', $urls ) ) . '</td><td>Review ownership</td></tr>';
		}
		if ( ! $found ) {
			echo '<tr><td colspan="3">Tidak ditemukan keyword yang memiliki lebih dari satu Owner URL di data engine.</td></tr>';
		}
		echo '</tbody></table></div>';
	}

	private function renderMonitoring( array $data, string $screen ): void {
		$counts = $data['issue_counts'];
		$total = array_sum( $counts );
		$title = 'monitoring-site-health' === $screen ? 'Site Health' : 'SEO Performance Monitoring';
		echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">';
		$this->renderCard( 'Total live issues', (string) $total );
		$this->renderCard( 'Technical SEO', (string) $counts['technical'] );
		$this->renderCard( 'On-Page SEO', (string) $counts['onpage'] );
		$this->renderCard( 'Content SEO', (string) $counts['content'] );
		$this->renderCard( 'Internal Linking', (string) $counts['links'] );
		$this->renderCard( 'Indexability', (string) $counts['indexability'] );
		echo '</div>';
		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;"><h2 style="margin-top:0;">' . esc_html( $title ) . '</h2>';
		if ( ! empty( $data['comparison'] ) ) {
			echo '<p>Perubahan sejak scan sebelumnya: <strong>' . esc_html( (string) $data['comparison']['findings_delta'] ) . ' findings</strong>, <strong>' . esc_html( (string) $data['comparison']['pages_delta'] ) . ' halaman</strong>.</p>';
		}
		echo '<p>Monitoring ini menggunakan snapshot Live SEO Scan. Integrasi Google tetap opsional.</p></div>';
	}
	
	private function renderGoogleIntegration( string $screen ): void {
		$services = array(
			'google-search-console' => array( 'name' => 'Google Search Console', 'purpose' => 'Search performance, queries, clicks, impressions, CTR, position, and ranking URLs.' ),
			'google-analytics' => array( 'name' => 'Google Analytics 4', 'purpose' => 'Organic users, engagement, and key event data.' ),
			'google-tag-manager' => array( 'name' => 'Google Tag Manager', 'purpose' => 'Tracking infrastructure and measurement support.' ),
		);
		$service = $services[ $screen ];
		$oauth = Plugin::boot()->get( 'google_oauth' );
		$repository = Plugin::boot()->get( 'google_connection_repository' );

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_google_action'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You are not authorized to manage Google connections.', 'amanah-seo-engine' ), 403 );
			}
			check_admin_referer( 'amanah_google_connection', 'amanah_google_nonce' );
			$action = sanitize_key( (string) $_POST['amanah_google_action'] );
			try {
				if ( 'configure' === $action ) {
					$oauth->configureClient(
						sanitize_text_field( wp_unslash( (string) ( $_POST['google_client_id'] ?? '' ) ) ),
						sanitize_text_field( wp_unslash( (string) ( $_POST['google_client_secret'] ?? '' ) ) )
					);
					add_settings_error( 'amanah_google', 'google_configured', 'Google OAuth client configuration saved.', 'success' );
				} elseif ( 'disconnect' === $action ) {
					$oauth->disconnect( $screen );
					add_settings_error( 'amanah_google', 'google_disconnected', 'Google service disconnected.', 'success' );
				}
			} catch ( \Throwable $e ) {
				add_settings_error( 'amanah_google', 'google_error', $e->getMessage(), 'error' );
			}
		}

		$connection = $oauth->getConnection( $screen );
		$connected = is_array( $connection );
		$client_id = $repository->getClientId();
		$has_client_secret = '' !== $repository->getClientSecret();

		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;max-width:900px;">';
		settings_errors( 'amanah_google' );
		if ( isset( $_GET['google_connected'] ) ) {
			echo '<div class="notice notice-success inline"><p>Google connection completed successfully.</p></div>';
		}
		if ( isset( $_GET['google_error'] ) ) {
			echo '<div class="notice notice-error inline"><p>' . esc_html( rawurldecode( (string) $_GET['google_error'] ) ) . '</p></div>';
		}
		echo '<h2 style="margin-top:0;">' . esc_html( $service['name'] ) . '</h2>';
		echo '<p>' . esc_html( $service['purpose'] ) . '</p>';
		echo '<p><strong>Connection status:</strong> ' . esc_html( $connected ? 'Connected' : 'Not connected' ) . '</p>';
		echo '<p><strong>Synchronization status:</strong> ' . esc_html( $connected ? 'Ready for synchronization' : 'Not configured' ) . '</p>';
		echo '<p><strong>Last synchronization:</strong> —</p>';

		if ( $connected ) {
			echo '<p><strong>Connected at:</strong> ' . esc_html( (string) ( $connection['connected_at'] ?? '—' ) ) . '</p>';
			echo '<form method="post">';
			wp_nonce_field( 'amanah_google_connection', 'amanah_google_nonce' );
			echo '<input type="hidden" name="amanah_google_action" value="disconnect" /><button type="submit" class="button">Disconnect Google</button>';
			echo '</form>';
		} else {
			echo '<div class="notice notice-info inline"><p>Google OAuth credentials are stored securely by the plugin. Tokens are encrypted at rest and are not displayed.</p></div>';
			echo '<h3>Google OAuth Client</h3>';
			echo '<form method="post">';
			wp_nonce_field( 'amanah_google_connection', 'amanah_google_nonce' );
			echo '<input type="hidden" name="amanah_google_action" value="configure" />';
			echo '<p><label for="amanah-google-client-id"><strong>Client ID</strong></label><br /><input id="amanah-google-client-id" type="text" name="google_client_id" value="' . esc_attr( $client_id ) . '" class="regular-text" autocomplete="off" /></p>';
			echo '<p><label for="amanah-google-client-secret"><strong>Client Secret</strong></label><br /><input id="amanah-google-client-secret" type="password" name="google_client_secret" value="" class="regular-text" autocomplete="new-password" placeholder="' . esc_attr( $has_client_secret ? 'Saved — leave blank to keep existing secret' : '' ) . '" /></p>';
			echo '<p><button type="submit" class="button">Save Google OAuth Client</button></p>';
			echo '</form>';
		if ( $has_client_secret && '' !== $client_id ) {
			try {
				$url = $oauth->getAuthorizationUrl( $screen );
				echo '<p><a class="button button-primary" href="' . esc_url( $url ) . '">Connect Google</a></p>';
			} catch ( \Throwable $e ) {
				echo '<p class="description">' . esc_html( $e->getMessage() ) . '</p>';
			}
		}
		}
		echo '</div>';
	}


	private function renderAIConfiguration(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage AI configuration.', 'amanah-seo-engine' ), 403 );
		}

		$services = Plugin::boot();
		$registry = $services->get( 'ai_provider_registry' );
		$secret_store = $services->get( 'ai_secret_store' );
		$discovery = $services->get( 'ai_model_discovery' );
		$catalog = $services->get( 'ai_model_catalog' );

		$provider = (string) $this->configuration->get( 'ai_provider', '' );
		if ( '' === $provider ) {
			$provider = sanitize_key( (string) get_option( 'amanah_seo_engine_active_ai_provider', 'gemini' ) );
			if ( '' !== $provider ) {
				$this->configuration->set( 'ai_provider', $provider );
			}
		}
		if ( 'amanah_seo_engine' === $provider ) {
			$provider = 'gemini';
			$this->configuration->set( 'ai_provider', 'gemini' );
			$this->configuration->set( 'ai_enabled', true );
			update_option( 'amanah_seo_engine_active_ai_provider', 'gemini', false );
			$legacy_model = (string) $this->configuration->get( 'ai_model', '' );
			if ( 'amanah-default-v1' === $legacy_model || '' === $legacy_model ) {
				$this->configuration->set( 'ai_model', 'gemini-2.5-flash' );
				update_option( 'amanah_seo_engine_active_ai_model', 'gemini-2.5-flash', false );
			}
		}

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_ai_configuration_action'] ) ) {
			$action = sanitize_key( (string) $_POST['amanah_ai_configuration_action'] );

			if ( 'save_provider' === $action ) {
				check_admin_referer( 'amanah_ai_provider', 'amanah_ai_provider_nonce' );
				$provider = sanitize_key( (string) ( $_POST['ai_provider'] ?? '' ) );
				$api_key = trim( (string) ( $_POST['ai_api_key'] ?? '' ) );
				$adapter = $registry->get( $provider );

				if ( null === $adapter ) {
					add_settings_error( 'amanah_ai', 'provider_invalid', 'AI provider tidak valid.', 'error' );
				} elseif ( '' === $api_key ) {
					add_settings_error( 'amanah_ai', 'key_empty', 'API key wajib diisi.', 'error' );
				} else {
					try {
						$validation = $adapter->validateCredentials( array( 'api_key' => $api_key ) );
						if ( empty( $validation['valid'] ) ) {
							throw new \RuntimeException( (string) ( $validation['category'] ?? 'authentication_error' ) );
						}
						$secret_store->set( $provider, $api_key );
						update_option( 'amanah_seo_engine_active_ai_provider', $provider, false );
						$this->configuration->set( 'ai_provider', $provider );
						$this->configuration->set( 'ai_enabled', true );
						add_settings_error( 'amanah_ai', 'provider_saved', 'AI provider dan API key berhasil divalidasi dan disimpan.', 'success' );
					} catch ( \Throwable $e ) {
						add_settings_error( 'amanah_ai', 'provider_error', 'Validasi provider gagal: ' . $e->getMessage(), 'error' );
					}
				}
			} elseif ( 'discover_models' === $action ) {
				check_admin_referer( 'amanah_ai_model', 'amanah_ai_model_nonce' );
				try {
					$secret = $secret_store->get( $provider );
					if ( '' === (string) $secret || null === $registry->get( $provider ) ) {
						throw new \RuntimeException( 'Provider belum dikonfigurasi.' );
					}
					$models = $discovery->discover( $provider, array( 'api_key' => $secret ) );
					add_settings_error( 'amanah_ai', 'models_discovered', sprintf( '%d model ditemukan.', count( $models ) ), 'success' );
				} catch ( \Throwable $e ) {
					add_settings_error( 'amanah_ai', 'models_error', 'Model discovery gagal: ' . $e->getMessage(), 'error' );
				}
			} elseif ( 'save_model' === $action ) {
				check_admin_referer( 'amanah_ai_model', 'amanah_ai_model_nonce' );
				$model = sanitize_text_field( (string) ( $_POST['ai_model'] ?? '' ) );
				if ( '' !== $model ) {
					$available_models = $catalog->getModels( $provider );
					$available_ids = array_map( static function ( $item ) {
						return (string) ( $item['model_identifier'] ?? '' );
					}, $available_models );
					if ( ! in_array( $model, $available_ids, true ) ) {
						add_settings_error( 'amanah_ai', 'model_invalid', 'Model yang dipilih tidak tersedia untuk provider aktif.', 'error' );
					} else {
						$this->configuration->set( 'ai_provider', $provider );
						update_option( 'amanah_seo_engine_active_ai_provider', $provider, false );
						$this->configuration->set( 'ai_model', $model );
						update_option( 'amanah_seo_engine_active_ai_model', $model, false );
						$this->configuration->set( 'ai_enabled', true );
						$saved_model = (string) $this->configuration->get( 'ai_model', '' );
						if ( '' === $saved_model ) {
							$saved_model = sanitize_text_field( (string) get_option( 'amanah_seo_engine_active_ai_model', '' ) );
							if ( '' !== $saved_model ) {
								$this->configuration->set( 'ai_model', $saved_model );
							}
						}
						if ( $saved_model === $model ) {
							add_settings_error( 'amanah_ai', 'model_selected', 'Model berhasil dipilih. AI siap digunakan.', 'success' );
						} else {
							add_settings_error( 'amanah_ai', 'model_not_persisted', 'Model gagal disimpan ke konfigurasi AI.', 'error' );
						}
					}
				}
			}
		}

		$models = '' !== $provider ? $catalog->getModels( $provider ) : array();
		$selected_model = (string) $this->configuration->get( 'ai_model', '' );
		if ( '' === $selected_model ) {
			$selected_model = sanitize_text_field( (string) get_option( 'amanah_seo_engine_active_ai_model', '' ) );
			if ( '' !== $selected_model ) {
				$this->configuration->set( 'ai_model', $selected_model );
			}
		}
		$selected_model_name = '';
		foreach ( $models as $item ) {
			$id = (string) ( $item['model_identifier'] ?? '' );
			if ( $id === $selected_model ) {
				$selected_model_name = (string) ( $item['display_name'] ?? $id );
				break;
			}
		}

		echo '<div style="background:#fff;border:1px solid #dcdcde;padding:20px;margin:16px 0;max-width:1000px;">';
		settings_errors( 'amanah_ai' );
		echo '<p class="description">Konfigurasi AI dilakukan dalam satu alur: pilih provider, validasi API key, temukan model, lalu pilih model aktif.</p>';

		echo '<h2>Provider</h2>';
		echo '<form method="post">';
		wp_nonce_field( 'amanah_ai_provider', 'amanah_ai_provider_nonce' );
		echo '<input type="hidden" name="amanah_ai_configuration_action" value="save_provider" />';
		echo '<p><label><strong>Provider</strong></label><br /><select name="ai_provider">';
		foreach ( $registry->all() as $key => $adapter ) {
			$sel = selected( $provider, $key, false );
			echo '<option value="' . esc_attr( $key ) . '"' . $sel . '>' . esc_html( $adapter->getProviderName() ) . '</option>';
		}
		echo '</select></p>';
		echo '<p><label><strong>API Key</strong></label><br /><input type="password" name="ai_api_key" class="regular-text" autocomplete="new-password" /></p>';
		echo '<p><button type="submit" class="button button-primary">Save &amp; Validate Provider</button></p>';
		echo '</form>';

		echo '<hr />';

		echo '<h2>Model</h2>';
		echo '<p><strong>Provider aktif:</strong> ' . esc_html( '' !== $provider ? $provider : 'Belum dikonfigurasi' ) . '</p>';
		if ( '' === $provider ) {
			echo '<div class="notice notice-warning inline"><p>Konfigurasi provider terlebih dahulu.</p></div>';
		} else {
			echo '<form method="post" style="margin-bottom:16px;">';
			wp_nonce_field( 'amanah_ai_model', 'amanah_ai_model_nonce' );
			echo '<input type="hidden" name="amanah_ai_configuration_action" value="discover_models" />';
			echo '<button type="submit" class="button">Discover Models</button>';
			echo '</form>';

			if ( '' !== $selected_model ) {
				echo '<div class="notice notice-success inline"><p><strong>Model aktif:</strong> ' . esc_html( '' !== $selected_model_name ? $selected_model_name : $selected_model ) . '</p></div>';
			} else {
				echo '<div class="notice notice-warning inline"><p><strong>Model aktif:</strong> Belum dipilih.</p></div>';
			}

			if ( empty( $models ) ) {
				echo '<p>Belum ada model yang ditemukan. Jalankan Discover Models.</p>';
			} else {
				echo '<form method="post">';
				wp_nonce_field( 'amanah_ai_model', 'amanah_ai_model_nonce' );
				echo '<input type="hidden" name="amanah_ai_configuration_action" value="save_model" />';
				echo '<p><label><strong>Model</strong></label><br /><select name="ai_model">';
				foreach ( $models as $item ) {
					$id = (string) $item['model_identifier'];
					echo '<option value="' . esc_attr( $id ) . '"' . selected( $selected_model, $id, false ) . '>' . esc_html( (string) ( $item['display_name'] ?? $id ) ) . '</option>';
				}
				echo '</select></p><p><button type="submit" class="button button-primary">Save Model</button></p></form>';
			}
		}
		echo '</div>';
	}

	private function renderPlaceholder( array $definition ): void {
		echo '<div class="notice notice-info"><p>';
		echo esc_html(
			sprintf(
				'%s is part of the Amanah SEO Engine workflow and will be connected to its application data in the corresponding implementation package.',
				$definition['title']
			)
		);
		echo '</p></div>';
		echo '</div>';
	}

	private function renderCard( string $title, string $message ): void {
		echo '<div style="padding:16px;background:#fff;border:1px solid #dcdcde;box-sizing:border-box;min-height:130px;">';
		echo '<h2 style="margin-top:0;font-size:16px;">' . esc_html( $title ) . '</h2>';
		echo '<p>' . esc_html( $message ) . '</p>';
		echo '</div>';
	}
}