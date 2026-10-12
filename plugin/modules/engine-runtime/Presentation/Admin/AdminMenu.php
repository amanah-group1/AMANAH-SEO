<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Application\UseCase\CreateKeyword;
use Amanah\SEOEngine\Application\UseCase\GeneratePotentialSupportKeywords;
use Amanah\SEOEngine\Application\UseCase\CreateSupportKeywordsFromCandidates;
use Amanah\SEOEngine\Application\UseCase\ListKeywords;
use Amanah\SEOEngine\Application\UseCase\FindOwnerUrl;
use Amanah\SEOEngine\Application\UseCase\ScanPillarArticle;
use Amanah\SEOEngine\Application\UseCase\GenerateStrategyWorkflow;
use Amanah\SEOEngine\Application\UseCase\GenerateContentPlan;
use Amanah\SEOEngine\Application\UseCase\GenerateContentBrief;
use Amanah\SEOEngine\Application\UseCase\GeneratePageBlueprint;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentPlanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\UseCase\GenerateContent;
use Amanah\SEOEngine\Application\UseCase\CreateManualContent;
use Amanah\SEOEngine\Application\UseCase\EvaluateContentQuality;
use Amanah\SEOEngine\Application\UseCase\ReviewContent;
use Amanah\SEOEngine\Application\UseCase\PublishContent;
use Amanah\SEOEngine\Application\UseCase\SyncWordPressContent;
use Amanah\SEOEngine\Application\UseCase\UpdateContent;
use Amanah\SEOEngine\Application\UseCase\CreateSearchIntent;
use Amanah\SEOEngine\Application\UseCase\ResolveCluster;
use Amanah\SEOEngine\Application\UseCase\ListClusters;
use Amanah\SEOEngine\Application\UseCase\ListSearchIntents;
use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;
use Amanah\SEOEngine\Platform\License\LicenseClient;
use Amanah\SEOEngine\Platform\Update\UpdateService;
use Amanah\SEOEngine\Core\Bootstrap\Plugin;

defined( 'ABSPATH' ) || exit;

final class AdminMenu {

	private $renderer;
	private $license_client;
	private $update_service;
	private $keyword_admin;
	private $search_intent_admin;
	private $strategy_admin;
	private $content_plan_admin;
	private $page_blueprint_admin;
	private $url_mapping_admin;
	private $content_editor_admin;
	private $content_scanning_admin;

