<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Application\UseCase\FindOwnerUrl;
use Amanah\SEOEngine\Application\UseCase\ListKeywords;

defined( 'ABSPATH' ) || exit;

final class UrlMappingAdmin {

	private $list_keywords;
	private $find_owner_url;

	public function __construct( ListKeywords $list_keywords, FindOwnerUrl $find_owner_url ) {
		$this->list_keywords = $list_keywords;
		$this->find_owner_url = $find_owner_url;
	}

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage URL mappings.', 'amanah-seo-engine' ) );
		}

		$keywords = $this->list_keywords->execute();
		$mapped = 0;
		$unassigned = 0;
		$existing_pages = 0;
		$new_pages = 0;
		$rows = array();
		$owner_counts = array();

		foreach ( $keywords as $keyword ) {
			$data = $keyword->toArray();
			$owner = null !== $data['owner_url_id']
				? $this->find_owner_url->execute( (int) $data['owner_url_id'] )
				: null;

			if ( null === $owner ) {
				++$unassigned;
				$rows[] = array(
					'keyword' => $data['phrase'],
					'type' => $data['keyword_type'],
					'owner_url' => 'Unassigned',
					'page' => '—',
					'status' => 'Unassigned',
				);
				continue;
			}

			++$mapped;
			$owner_id = (int) $owner->getId();
			$owner_counts[ $owner_id ] = isset( $owner_counts[ $owner_id ] ) ? $owner_counts[ $owner_id ] + 1 : 1;

			if ( null !== $owner->getWordPressPostId() ) {
				++$existing_pages;
				$page = 'Existing Page';
			} else {
				++$new_pages;
				$page = 'New Page';
			}

			$rows[] = array(
				'keyword' => $data['phrase'],
				'type' => $data['keyword_type'],
				'owner_url' => $owner->getUrl(),
				'page' => $page,
				'status' => 'Mapped',
			);
		}

		$shared_owners = 0;
		foreach ( $owner_counts as $count ) {
			if ( $count > 1 ) {
				++$shared_owners;
			}
		}

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'URL Mapping', 'amanah-seo-engine' ) . '</h1>';
		echo '<p>' . esc_html__( 'URL Mapping shows keyword ownership, target-page state, and mapping conditions. Ownership changes remain controlled by the keyword workflow.', 'amanah-seo-engine' ) . '</p>';

		echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;max-width:1100px;margin:20px 0;">';
		$this->card( 'Keywords', count( $keywords ) );
		$this->card( 'Mapped', $mapped );
		$this->card( 'Unassigned', $unassigned );
		$this->card( 'Existing Pages', $existing_pages );
		$this->card( 'New Pages', $new_pages );
		$this->card( 'Shared URLs', $shared_owners );
		echo '</div>';

		echo '<h2>' . esc_html__( 'URL Ownership', 'amanah-seo-engine' ) . '</h2>';
		echo '<table class="widefat striped">';
		echo '<thead><tr><th>Keyword</th><th>Type</th><th>Owner URL</th><th>Page</th><th>Status</th></tr></thead><tbody>';

		if ( empty( $rows ) ) {
			echo '<tr><td colspan="5">' . esc_html__( 'No keyword records yet.', 'amanah-seo-engine' ) . '</td></tr>';
		} else {
			foreach ( $rows as $row ) {
				echo '<tr>';
				echo '<td>' . esc_html( $row['keyword'] ) . '</td>';
				echo '<td>' . esc_html( ucfirst( $row['type'] ) ) . '</td>';
				echo '<td>' . esc_html( $row['owner_url'] ) . '</td>';
				echo '<td>' . esc_html( $row['page'] ) . '</td>';
				echo '<td>' . esc_html( $row['status'] ) . '</td>';
				echo '</tr>';
			}
		}

		echo '</tbody></table>';

		if ( $shared_owners > 0 ) {
			echo '<div class="notice notice-warning" style="margin-top:16px;"><p>';
			echo esc_html__( 'Some Owner URLs are shared by multiple keywords. This is shown as an observation; Amanah SEO Engine does not automatically remap ownership.', 'amanah-seo-engine' );
			echo '</p></div>';
		}

		echo '</div>';
	}

	private function card( string $label, int $value ): void {
		echo '<div style="padding:16px;background:#fff;border:1px solid #dcdcde;box-sizing:border-box;">';
		echo '<div style="font-size:12px;text-transform:uppercase;color:#646970;">' . esc_html( $label ) . '</div>';
		echo '<strong style="display:block;font-size:24px;margin-top:6px;">' . esc_html( (string) $value ) . '</strong>';
		echo '</div>';
	}
}
