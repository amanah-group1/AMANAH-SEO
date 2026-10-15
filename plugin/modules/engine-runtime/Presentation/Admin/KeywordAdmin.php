<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Application\Command\CreateKeywordCommand;
use Amanah\SEOEngine\Application\UseCase\CreateKeyword;
use Amanah\SEOEngine\Application\UseCase\FindOwnerUrl;
use Amanah\SEOEngine\Application\UseCase\ListKeywords;
use Amanah\SEOEngine\Application\UseCase\GeneratePotentialSupportKeywords;
use Amanah\SEOEngine\Application\UseCase\CreateSupportKeywordsFromCandidates;
use Amanah\SEOEngine\Application\UseCase\ScanPillarArticle;

defined( 'ABSPATH' ) || exit;

final class KeywordAdmin {

	private $create_keyword;
	private $list_keywords;
	private $find_owner_url;
	private $generate_potential_support_keywords;
	private $create_support_keywords;
	private $scan_pillar_article;
	private $pillar_scan_result = array();
	private $generated_suggestions = array();

	public function __construct(
		CreateKeyword $create_keyword,
		ListKeywords $list_keywords,
		FindOwnerUrl $find_owner_url,
		GeneratePotentialSupportKeywords $generate_potential_support_keywords,
		CreateSupportKeywordsFromCandidates $create_support_keywords,
		ScanPillarArticle $scan_pillar_article
	) {
		$this->create_keyword = $create_keyword;
		$this->list_keywords = $list_keywords;
		$this->find_owner_url = $find_owner_url;
		$this->generate_potential_support_keywords = $generate_potential_support_keywords;
		$this->create_support_keywords = $create_support_keywords;
		$this->scan_pillar_article = $scan_pillar_article;
	}

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage keywords.', 'amanah-seo-engine' ) );
		}

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_keyword_action'] ) ) {
			$this->handlePost();
		}

		$keywords = $this->list_keywords->execute();
		$pillar_keywords = array();
		foreach ( $keywords as $item ) {
			$data = $item->toArray();
			if ( 'pillar' === $data['article_type'] ) {
				$pillar_keywords[] = $data;
			}
		}

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Keywords', 'amanah-seo-engine' ) . '</h1>';
		settings_errors( 'amanah_keywords' );

		if ( ! empty( $this->pillar_scan_result ) ) {
			$result = $this->pillar_scan_result;
			echo '<div class="notice notice-success" style="padding:12px 16px;">';
			echo '<p><strong>Artikel Pilar berhasil dipindai.</strong> Amanah SEO sudah mengambil struktur dan isi utama halaman untuk menjadi dasar analisis Support.</p>';
			echo '<p><strong>Pilar:</strong> ' . esc_html( $result['pillar_keyword'] ?? '' ) . ' &nbsp; <strong>URL:</strong> ' . esc_html( $result['url'] ?? '' ) . '</p>';
			echo '<p><strong>Judul:</strong> ' . esc_html( $result['title'] ?? '' ) . ' &nbsp; <strong>Kata:</strong> ' . esc_html( (string) ( $result['word_count'] ?? 0 ) ) . ' &nbsp; <strong>H1:</strong> ' . esc_html( (string) ( $result['h1_count'] ?? 0 ) ) . '</p>';
			if ( ! empty( $result['support_keyword'] ) ) {
				echo '<p><strong>Keyword Support:</strong> ' . esc_html( $result['support_keyword'] ) . ' &nbsp; <strong>Kemunculan:</strong> ' . esc_html( (string) ( $result['keyword_occurrences'] ?? 0 ) ) . ' &nbsp; <strong>Token ditemukan:</strong> ' . esc_html( (string) ( $result['keyword_tokens_in_pillar'] ?? 0 ) ) . '</p>';
			}
			if ( ! empty( $result['headings'] ) ) {
				echo '<p><strong>Struktur Heading Pilar</strong></p><ol style="margin-left:24px;">';
				foreach ( array_slice( $result['headings'], 0, 30 ) as $heading ) {
					echo '<li>H' . esc_html( (string) ( $heading['level'] ?? 0 ) ) . ' — ' . esc_html( (string) ( $heading['text'] ?? '' ) ) . '</li>';
				}
				echo '</ol>';
			}
			echo '<p class="description">Hasil ini adalah fondasi analisis. Brief Support belum dibuat pada tahap ini.</p>';
			echo '</div>';
		}

		if ( ! empty( $this->generated_suggestions ) ) {
			echo '<div class="notice notice-info" style="padding:12px 16px;margin:15px 0;">';
			echo '<p><strong>Potential Support Keywords</strong> untuk Pilar <em>' . esc_html( $this->generated_suggestions['pillar_phrase'] ) . '</em>.</p>';
			echo '<p>Kandidat yang terdeteksi dari Keyword Intelligence/Autocomplete telah otomatis dimasukkan sebagai Keyword Support untuk coverage artikel Pilar.</p>';
			echo '<table class="widefat striped"><thead><tr><th>Keyword Support</th><th>Sumber</th><th>Status</th></tr></thead><tbody>';
			foreach ( $this->generated_suggestions['items'] as $suggestion ) {
				echo '<tr>';
				echo '<td>' . esc_html( $suggestion['keyword'] ) . '</td>';
				echo '<td>' . esc_html( 'google_autocomplete' === (string) ( $suggestion['source'] ?? '' ) ? 'Google Autocomplete' : (string) ( $suggestion['source'] ?? 'unknown' ) ) . '</td>';
				echo '<td><strong>Included in Support Coverage</strong></td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
			echo '<p class="description">Keyword Support ini akan dibawa ke SEO Strategy → Content Brief → Page Blueprint → Content Generation dan diwajibkan muncul di artikel.</p>';
			echo '</div>';
		}

		echo '<h2>' . esc_html__( 'Add Keyword', 'amanah-seo-engine' ) . '</h2>';
		echo '<form method="post">';
		wp_nonce_field( 'amanah_create_keyword', 'amanah_keyword_nonce' );
		echo '<input type="hidden" name="amanah_keyword_action" value="create" />';
		echo '<table class="form-table" role="presentation"><tbody>';

		echo '<tr><th scope="row"><label for="amanah_keyword_phrase">Keyword</label></th><td>';
		echo '<input type="text" id="amanah_keyword_phrase" name="keyword_phrase" class="regular-text" required />';
		echo '</td></tr>';


		echo '<tr><th scope="row"><label>Type Artikel</label></th><td>';
		echo '<fieldset style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;margin:0;">';
		echo '<label style="display:inline-flex;align-items:center;gap:6px;margin:0;"><input type="radio" name="article_type" value="pillar" checked /> <span>Pilar</span></label>';
		echo '<label style="display:inline-flex;align-items:center;gap:6px;margin:0;"><input type="radio" name="article_type" value="support" /> <span>Support</span></label>';
		echo '</fieldset>';
		echo '</td></tr>';

		echo '<tr id="amanah_pillar_row"><th scope="row"><label for="amanah_pillar_keyword_id">Pilar</label></th><td><select id="amanah_pillar_keyword_id" name="pillar_keyword_id">';
		echo '<option value="">— Pilih Pilar —</option>';
		foreach ( $pillar_keywords as $pillar ) {
			echo '<option value="' . esc_attr( (string) $pillar['id'] ) . '">' . esc_html( $pillar['phrase'] ) . '</option>';
		}
		echo '</select><p class="description">Wajib dipilih untuk artikel Support.</p></td></tr>';

		echo '<tr><th scope="row"><label for="amanah_keyword_target_url">Target URL</label></th><td>';
		echo '<input type="text" id="amanah_keyword_target_url" name="target_url" class="regular-text" placeholder="/jual-iphone/" required />';
		echo '<p class="description">URL halaman yang akan dimiliki keyword.</p></td></tr>';

		echo '</tbody></table>';
		echo '<p>';
		echo '<button type="submit" class="button button-primary">Add Keyword</button> ';
		echo '<button type="submit" name="amanah_keyword_action" value="scan_pillar_article" class="button" id="amanah_scan_pillar_button" formnovalidate style="display:none;">Scan Artikel Pilar</button>';
		echo '</p>';
		echo '</form>';

		echo '<h2>' . esc_html__( 'Keyword Records', 'amanah-seo-engine' ) . '</h2>';
		if ( empty( $keywords ) ) {
			echo '<p>No keyword records yet.</p>';
		} else {
			echo '<table class="widefat striped"><thead><tr>';
			echo '<th>ID</th><th>Keyword</th><th>Type Artikel</th><th>Pilar</th><th>Owner URL</th><th>Status</th>';
			echo '</tr></thead><tbody>';
			foreach ( $keywords as $keyword ) {
				$data = $keyword->toArray();
				$owner_url = null !== $data['owner_url_id'] ? $this->find_owner_url->execute( (int) $data['owner_url_id'] ) : null;
				$owner_url_text = null !== $owner_url ? $owner_url->getUrl() : 'Unassigned';
				$pillar_text = '—';
				if ( null !== $data['pillar_keyword_id'] ) {
					foreach ( $keywords as $possible_pillar ) {
						$p = $possible_pillar->toArray();
						if ( (int) $p['id'] === (int) $data['pillar_keyword_id'] ) {
							$pillar_text = $p['phrase'];
							break;
						}
					}
				}
				echo '<tr>';
				echo '<td>' . esc_html( (string) $data['id'] ) . '</td>';
				echo '<td>' . esc_html( $data['phrase'] ) . '</td>';
				echo '<td>' . esc_html( 'pillar' === $data['article_type'] ? 'Pilar' : 'Support' ) . '</td>';
				echo '<td>' . esc_html( $pillar_text ) . '</td>';
				echo '<td>' . esc_html( $owner_url_text ) . '</td>';
				echo '<td>' . esc_html( $data['status'] ) . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		}
		echo '</div>';
		$this->renderArticleTypeScript();
	}

	private function renderArticleTypeScript(): void {
		echo '<script>
document.addEventListener("DOMContentLoaded", function () {
	const radios = document.querySelectorAll("input[name=\"article_type\"]");
	const pillarRow = document.getElementById("amanah_pillar_row");
	const pillarSelect = document.getElementById("amanah_pillar_keyword_id");
	const scanButton = document.getElementById("amanah_scan_pillar_button");

	function syncArticleType() {
		const selected = document.querySelector("input[name=\"article_type\"]:checked");
		const isSupport = selected && "support" === selected.value;

		if (pillarRow) {
			pillarRow.style.display = isSupport ? "" : "none";
		}
		if (pillarSelect) {
			pillarSelect.required = !!isSupport;
			if (!isSupport) {
				pillarSelect.value = "";
			}
		}
		if (scanButton) {
			scanButton.style.display = isSupport ? "" : "none";
		}
	}

	radios.forEach(function (radio) {
		radio.addEventListener("change", syncArticleType);
	});
	syncArticleType();
});
</script>';
	}

	private function handlePost(): void {
		$action = sanitize_key( wp_unslash( (string) ( $_POST['amanah_keyword_action'] ?? '' ) ) );
		if ( 'scan_pillar_article' === $action ) {
			$this->handleScanPillarArticle();
			return;
		}
		if ( 'create_support_candidates' === $action ) {
			$this->handleCreateSupportCandidates();
			return;
		}
		check_admin_referer( 'amanah_create_keyword', 'amanah_keyword_nonce' );
		$phrase = sanitize_text_field( wp_unslash( (string) ( $_POST['keyword_phrase'] ?? '' ) ) );
		$article_type = sanitize_key( wp_unslash( (string) ( $_POST['article_type'] ?? 'pillar' ) ) );
		$pillar_keyword_id = isset( $_POST['pillar_keyword_id'] ) ? absint( $_POST['pillar_keyword_id'] ) : 0;
		$target_url = trim( sanitize_text_field( wp_unslash( (string) ( $_POST['target_url'] ?? '' ) ) ) );

		if ( ! in_array( $article_type, array( 'pillar', 'support' ), true ) ) $article_type = 'pillar';
		if ( 'pillar' === $article_type ) $pillar_keyword_id = null;
		if ( '' !== $target_url && '/' === substr( $target_url, 0, 1 ) ) $target_url = home_url( $target_url );

		if ( '' === $phrase || '' === $target_url || ( 'support' === $article_type && ! $pillar_keyword_id ) ) {
			add_settings_error( 'amanah_keywords', 'keyword_required_fields', 'Keyword, Target URL, dan Pilar untuk Support wajib diisi.', 'error' );
			return;
		}

		try {
			$created = $this->create_keyword->execute( new CreateKeywordCommand( $phrase, 'primary', $target_url, $article_type, $pillar_keyword_id ? (int) $pillar_keyword_id : null ) );
			add_settings_error( 'amanah_keywords', 'keyword_created', 'Keyword created successfully.', 'success' );

			if ( 'pillar' === $article_type && null !== $created->toArray()['id'] ) {
				$created_data = $created->toArray();
				$pillar_id = (int) $created_data['id'];
				$suggestions = $this->generate_potential_support_keywords->execute( $pillar_id );

				if ( ! empty( $suggestions ) ) {
					$owner_url = null;
					if ( null !== $created_data['owner_url_id'] ) {
						$owner_url = $this->find_owner_url->execute( (int) $created_data['owner_url_id'] );
					}

					if ( null === $owner_url ) {
						throw new \RuntimeException( 'Owner URL for the new pillar keyword could not be resolved.' );
					}

					$candidates = array();
					foreach ( $suggestions as $suggestion ) {
						$candidates[] = array(
							'keyword' => (string) $suggestion['keyword'],
							'target_url' => $owner_url->getUrl(),
						);
					}

					$created_support = $this->create_support_keywords->execute( $pillar_id, $candidates );
					add_settings_error(
						'amanah_keywords',
						'support_keywords_auto_created',
						count( $created_support ) . ' Keyword Support dari Keyword Intelligence/Autocomplete otomatis dimasukkan ke Support Coverage.',
						'success'
					);
				}

				$this->generated_suggestions = array(
					'pillar_keyword_id' => $pillar_id,
					'pillar_phrase' => $phrase,
					'items' => $suggestions,
				);
			}
		} catch ( \Throwable $e ) {
			add_settings_error( 'amanah_keywords', 'keyword_create_error', $e->getMessage(), 'error' );
		}
	}

	private function handleScanPillarArticle(): void {
		check_admin_referer( 'amanah_create_keyword', 'amanah_keyword_nonce' );
		$pillar_id = absint( $_POST['pillar_keyword_id'] ?? 0 );
		$phrase = sanitize_text_field( wp_unslash( (string) ( $_POST['keyword_phrase'] ?? '' ) ) );

		if ( 0 === $pillar_id || '' === $phrase ) {
			add_settings_error( 'amanah_keywords', 'pillar_scan_required', 'Keyword Support dan Pilar wajib diisi sebelum scan.', 'error' );
			return;
		}

		try {
			$this->pillar_scan_result = $this->scan_pillar_article->execute( $pillar_id, $phrase );
			add_settings_error( 'amanah_keywords', 'pillar_scan_complete', 'Scan artikel Pilar selesai.', 'success' );
		} catch ( \Throwable $e ) {
			add_settings_error( 'amanah_keywords', 'pillar_scan_error', $e->getMessage(), 'error' );
		}
	}

	private function handleCreateSupportCandidates(): void {
		check_admin_referer( 'amanah_create_support_keywords', 'amanah_support_nonce' );
		$pillar_id = absint( $_POST['pillar_keyword_id'] ?? 0 );
		$rows = isset( $_POST['support_candidates'] ) && is_array( $_POST['support_candidates'] ) ? wp_unslash( $_POST['support_candidates'] ) : array();
		$candidates = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || empty( $row['selected'] ) ) {
				continue;
			}
			$candidates[] = array(
				'keyword' => sanitize_text_field( (string) ( $row['keyword'] ?? '' ) ),
				'target_url' => esc_url_raw( (string) ( $row['target_url'] ?? '' ) ),
			);
		}

		if ( 0 === $pillar_id || empty( $candidates ) ) {
			add_settings_error( 'amanah_keywords', 'support_selection_required', 'Pilih minimal satu kandidat Support.', 'error' );
			return;
		}

		try {
			$created = $this->create_support_keywords->execute( $pillar_id, $candidates );
			add_settings_error( 'amanah_keywords', 'support_keywords_created', count( $created ) . ' Keyword Support berhasil dibuat.', 'success' );
		} catch ( \Throwable $e ) {
			add_settings_error( 'amanah_keywords', 'support_keyword_create_error', $e->getMessage(), 'error' );
		}
	}
}