	public function __construct(
		Configuration $configuration,
		?LicenseClient $license_client = null,
		?UpdateService $update_service = null,
		?CreateKeyword $create_keyword = null,
		?ListKeywords $list_keywords = null,
		?GeneratePotentialSupportKeywords $generate_potential_support_keywords = null,
		?CreateSupportKeywordsFromCandidates $create_support_keywords = null,
		?ScanPillarArticle $scan_pillar_article = null,
		?CreateSearchIntent $create_search_intent = null,
		?ListSearchIntents $list_search_intents = null,
		?ResolveCluster $resolve_cluster = null,
		?ListClusters $list_clusters = null,
		?FindOwnerUrl $find_owner_url = null,
		?GenerateStrategyWorkflow $generate_strategy = null,
		?ContentStrategyRepositoryInterface $strategy_repository = null,
		?GenerateContentPlan $generate_content_plan = null,
		?ContentPlanRepositoryInterface $plan_repository = null,
		?GenerateContentBrief $generate_brief = null,
		?ContentBriefRepositoryInterface $brief_repository = null,
		?GeneratePageBlueprint $generate_page_blueprint = null,
		?PageBlueprintRepositoryInterface $page_blueprint_repository = null,
		?GenerateContent $generate_content = null,
		?ContentRepositoryInterface $content_repository = null,
		?CreateManualContent $create_manual_content = null,
		?EvaluateContentQuality $evaluate_content_quality = null,
		?ReviewContent $review_content = null,
		?PublishContent $publish_content = null,
		?SyncWordPressContent $sync_wordpress_content = null,
		?UpdateContent $update_content = null
	) {
		$this->renderer = new AdminRenderer( $configuration );
		$this->license_client = $license_client;
		$this->update_service = $update_service;
		$this->keyword_admin = null !== $create_keyword && null !== $list_keywords && null !== $find_owner_url && null !== $generate_potential_support_keywords && null !== $create_support_keywords && null !== $scan_pillar_article
			? new KeywordAdmin( $create_keyword, $list_keywords, $find_owner_url, $generate_potential_support_keywords, $create_support_keywords, $scan_pillar_article )
			: null;
		$this->url_mapping_admin = null !== $list_keywords && null !== $find_owner_url
			? new UrlMappingAdmin( $list_keywords, $find_owner_url )
			: null;
		$this->search_intent_admin = null !== $create_search_intent && null !== $list_search_intents
			? new SearchIntentAdmin( $create_search_intent, $list_search_intents )
			: null;
		$this->strategy_admin = null !== $generate_strategy && null !== $list_keywords && null !== $strategy_repository
			? new StrategyAdmin( $generate_strategy, $list_keywords, $strategy_repository, $resolve_cluster, $list_clusters )
			: null;
		$this->content_plan_admin = null !== $generate_content_plan && null !== $list_keywords && null !== $plan_repository
			? new ContentPlanAdmin( $generate_content_plan, $list_keywords, $plan_repository, $generate_brief, $brief_repository, $content_repository )
			: null;
		$this->content_editor_admin = null !== $update_content ? new ContentEditorAdmin( $update_content ) : null;
		$this->content_scanning_admin = new ContentScanningAdmin( Plugin::boot()->get( 'run_content_scanning' ), Plugin::boot()->get( 'save_scanning_review' ), Plugin::boot()->get( 'generate_outline_from_scanning' ), Plugin::boot()->get( 'content_scan_repository' ), Plugin::boot()->get( 'serp_result_repository' ), Plugin::boot()->get( 'keyword_repository' ) );
		$this->page_blueprint_admin = null !== $generate_page_blueprint && null !== $brief_repository && null !== $page_blueprint_repository && null !== $generate_content && null !== $content_repository
			? new PageBlueprintAdmin( $generate_page_blueprint, $brief_repository, $page_blueprint_repository, $generate_content, $content_repository, $create_manual_content, Plugin::boot()->get( 'quality_result_repository' ), $evaluate_content_quality, $review_content, $publish_content, $sync_wordpress_content, $this->content_editor_admin, Plugin::boot()->get( 'save_content_outline' ) )
			: null;
	}

