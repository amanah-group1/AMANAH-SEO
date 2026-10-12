<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Application\UseCase\GenerateStrategyWorkflow;
use Amanah\SEOEngine\Application\UseCase\ListKeywords;
use Amanah\SEOEngine\Application\UseCase\ResolveCluster;
use Amanah\SEOEngine\Application\UseCase\ListClusters;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Core\Bootstrap\Plugin;

defined( 'ABSPATH' ) || exit;

final class StrategyAdmin {

	private $generate_strategy;
	private $list_keywords;
	private $strategy_repository;
	private $resolve_cluster;
	private $list_clusters;
	private $generated_strategy;

	public function __construct(
		GenerateStrategyWorkflow $generate_strategy,
		ListKeywords $list_keywords,
		ContentStrategyRepositoryInterface $strategy_repository,
		ResolveCluster $resolve_cluster,
		ListClusters $list_clusters
	) {
		$this->generate_strategy = $generate_strategy;
		$this->list_keywords = $list_keywords;
		$this->strategy_repository = $strategy_repository;
		$this->resolve_cluster = $resolve_cluster;
		$this->list_clusters = $list_clusters;
		$this->generated_strategy = null;
	}

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage SEO strategy.', 'amanah-seo-engine' ) );
		}

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_strategy_action'] ) ) {
			$this->handlePost();
		}

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_cluster_action'] ) ) {
			$this->handleClusterPost();
		}

		$keywords = $this->list_keywords->execute();
		$strategies = $this->strategy_repository->findAll();

		$strategy_by_keyword = array();
		foreach ( $strategies as $strategy ) {
			$strategy_by_keyword[ $strategy->getKeywordId() ] = $strategy;
		}

		$standalone_keywords = array_values( array_filter( $keywords, static function ( $item ) {
			$data = $item->toArray();
			return null === $data['pillar_keyword_id'];
		} ) );
		$total = count( $standalone_keywords );
		$covered = 0;
		foreach ( $standalone_keywords as $item ) {
			$data = $item->toArray();
			if ( isset( $strategy_by_keyword[ $data['id'] ] ) ) {
				++$covered;
			}
		}
		$issues = 0;
		$cluster_ids = array();

		foreach ( $strategy_by_keyword as $strategy ) {
			$data = $strategy->getData();
			if ( ! empty( $data['issues'] ) ) {
				++$issues;
			}
			if ( isset( $data['cluster']['id'] ) && null !== $data['cluster']['id'] ) {
				$cluster_ids[ (int) $data['cluster']['id'] ] = true;
			}
		}

		$this->renderClusterManagement();

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'SEO Strategy', 'amanah-seo-engine' ) . '</h1>';
		settings_errors( 'amanah_seo_strategy' );

		echo '<div style="display:flex;gap:24px;flex-wrap:wrap;margin:16px 0;">';
		echo '<div><strong>Strategy Coverage</strong><br />' . esc_html( $total > 0 ? round( ( $covered / $total ) * 100 ) . '%' : '0%' ) . '</div>';
		echo '<div><strong>Strategy Keywords</strong><br />' . esc_html( (string) $total ) . '</div>';
		echo '<div><strong>Clusters</strong><br />' . esc_html( (string) count( $cluster_ids ) ) . '</div>';
		echo '<div><strong>Unassigned</strong><br />' . esc_html( (string) count( array_filter( $standalone_keywords, static function ( $item ) { return null === $item->toArray()['cluster_id']; } ) ) ) . '</div>';
		echo '<div><strong>Strategy Issues</strong><br />' . esc_html( (string) $issues ) . '</div>';
		echo '</div>';

		echo '<table class="widefat striped"><thead><tr>';
		echo '<th>Keyword</th><th>Type Artikel</th><th>Search Intent</th><th>Cluster</th><th>Owner URL</th><th>Content</th><th>Relationships</th><th>Status</th><th>Action</th>';
		echo '</tr></thead><tbody>';

		if ( empty( $keywords ) ) {
			echo '<tr><td colspan="9">No keyword records yet.</td></tr>';
		} else {
			foreach ( $keywords as $keyword ) {
				$data = $keyword->toArray();
				$strategy = isset( $strategy_by_keyword[ $data['id'] ] ) ? $strategy_by_keyword[ $data['id'] ] : null;
				if ( null === $strategy && null !== $this->generated_strategy && $this->generated_strategy->keyword_id === (int) $data['id'] ) {
					$strategy = $this->strategy_repository->findByKeywordId( (int) $data['id'] );
				}
				$strategy_data = null !== $strategy ? $strategy->getData() : array();

				$is_pillar = 'pillar' === $data['article_type'];

				if ( $is_pillar ) {
					$covered_intents = isset( $strategy_data['intent_coverage'] ) && is_array( $strategy_data['intent_coverage'] )
						? $strategy_data['intent_coverage']
						: array();

					$intent_names = array();
					foreach ( $covered_intents as $covered_intent ) {
						if ( isset( $covered_intent['name'] ) && '' !== trim( (string) $covered_intent['name'] ) ) {
							$intent_names[] = (string) $covered_intent['name'];
						}
					}

					$intent = ! empty( $intent_names ) ? implode( ', ', $intent_names ) : 'All intents';
					$cluster = 'Not required';
				} else {
					$intent = isset( $strategy_data['search_intent']['name'] ) ? $strategy_data['search_intent']['name'] : 'Unassigned';
					$cluster = isset( $strategy_data['cluster']['name'] ) ? $strategy_data['cluster']['name'] : 'Unassigned';
				}
				$owner_url = isset( $strategy_data['owner_url']['url'] ) ? $strategy_data['owner_url']['url'] : 'Unassigned';
				$content_requirement = isset( $strategy_data['content_requirement'] ) ? $strategy_data['content_requirement'] : '—';
				$relationship_count = isset( $strategy_data['relationships'] ) && is_array( $strategy_data['relationships'] ) ? count( $strategy_data['relationships'] ) : 0;
				$is_pillar_support = null !== $data['pillar_keyword_id'] && 'support' === $data['article_type'];
				$status = null !== $strategy ? $strategy->getStatus() : ( $is_pillar_support ? 'Covered by Pillar' : 'Not generated' );

				echo '<tr>';
				echo '<td>' . esc_html( $data['phrase'] ) . '</td>';
				echo '<td>' . esc_html( 'pillar' === $data['article_type'] ? 'Pilar' : 'Support' ) . '</td>';
				echo '<td>' . esc_html( $intent ) . '</td>';
				echo '<td>' . esc_html( $cluster ) . '</td>';
				echo '<td>' . esc_html( $owner_url ) . '</td>';
				echo '<td>' . esc_html( $content_requirement ) . '</td>';
				if ( $is_pillar ) {
					$support_coverage = isset( $strategy_data['support_keywords'] ) && is_array( $strategy_data['support_keywords'] ) ? count( $strategy_data['support_keywords'] ) : 0;
					$relationship_count = $support_coverage > 0 ? $support_coverage : $relationship_count;
				}
				echo '<td>' . esc_html( (string) $relationship_count ) . '</td>';
				echo '<td>' . esc_html( $status ) . '</td>';
				echo '<td>';
				if ( 'support' === $data['article_type'] && ! $is_pillar_support ) {
					$this->renderAssignmentForm( $data );
				}
				echo '<form method="post" style="margin:8px 0 0;">';
				wp_nonce_field( 'amanah_generate_strategy', 'amanah_strategy_nonce' );
				echo '<input type="hidden" name="amanah_strategy_action" value="generate" />';
				echo '<input type="hidden" name="keyword_id" value="' . esc_attr( (string) $data['id'] ) . '" />';
				if ( ! $is_pillar_support ) {
					echo '<button type="submit" class="button">' . esc_html( null !== $strategy ? 'Refresh Strategy' : 'Generate Strategy' ) . '</button>';
				} else {
					echo '<span>Coverage inherited from Pillar</span>';
				}
				echo '</form>';
				echo '</td>';
				echo '</tr>';
			}
		}

		echo '</tbody></table>';
		echo '</div>';
	}



	private function renderClusterManagement(): void {
		echo '<div class="postbox" style="margin:16px 0;padding:16px;">';
		echo '<h2 style="margin-top:0;">Cluster Management</h2>';
		echo '<p>Buat Cluster untuk mengelompokkan keyword sebelum SEO Strategy dibuat.</p>';
		echo '<form method="post">';
		wp_nonce_field( 'amanah_create_cluster', 'amanah_cluster_nonce' );
		echo '<input type="hidden" name="amanah_cluster_action" value="create" />';
		echo '<input type="text" name="cluster_name" class="regular-text" placeholder="Nama Cluster" required /> ';
		echo '<input type="text" name="cluster_slug" class="regular-text" placeholder="Slug" required /> ';
		echo '<input type="text" name="cluster_description" class="regular-text" placeholder="Deskripsi (opsional)" /> ';
		echo '<button type="submit" class="button button-primary">Create Cluster</button>';
		echo '</form>';
		$clusters=$this->list_clusters->execute();
		if(empty($clusters)){echo '<p>No clusters yet.</p>';}else{
			echo '<table class="widefat striped" style="margin-top:12px;"><thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Status</th></tr></thead><tbody>';
			foreach($clusters as $cluster){echo '<tr><td>'.esc_html((string)$cluster->id).'</td><td>'.esc_html($cluster->name).'</td><td>'.esc_html($cluster->slug).'</td><td>'.esc_html($cluster->status).'</td></tr>';}
			echo '</tbody></table>';
		}
		echo '</div>';
	}

	private function handleClusterPost(): void {
		check_admin_referer( 'amanah_create_cluster', 'amanah_cluster_nonce' );
		$name=sanitize_text_field(wp_unslash((string)($_POST['cluster_name']??'')));
		$slug=sanitize_title(wp_unslash((string)($_POST['cluster_slug']??'')));
		$description=sanitize_textarea_field(wp_unslash((string)($_POST['cluster_description']??'')));
		if(''===$name||''===$slug){add_settings_error('amanah_seo_strategy','cluster_required','Nama dan slug Cluster wajib diisi.','error');return;}
		try{$this->resolve_cluster->execute($slug,$name,$description);add_settings_error('amanah_seo_strategy','cluster_created','Cluster berhasil dibuat.','success');}
		catch(\Throwable $e){add_settings_error('amanah_seo_strategy','cluster_error',$e->getMessage(),'error');}
	}

	private function renderAssignmentForm( array $data ): void {
		$services = Plugin::boot();
		$intents = $services->get( 'search_intent_repository' )->findAll();
		$clusters = $services->get( 'cluster_repository' )->findAll();

		echo '<form method="post" style="margin:0 0 8px;">';
		wp_nonce_field( 'amanah_assign_strategy_context', 'amanah_strategy_context_nonce' );
		echo '<input type="hidden" name="amanah_strategy_action" value="assign_context" />';
		echo '<input type="hidden" name="keyword_id" value="' . esc_attr( (string) $data['id'] ) . '" />';
		echo '<select name="search_intent_id" style="max-width:160px;">';
		echo '<option value="0">Search Intent</option>';
		foreach ( $intents as $intent ) {
			$selected = null !== $data['search_intent_id'] && (int) $data['search_intent_id'] === (int) $intent->getId() ? ' selected="selected"' : '';
			echo '<option value="' . esc_attr( (string) $intent->getId() ) . '"' . $selected . '>' . esc_html( $intent->getName() ) . '</option>';
		}
		echo '</select> ';
		echo '<select name="cluster_id" style="max-width:160px;">';
		echo '<option value="0">Cluster</option>';
		foreach ( $clusters as $cluster ) {
			$selected = null !== $data['cluster_id'] && (int) $data['cluster_id'] === (int) $cluster->getId() ? ' selected="selected"' : '';
			echo '<option value="' . esc_attr( (string) $cluster->getId() ) . '"' . $selected . '>' . esc_html( $cluster->getName() ) . '</option>';
		}
		echo '</select> ';
		echo '<button type="submit" class="button button-small">Save Context</button>';
		echo '</form>';
	}

	private function handleAssignContext(): void {
		check_admin_referer( 'amanah_assign_strategy_context', 'amanah_strategy_context_nonce' );
		$keyword_id = absint( $_POST['keyword_id'] ?? 0 );
		$intent_id = absint( $_POST['search_intent_id'] ?? 0 );
		$cluster_id = absint( $_POST['cluster_id'] ?? 0 );

		if ( $keyword_id < 1 || $intent_id < 1 || $cluster_id < 1 ) {
			add_settings_error( 'amanah_seo_strategy', 'strategy_context_required', 'Search Intent dan Cluster wajib dipilih.', 'error' );
			return;
		}

		try {
			$services = Plugin::boot();
			$services->get( 'assign_keyword_search_intent' )->execute( $keyword_id, $intent_id );
			$services->get( 'assign_keyword_cluster' )->execute( $keyword_id, $cluster_id );
			add_settings_error( 'amanah_seo_strategy', 'strategy_context_saved', 'Search Intent dan Cluster berhasil disimpan.', 'success' );
		} catch ( \Throwable $e ) {
			add_settings_error( 'amanah_seo_strategy', 'strategy_context_error', $e->getMessage(), 'error' );
		}
	}

	private function handlePost(): void {
		$action = sanitize_key( wp_unslash( (string) ( $_POST['amanah_strategy_action'] ?? '' ) ) );

		if ( 'assign_context' === $action ) {
			$this->handleAssignContext();
			return;
		}

		check_admin_referer( 'amanah_generate_strategy', 'amanah_strategy_nonce' );

		$keyword_id = absint( $_POST['keyword_id'] ?? 0 );

		if ( $keyword_id < 1 ) {
			add_settings_error( 'amanah_seo_strategy', 'strategy_invalid_keyword', 'Invalid keyword.', 'error' );
			return;
		}

		try {
			$this->generated_strategy = $this->generate_strategy->execute( $keyword_id );
			add_settings_error( 'amanah_seo_strategy', 'strategy_generated', 'SEO strategy generated successfully.', 'success' );
		} catch ( \Throwable $e ) {
			add_settings_error( 'amanah_seo_strategy', 'strategy_generation_failed', 'SEO strategy could not be generated.', 'error' );
		}
	}
}