	public function register(): void {
		// Satu menu utama Amanah SEO Engine. Halaman utama berisi License,
		// Updates, dan About. Tidak ada menu License & Updates terpisah.
		add_menu_page(
			'Amanah SEO Engine',
			'Amanah SEO Engine',
			'manage_options',
			'amanah-seo-engine',
			array( $this, 'renderProductHome' ),
			'dashicons-chart-area',
			30
		);

		// Dashboard tetap menjadi submenu langsung dari Amanah SEO Engine.
		add_submenu_page(
			'amanah-seo',
			'Dashboard',
			'Dashboard',
			'manage_options',
			'amanah-seo-engine-dashboard',
			array( $this, 'renderDashboard' )
		);

		$groups = array(
			'website' => array( 'title' => 'Live Website', 'first' => 'live-scan' ),
			'seo' => array( 'title' => 'SEO', 'first' => 'keywords' ),
			'content' => array( 'title' => 'Content', 'first' => 'content-plan' ),
			'links' => array( 'title' => 'Links', 'first' => 'internal-links' ),
			'issues' => array( 'title' => 'Issues', 'first' => 'seo-issues' ),
			'monitoring' => array( 'title' => 'Monitoring', 'first' => 'monitoring-performance' ),
			'ai' => array( 'title' => 'AI', 'first' => 'ai-configuration' ),
			'administration' => array( 'title' => 'Administration', 'first' => 'import-export' ),
		);

		$screens = AdminScreenRegistry::all();

		foreach ( $groups as $group => $definition ) {
			// WordPress tidak menyediakan nested submenu native. Group dibuat
			// sebagai heading non-clickable, sedangkan seluruh screen menjadi
			// submenu yang benar-benar dapat dibuka.
			add_submenu_page(
				'amanah-seo-engine',
				$definition['title'],
				'<span class="amanah-seo-menu-group" data-group="' . esc_attr( $group ) . '">' . esc_html( $definition['title'] ) . '</span>',
				'manage_options',
				'amanah-seo-engine-' . $group,
				function () use ( $definition ): void { $this->renderScreen( $definition['first'] ); }
			);

			foreach ( $screens as $slug => $screen ) {
				if ( $screen['group'] !== $group ) {
					continue;
				}

				if ( 'administration' === $group && in_array( $slug, array( 'license', 'updates' ), true ) ) {
					continue;
				}

				add_submenu_page(
					'amanah-seo-engine',
					$screen['title'],
					'<span class="amanah-seo-menu-child">&nbsp;&nbsp;&nbsp;' . esc_html( $screen['menu_title'] ) . '</span>',
					$screen['capability'],
					'amanah-seo-engine-' . $slug,
					function () use ( $slug ): void { $this->renderScreen( $slug ); }
				);
			}
		}

		add_action( 'admin_head', array( $this, 'renderMenuGroupingStyles' ) );
		add_action( 'admin_footer', array( $this, 'renderAdminActionLoading' ) );

		if ( null !== $this->content_editor_admin ) {
			add_submenu_page( '', 'Edit Content', 'Edit Content', 'manage_options', 'amanah-seo-engine-content-editor', array( $this->content_editor_admin, 'render' ) );
		}
	}

	public function renderMenuGroupingStyles(): void {
		if ( 0 !== strpos( sanitize_key( (string) ( $_GET['page'] ?? '' ) ), 'amanah-seo-engine' ) && 'amanah-seo-engine' !== sanitize_key( (string) ( $_GET['page'] ?? '' ) ) ) {
			return;
		}
		?>
		<style>
			#adminmenu #toplevel_page_amanah-seo > a {
				cursor: pointer;
			}
			#adminmenu .amanah-seo-menu-group-link {
				cursor: pointer;
			}
			#adminmenu .amanah-seo-menu-group {
				display: block;
				font-weight: 600;
				position: relative;
			}
			#adminmenu .amanah-seo-menu-group::after {
				content: "▾";
				position: absolute;
				right: 16px;
				font-size: 12px;
			}
			#adminmenu .amanah-seo-menu-group-link.amanah-seo-menu-collapsed .amanah-seo-menu-group::after {
				content: "▸";
			}
			#adminmenu .amanah-seo-menu-child {
			display: block;
			}
		</style>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
			// Child menu selalu ditampilkan. Group hanya berfungsi sebagai
			// heading navigasi agar seluruh workflow dapat terlihat langsung.
			var groups = document.querySelectorAll('#adminmenu .amanah-seo-menu-group');
			groups.forEach(function (label) {
				var link = label.closest('a');
				if (!link) {
					return;
				}
				link.classList.add('amanah-seo-menu-group-link');
				link.setAttribute('aria-expanded', 'true');
				link.classList.remove('amanah-seo-menu-collapsed');
			});
		});
		</script>
		<?php
	}


	public function renderAdminActionLoading(): void {
		$page = sanitize_key( (string) ( $_GET['page'] ?? '' ) );
		if ( 'amanah-seo-engine' !== $page && 0 !== strpos( $page, 'amanah-seo-engine-' ) ) {
			return;
		}
		// Content Production already has its own workflow-specific loading overlay.
		if ( 'amanah-seo-engine-content-production' === $page ) {
			return;
		}
		?>
		<div id="amanah-admin-action-busy" aria-hidden="true" style="display:none;position:fixed;inset:0;background:rgba(255,255,255,.72);z-index:999999;align-items:center;justify-content:center;">
			<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:24px 28px;box-shadow:0 8px 30px rgba(0,0,0,.12);text-align:center;min-width:300px;max-width:90vw;">
				<div class="amanah-admin-action-spinner" aria-hidden="true"></div>
				<strong id="amanah-admin-action-title" style="display:block;font-size:16px;margin-top:12px;">Sedang memproses...</strong>
				<p id="amanah-admin-action-text" style="margin:8px 0 0;color:#50575e;">Mohon tunggu. Jangan klik tombol lain selama proses berjalan.</p>
			</div>
		</div>
		<style>
			.amanah-admin-action-spinner{width:34px;height:34px;margin:0 auto;border:4px solid #dcdcde;border-top-color:#2271b1;border-radius:50%;animation:amanahAdminActionSpin .8s linear infinite}
			@keyframes amanahAdminActionSpin{to{transform:rotate(360deg)}}
			body.amanah-admin-action-busy #wpcontent{pointer-events:none}
		</style>
		<script>
			document.addEventListener('DOMContentLoaded', function () {
				var overlay = document.getElementById('amanah-admin-action-busy');
				var title = document.getElementById('amanah-admin-action-title');
				var message = document.getElementById('amanah-admin-action-text');
				if (!overlay) {
					return;
				}

				var states = {
					'amanah_keyword_action:create': ['Creating Keyword...', 'Sedang membuat keyword dan menyiapkan data awal.'],
					'amanah_keyword_action:scan_pillar_article': ['Scanning Pillar Article...', 'Sedang memindai artikel Pilar. Mohon tunggu.'],
					'amanah_keyword_action:create_support_candidates': ['Creating Support Keywords...', 'Sedang membuat keyword Support yang dipilih.'],
					'amanah_strategy_action:generate': ['Generating Strategy...', 'Sedang menyusun strategi SEO. Mohon tunggu.'],
					'amanah_cluster_action:resolve': ['Resolving Cluster...', 'Sedang menentukan cluster keyword. Mohon tunggu.'],
					'amanah_content_plan_action:generate': ['Generating Content Plan...', 'Sedang menyusun Content Plan. Mohon tunggu.'],
					'amanah_content_plan_action:generate_brief': ['Generating Content Brief...', 'Sedang menyusun Content Brief. Mohon tunggu.'],
					'amanah_page_blueprint_action:generate': ['Creating Page Blueprint...', 'Sedang menyiapkan struktur artikel. Mohon tunggu.'],
					'amanah_page_blueprint_action:generate_content': ['Generating Content...', 'AI sedang menghasilkan konten berdasarkan Blueprint. Mohon tunggu.'],
					'amanah_page_blueprint_action:create_manual_content': ['Creating Manual Draft...', 'Sedang membuat draft awal dari Page Blueprint. Mohon tunggu.'],
					'amanah_page_blueprint_action:evaluate_quality': ['Evaluating Quality...', 'Konten sedang dievaluasi. Mohon tunggu.'],
					'amanah_page_blueprint_action:approve': ['Approving Content...', 'Persetujuan konten sedang diproses.'],
					'amanah_page_blueprint_action:request_revision': ['Requesting Revision...', 'Status konten sedang diperbarui.'],
					'amanah_page_blueprint_action:publish': ['Publishing Content...', 'Konten sedang dipublikasikan. Mohon tunggu.'],
					'amanah_page_blueprint_action:sync_wordpress_content': ['Syncing Content...', 'Konten sedang disinkronkan dengan WordPress.'],
					'amanah_license_action:activate': ['Activating License...', 'Lisensi sedang diaktifkan. Mohon tunggu.'],
					'amanah_license_action:validate': ['Validating License...', 'Lisensi sedang divalidasi. Mohon tunggu.'],
					'amanah_google_action:configure': ['Saving Google Configuration...', 'Konfigurasi Google sedang disimpan.'],
					'amanah_google_action:disconnect': ['Disconnecting Google...', 'Koneksi Google sedang diputus.'],
					'amanah_ai_configuration_action:save_provider': ['Saving AI Provider...', 'Konfigurasi AI provider sedang disimpan.'],
					'amanah_ai_configuration_action:discover_models': ['Discovering Models...', 'Sedang mencari model AI yang tersedia. Mohon tunggu.'],
					'amanah_ai_configuration_action:save_model': ['Saving AI Model...', 'Model AI sedang disimpan.'],
					'amanah_content_editor_action:save': ['Saving Content...', 'Konten sedang disimpan. Mohon tunggu.']
				};

				function getActionKey(form) {
					var fields = form.querySelectorAll('input[type="hidden"], select, button[name]');
					for (var i = 0; i < fields.length; i++) {
						var field = fields[i];
						var name = field.name || '';
						if (!name) {
							continue;
						}
						if (name.indexOf('action') === -1) {
							continue;
						}
						var value = field.value || '';
						if (value) {
							return name + ':' + value;
						}
					}
					return '';
				}

				document.querySelectorAll('form').forEach(function (form) {
					if (form.dataset.amanahLoadingBound === '1') {
						return;
					}
					if ('get' === (form.getAttribute('method') || 'get').toLowerCase()) {
						return;
					}

					form.addEventListener('submit', function (event) {
						if (form.dataset.amanahLoadingShown === '1') {
							return;
						}
						var submitter = event.submitter || document.activeElement;
						var key = getActionKey(form);
						var state = states[key] || ['Sedang memproses...', 'Mohon tunggu. Jangan klik tombol lain selama proses berjalan.'];

						if (title) {
							title.textContent = state[0];
						}
						if (message) {
							message.textContent = state[1];
						}
						overlay.style.display = 'flex';
						overlay.setAttribute('aria-hidden', 'false');
						document.body.classList.add('amanah-admin-action-busy');
						form.dataset.amanahLoadingShown = '1';

						document.querySelectorAll('button, input[type="submit"], input[type="button"]').forEach(function (button) {
							if (button !== submitter) {
								button.disabled = true;
								button.setAttribute('aria-disabled', 'true');
							}
						});
						if (submitter && (submitter.tagName === 'BUTTON' || submitter.type === 'submit')) {
							submitter.disabled = true;
							submitter.setAttribute('aria-disabled', 'true');
							submitter.textContent = 'Sedang berjalan...';
						}
					});
					form.dataset.amanahLoadingBound = '1';
				});
			});
		</script>
		<?php
	}

	public function handleContentProductionPost(): void {
		if ( null !== $this->page_blueprint_admin ) {
			$this->page_blueprint_admin->handlePostRequest();
		}
	}

	public function handleContentEditorSave(): void {
		if ( null !== $this->content_editor_admin ) {
			$this->content_editor_admin->handleSaveRequest();
		}
	}

	public function handleIssueRecommendationAction(): void {
		$page = sanitize_key( (string) ( $_GET['page'] ?? '' ) );
		$screen = 0 === strpos( $page, 'amanah-seo-engine-' ) ? substr( $page, strlen( 'amanah-seo-engine-' ) ) : '';
		if ( ! in_array( $screen, array( 'seo-issues', 'recommendations' ), true ) ) {
			return;
		}
		$this->renderer->handleIssueRecommendationAction( \Amanah\SEOEngine\Core\Bootstrap\Plugin::boot()->get( 'issue_recommendation_service' ), $screen );
	}

	private function renderScreen( string $slug ): void {
		if ( 'keywords' === $slug && null !== $this->keyword_admin ) {
			$this->keyword_admin->render();
			return;
		}
		if ( 'url-mapping' === $slug && null !== $this->url_mapping_admin ) {
			$this->url_mapping_admin->render();
			return;
		}
		if ( 'content-editor' === $slug && null !== $this->content_editor_admin ) {
			$this->content_editor_admin->render();
			return;
		}
		if ( 'content-production' === $slug && null !== $this->page_blueprint_admin ) {
			$this->page_blueprint_admin->render();
			return;
		}
		if ( 'content-scanning' === $slug ) {
			$this->content_scanning_admin->render();
			return;
		}
		if ( 'content-plan' === $slug && null !== $this->content_plan_admin ) {
			$this->content_plan_admin->render();
			return;
		}
		if ( 'seo-strategy' === $slug && null !== $this->strategy_admin ) {
			$this->strategy_admin->render();
			return;
		}
		if ( 'license' === $slug ) {
			$this->renderLicense();
			return;
		}
		if ( 'updates' === $slug ) {
			$this->renderUpdates();
			return;
		}
		$this->renderer->render( $slug );
	}

	public function renderLicense(): void {
		if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'You are not allowed to manage the license.', 'amanah-seo-engine' ) ); }
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_license_action'] ) && null !== $this->license_client ) {
			check_admin_referer( 'amanah_license_action', 'amanah_license_nonce' );
			$action = sanitize_key( (string) $_POST['amanah_license_action'] );
			try {
				if ( 'activate' === $action ) {
					$license_key = sanitize_text_field( wp_unslash( (string) ( $_POST['license_key'] ?? '' ) ) );
					$this->license_client->activate( $license_key );
					add_settings_error( 'amanah_license', 'license_activated', 'License activated successfully.', 'success' );
				} elseif ( 'validate' === $action ) {
					$state = $this->license_client->validate();
					if ( 'unconfigured' === $state->getStatus() ) {
						add_settings_error( 'amanah_license', 'license_not_configured', 'License is not configured. Please activate a license first.', 'error' );
					} elseif ( $state->isActive() ) {
						add_settings_error( 'amanah_license', 'license_validated', 'License validated successfully.', 'success' );
					} else {
						add_settings_error( 'amanah_license', 'license_validation_failed', 'License validation did not return an active license.', 'error' );
					}
				}
			} catch ( \Throwable $e ) {
				add_settings_error( 'amanah_license', 'license_error', $e->getMessage(), 'error' );
			}
		}
		$state = null !== $this->license_client ? $this->license_client->getState() : null;
		echo '<div class="wrap">';
		echo '<h1>License</h1>';
		settings_errors( 'amanah_license' );
		if ( null === $state ) {
			echo '<div class="notice notice-warning"><p>License service is not available.</p></div>';
			echo '</div>';
			return;
		}
		echo '<p><strong>Status:</strong> ' . esc_html( $state->getStatus() ) . '</p>';
		echo '<p><strong>Plan:</strong> ' . esc_html( $state->getPlan() ) . '</p>';
		echo '<p><strong>Expiration:</strong> ' . esc_html( (string) $state->getExpiration() ) . '</p>';
		echo '<p><strong>Entitlements:</strong> ' . esc_html( implode( ', ', $state->getEntitlements() ) ) . '</p>';
		echo '<form method="post">';
		wp_nonce_field( 'amanah_license_action', 'amanah_license_nonce' );
		echo '<input type="hidden" name="amanah_license_action" value="activate" />';
		echo '<input type="password" name="license_key" class="regular-text" autocomplete="off" />';
		echo ' <button class="button button-primary">Activate</button>';
		echo '</form>';
		echo '<form method="post" style="margin-top:10px">';
		wp_nonce_field( 'amanah_license_action', 'amanah_license_nonce' );
		echo '<input type="hidden" name="amanah_license_action" value="validate" />';
		echo ' <button class="button">Validate License</button>';
		echo '</form>';
		echo '</div>';
	}

	public function renderUpdates(): void {
		if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'You are not allowed to manage updates.', 'amanah-seo-engine' ) ); }
		echo '<div class="wrap">';
		echo '<h1>Updates</h1>';
		if ( null === $this->update_service || null === $this->license_client ) {
			echo '<div class="notice notice-warning"><p>Update service is not available.</p></div>';
			echo '</div>';
			return;
		}
		$check = $this->update_service->check( $this->license_client->hasEntitlement( 'premium_updates' ) );
		echo '<p><strong>Status:</strong> ' . esc_html( $check['available'] ? ( $check['eligible'] ? 'Update available and eligible.' : 'Update available but restricted.' ) : 'No update available.' ) . '</p>';
		echo '</div>';
	}

	public function renderProductHome(): void {
		$this->renderLicenseUpdates();
	}

	public function renderLicenseUpdates(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage Amanah SEO Engine.', 'amanah-seo-engine' ) );
		}

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_license_action'] ) && null !== $this->license_client ) {
			check_admin_referer( 'amanah_license_action', 'amanah_license_nonce' );
			$action = sanitize_key( (string) $_POST['amanah_license_action'] );

			try {
				if ( 'activate' === $action ) {
					$license_key = sanitize_text_field( wp_unslash( (string) ( $_POST['license_key'] ?? '' ) ) );
					$this->license_client->activate( $license_key );
					add_settings_error( 'amanah_license', 'license_activated', 'License activated successfully.', 'success' );
				} elseif ( 'validate' === $action ) {
					$state = $this->license_client->validate();
					if ( 'unconfigured' === $state->getStatus() ) {
						add_settings_error( 'amanah_license', 'license_not_configured', 'License is not configured. Please activate a license first.', 'error' );
					} elseif ( $state->isActive() ) {
						add_settings_error( 'amanah_license', 'license_validated', 'License validated successfully.', 'success' );
					} else {
						add_settings_error( 'amanah_license', 'license_validation_failed', 'License validation did not return an active license.', 'error' );
					}
				}
			} catch ( \Throwable $e ) {
				add_settings_error( 'amanah_license', 'license_error', $e->getMessage(), 'error' );
			}
		}

		$state = null !== $this->license_client ? $this->license_client->getState() : null;
		$update_check = null;
		if ( null !== $this->update_service && null !== $this->license_client ) {
			$update_check = $this->update_service->check( $this->license_client->hasEntitlement( 'premium_updates' ) );
		}

		echo '<div class="wrap">';
		echo '<h1>Amanah SEO Engine</h1>';
		echo '<p style="font-size:16px;max-width:850px;">Kelola lisensi, cek pembaruan, dan pastikan Amanah SEO Engine tetap siap bekerja untuk mengembangkan performa SEO website Anda.</p>';
		settings_errors( 'amanah_license' );

		echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;max-width:1100px;margin-top:24px;">';

		echo '<div class="postbox" style="padding:20px;">';
		echo '<h2 style="margin-top:0;">License</h2>';
		if ( null === $state ) {
			echo '<p>License service is not available.</p>';
		} else {
			echo '<p><strong>Status:</strong> ' . esc_html( $state->getStatus() ) . '</p>';
			echo '<p><strong>Plan:</strong> ' . esc_html( $state->getPlan() ) . '</p>';
			echo '<p><strong>Expiration:</strong> ' . esc_html( (string) $state->getExpiration() ) . '</p>';
			echo '<form method="post">';
			wp_nonce_field( 'amanah_license_action', 'amanah_license_nonce' );
			echo '<input type="hidden" name="amanah_license_action" value="activate" />';
			echo '<input type="password" name="license_key" class="regular-text" autocomplete="off" placeholder="Masukkan license key" />';
			echo ' <button class="button button-primary">Activate License</button>';
			echo '</form>';
			echo '<form method="post" style="margin-top:10px">';
			wp_nonce_field( 'amanah_license_action', 'amanah_license_nonce' );
			echo '<input type="hidden" name="amanah_license_action" value="validate" />';
			echo ' <button class="button">Validate License</button>';
			echo '</form>';
		}
		echo '</div>';

		echo '<div class="postbox" style="padding:20px;">';
		echo '<h2 style="margin-top:0;">Updates</h2>';
		if ( null === $update_check ) {
			echo '<p>Update service is not available.</p>';
		} else {
			echo '<p><strong>Status:</strong> ' . esc_html( $update_check['available'] ? ( $update_check['eligible'] ? 'Update available and eligible.' : 'Update available but restricted.' ) : 'No update available.' ) . '</p>';
		}
		echo '</div>';
		echo '</div>';

		echo '<div class="postbox" style="max-width:1100px;margin-top:24px;padding:24px;">';
		echo '<h2 style="margin-top:0;">Tentang Amanah SEO Engine</h2>';
		echo '<h3>SEO bukan lagi sekadar mengejar keyword. SEO adalah membangun sistem pertumbuhan.</h3>';
		echo '<p>Amanah SEO Engine hadir untuk membantu pemilik website mengubah SEO yang selama ini terasa kompleks, terpisah, dan sulit dikendalikan menjadi sebuah <strong>mesin pertumbuhan organik yang terstruktur</strong>.</p>';
		echo '<p>Bukan hanya mencari kata kunci atau membuat konten sebanyak mungkin, tetapi memahami <strong>apa yang dibutuhkan website, mengapa halaman tertentu harus dibuat, bagaimana setiap URL memiliki peran, dan bagaimana setiap langkah dapat diukur hasilnya.</strong></p>';
		echo '<hr>';
		echo '<h3>Dari data website menjadi strategi yang menghasilkan keputusan.</h3>';
		echo '<p>Amanah SEO Engine bekerja dengan alur yang terintegrasi:</p>';
		echo '<p><strong>Data → Insight → Strategi → Konten → Evaluasi → Optimasi</strong></p>';
		echo '<p>Setiap keputusan SEO dibangun berdasarkan kondisi nyata website—mulai dari struktur halaman, peluang keyword, kualitas konten, kesehatan teknis, hingga performa organik.</p>';
		echo '<p>Tujuannya sederhana:</p>';
		echo '<p><strong>Membangun halaman yang tepat, untuk target yang tepat, dengan strategi yang jelas.</strong></p>';
		echo '<hr>';
		echo '<h3>Satu pusat kendali untuk seluruh perjalanan SEO Anda.</h3>';
		echo '<p>Tidak perlu lagi berpindah-pindah tools dan kehilangan arah.</p>';
		echo '<p>Amanah SEO Engine menyatukan seluruh proses SEO dalam satu ekosistem:</p>';
		echo '<p><strong>Live Website • SEO Intelligence • Content Planning • Keyword Architecture • Link Strategy • Technical Issues • Monitoring • AI Assistance</strong></p>';
		echo '<p>Semua terhubung dalam satu alur kerja yang membantu Anda:</p>';
		echo '<ul style="list-style:disc;margin-left:24px;">';
		echo '<li>Memahami kondisi website secara menyeluruh</li>';
		echo '<li>Menemukan peluang pertumbuhan organik</li>';
		echo '<li>Membuat strategi keyword yang terstruktur</li>';
		echo '<li>Menghasilkan konten yang memiliki tujuan</li>';
		echo '<li>Mengukur, mengevaluasi, dan terus meningkatkan performa</li>';
		echo '</ul>';
		echo '<hr>';
		echo '<h3>Bukan sekadar membuat lebih banyak halaman.</h3>';
		echo '<p><strong>Amanah SEO Engine membantu Anda membangun aset digital yang bekerja untuk jangka panjang.</strong></p>';
		echo '<p>Setiap halaman memiliki tujuan.<br>Setiap keyword memiliki strategi.<br>Setiap optimasi memiliki arah.</p>';
		echo '<div class="notice notice-info" style="padding:18px;margin-top:20px;"><p style="font-size:18px;margin:0;"><strong>Karena SEO yang kuat bukan dibangun dari banyaknya konten, tetapi dari sistem yang tepat.</strong></p></div>';

		echo '</div>';
		echo '</div>';
	}

	public function renderDashboard(): void {
		$this->renderer->render( 'dashboard' );
	}
}
