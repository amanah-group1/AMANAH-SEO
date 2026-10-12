<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Core\Bootstrap;

use Amanah\SEOEngine\AI\AIContextAssembler;
use Amanah\SEOEngine\Application\UseCase\CreateKeyword;
use Amanah\SEOEngine\Application\UseCase\CreateOwnerUrl;
use Amanah\SEOEngine\Application\UseCase\ResolveOwnerUrl;
use Amanah\SEOEngine\Application\UseCase\AssignKeywordOwnerUrl;
use Amanah\SEOEngine\Application\UseCase\FindOwnerUrl;
use Amanah\SEOEngine\Application\UseCase\GenerateKeywordStrategy;
use Amanah\SEOEngine\Application\UseCase\GenerateStrategyWorkflow;
use Amanah\SEOEngine\Application\UseCase\GenerateContentPlan;
use Amanah\SEOEngine\Application\UseCase\GenerateContentBrief;
use Amanah\SEOEngine\Application\UseCase\GeneratePageBlueprint;
use Amanah\SEOEngine\Application\UseCase\SaveContentOutline;
use Amanah\SEOEngine\Application\UseCase\ScanPillarArticle;
use Amanah\SEOEngine\Application\UseCase\GenerateContent;
use Amanah\SEOEngine\Application\UseCase\CreateManualContent;
use Amanah\SEOEngine\Application\UseCase\EvaluateContentQuality;
use Amanah\SEOEngine\Application\UseCase\ReviewContent;
use Amanah\SEOEngine\Application\UseCase\PublishContent;
use Amanah\SEOEngine\Domain\Content\ContentValidationService;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressContentRepository;
use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategyService;
use Amanah\SEOEngine\Domain\ContentPlan\ContentPlanService;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBriefService;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprintService;
use Amanah\SEOEngine\Application\UseCase\ListKeywords;
use Amanah\SEOEngine\Application\UseCase\CreateSearchIntent;
use Amanah\SEOEngine\Application\UseCase\ListSearchIntents;
use Amanah\SEOEngine\Application\UseCase\ResolveSearchIntent;
use Amanah\SEOEngine\Application\UseCase\AssignKeywordSearchIntent;
use Amanah\SEOEngine\Application\UseCase\AnalyzeKeyword;
use Amanah\SEOEngine\Application\UseCase\AnalyzeKeywordRelationships;
use Amanah\SEOEngine\Application\UseCase\ResolveCluster;
use Amanah\SEOEngine\Application\UseCase\ListClusters;
use Amanah\SEOEngine\Application\UseCase\AssignKeywordCluster;
use Amanah\SEOEngine\Domain\KeywordAnalysis\KeywordAnalysisService;
use Amanah\SEOEngine\Domain\KeywordRelationship\KeywordRelationshipService;
use Amanah\SEOEngine\Domain\Cluster\ClusterService;
use Amanah\SEOEngine\Domain\SearchIntent\SearchIntentService;
use Amanah\SEOEngine\Domain\OwnerUrl\OwnerUrlService;
use Amanah\SEOEngine\AI\AICostCalculator;
use Amanah\SEOEngine\AI\AIEngine;
use Amanah\SEOEngine\AI\AIExecutionService;
use Amanah\SEOEngine\AI\AIModelDiscoveryService;
use Amanah\SEOEngine\AI\AIRouter;
use Amanah\SEOEngine\AI\AIUsageRecorder;
use Amanah\SEOEngine\AI\Provider\CustomAIProvider;
use Amanah\SEOEngine\AI\Provider\GeminiProvider;
use Amanah\SEOEngine\AI\Provider\OpenAIProvider;
use Amanah\SEOEngine\AI\Provider\ProviderRegistry;
use Amanah\SEOEngine\Application\Job\AICostProtection;
use Amanah\SEOEngine\Application\Job\ConcurrencyPolicy;
use Amanah\SEOEngine\Application\Job\JobHandlerRegistry;
use Amanah\SEOEngine\Application\Job\JobManager;
use Amanah\SEOEngine\Application\Job\PortabilityRestoreJobHandler;
use Amanah\SEOEngine\Application\Job\JobWorker;
use Amanah\SEOEngine\Application\Job\RetryPolicy;
use Amanah\SEOEngine\Application\Portability\PortabilityService;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version7PortabilityRecovery;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version8ContentPlan;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version9SEOScan;
use Amanah\SEOEngine\Application\Service\DashboardSummaryService;
use Amanah\SEOEngine\Application\Service\PageBlueprintBuilder;
use Amanah\SEOEngine\Application\Service\SEOScanService;
use Amanah\SEOEngine\Application\Service\SEOIntelligenceService;
use Amanah\SEOEngine\Application\Service\IssueRecommendationService;
use Amanah\SEOEngine\Application\Repository\IssueRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\RecommendationRepositoryInterface;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressIssueRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressRecommendationRepository;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version11IssueRecommendation;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version12ContentRevision;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version13QualityContentVersion;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version14KeywordArticleStructure;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version15DefaultSearchIntents;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version16ContentScanning;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version17RemoveLegacySerpCredentials;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version18ContentIntelligenceFoundation;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressIntelligenceRunRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressIntelligenceFindingRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressContentRequirementRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressRequirementSnapshotRepository;
use Amanah\SEOEngine\Application\Service\ContentIntelligenceService;
use Amanah\SEOEngine\Infrastructure\Serp\GoogleSearchSerpProvider;
use Amanah\SEOEngine\Infrastructure\ContentScanning\ExternalPageFetcher;
use Amanah\SEOEngine\Infrastructure\ContentScanning\HtmlContentExtractor;
use Amanah\SEOEngine\Infrastructure\ContentScanning\ContentScanningAnalyzer;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressContentScanRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressSerpResultRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressScanAnalysisRepository;
use Amanah\SEOEngine\Application\UseCase\RunContentScanning;
use Amanah\SEOEngine\Application\UseCase\SaveScanningReview;
use Amanah\SEOEngine\Application\UseCase\GenerateOutlineFromScanning;
use Amanah\SEOEngine\Application\KeywordIntelligence\KeywordIntelligenceEngine;
use Amanah\SEOEngine\Application\UseCase\GeneratePotentialSupportKeywords;
use Amanah\SEOEngine\Infrastructure\KeywordIntelligence\GoogleAutocompleteCandidateSource;
use Amanah\SEOEngine\Application\UseCase\CreateSupportKeywordsFromCandidates;
use Amanah\SEOEngine\Application\UseCase\SyncWordPressContent;
use Amanah\SEOEngine\Application\UseCase\UpdateContent;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressContentRevisionRepository;
use Amanah\SEOEngine\Infrastructure\WordPress\Content\WordPressContentReader;
use Amanah\SEOEngine\Application\Repository\SEOScanRepositoryInterface;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressSEOScanRepository;
use Amanah\SEOEngine\Infrastructure\Portability\WordPressPortabilityBackupRepository;
use Amanah\SEOEngine\Application\Port\MigrationRunnerInterface;
use Amanah\SEOEngine\Infrastructure\WordPress\WordPressWebsiteInventory;
use Amanah\SEOEngine\Domain\Quality\DeterministicQualityEngine;
use Amanah\SEOEngine\Domain\Quality\PrePublishValidator;
use Amanah\SEOEngine\Infrastructure\AI\SecretStore;
use Amanah\SEOEngine\Infrastructure\BackgroundJob\DiagnosticsJobObserver;
use Amanah\SEOEngine\Infrastructure\BackgroundJob\JobCleanup;
use Amanah\SEOEngine\Infrastructure\BackgroundJob\WordPressJobRepository;
use Amanah\SEOEngine\Infrastructure\BackgroundJob\WordPressQueueAdapter;
use Amanah\SEOEngine\Infrastructure\Cache\WordPressCache;
use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;
use Amanah\SEOEngine\Infrastructure\Configuration\WordPressOptionStore;
use Amanah\SEOEngine\Infrastructure\Database\WordPressDatabase;
use Amanah\SEOEngine\Infrastructure\Diagnostics\Diagnostics;
use Amanah\SEOEngine\Infrastructure\Http\WordPressHttpClient;
use Amanah\SEOEngine\Infrastructure\PillarArticle\PillarArticleScanner;
use Amanah\SEOEngine\Infrastructure\Migration\MigrationManager;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version1CoreFoundation;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version2CoreDomain;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version3AiEngine;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version4BackgroundJobs;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version5Portability;
use Amanah\SEOEngine\Infrastructure\Migration\Migrations\Version6AiUsageCost;
use Amanah\SEOEngine\Infrastructure\Portability\WordPressPortabilityRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressAIExecutionTaskRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressAIModelCatalog;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressAIProviderIdentityResolver;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressKeywordRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressClusterRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressOwnerUrlRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressSearchIntentRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressContentStrategyRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressContentPlanRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressContentBriefRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressPageBlueprintRepository;
use Amanah\SEOEngine\Infrastructure\Repository\WordPressQualityResultRepository;
use Amanah\SEOEngine\Infrastructure\WordPress\Content\WordPressContentPublisher;
use Amanah\SEOEngine\Infrastructure\WordPress\Cron\WpCronAdapter;
use Amanah\SEOEngine\Infrastructure\WordPress\EnvironmentGuard;
use Amanah\SEOEngine\Infrastructure\WordPress\Lifecycle\WordPressLifecycle;
use Amanah\SEOEngine\Infrastructure\WordPress\PluginHooks;
use Amanah\SEOEngine\Infrastructure\WordPress\Security\WordPressSecurity;
use Amanah\SEOEngine\Infrastructure\WordPress\Settings\SettingsRegistrar;
use Amanah\SEOEngine\Platform\License\LicenseCache;
use Amanah\SEOEngine\Platform\License\LicenseClient;
use Amanah\SEOEngine\Platform\License\LicenseResponseValidator;
use Amanah\SEOEngine\Platform\License\LicenseSecretStore;
use Amanah\SEOEngine\Platform\License\EntitlementResolver;
use Amanah\SEOEngine\Platform\Update\UpdateClient;
use Amanah\SEOEngine\Platform\Update\UpdateEligibility;
use Amanah\SEOEngine\Platform\Update\UpdateExecutor;
use Amanah\SEOEngine\Platform\Update\UpdatePackageVerifier;
use Amanah\SEOEngine\Platform\Update\UpdatePreflight;
use Amanah\SEOEngine\Platform\Update\UpdateSafety;
use Amanah\SEOEngine\Platform\Update\UpdateServerClient;
use Amanah\SEOEngine\Platform\Update\UpdateService;
use Amanah\SEOEngine\Core\ServiceRegistry\ServiceRegistry;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	private static $services;

	public static function boot(): ServiceRegistry {
		if ( null !== self::$services ) {
			return self::$services;
		}

		self::$services = new ServiceRegistry();
		self::$services->register( 'plugin', new self() );

		if ( isset( $GLOBALS['wpdb'] ) && $GLOBALS['wpdb'] instanceof \wpdb ) {
			self::registerInfrastructure( self::$services, $GLOBALS['wpdb'] );
		}

		return self::$services;
	}

	public static function activate(): void {
		$services = self::boot();
		$services->get( 'environment_guard' )->assertCompatible();
		$services->get( 'configuration' )->initializeDefaults();
		$services->get( 'migration_manager' )->migrate();
	}

	public static function deactivate(): void {
		self::boot()->get( 'lifecycle' )->deactivate();
	}

	public static function uninstall(): void {
		self::boot()->get( 'lifecycle' )->uninstall();
	}

	private static function registerInfrastructure( ServiceRegistry $services, \wpdb $wpdb ): void {
		$configuration = new Configuration( new WordPressOptionStore() );
		$database = new WordPressDatabase( $wpdb );
		$cache = new WordPressCache();
		$http = new WordPressHttpClient();
		$diagnostics = new Diagnostics( $wpdb );
		$migrations = new MigrationManager( $configuration );
		$settings = new SettingsRegistrar( $configuration );
		$jobRepository = new WordPressJobRepository( $wpdb );
		$jobHandlers = new JobHandlerRegistry();
		$scheduler = new WpCronAdapter();
		$jobObserver = new DiagnosticsJobObserver( $diagnostics );
		$jobWorker = new JobWorker( $jobRepository, $jobHandlers, new RetryPolicy(), '', new ConcurrencyPolicy( 2 ), new AICostProtection(), $jobObserver );
		$jobQueue = new WordPressQueueAdapter( $scheduler );
		$jobManager = new JobManager( $jobRepository, $jobQueue );
		$jobCleanup = new JobCleanup( $wpdb );

		$migrations->register( new Version1CoreFoundation( $wpdb ) );
		$migrations->register( new Version2CoreDomain( $wpdb ) );
		$migrations->register( new Version3AiEngine( $wpdb ) );
		$migrations->register( new Version4BackgroundJobs( $wpdb ) );
		$migrations->register( new Version5Portability() );
		$migrations->register( new Version6AiUsageCost( $wpdb ) );
		$migrations->register( new Version7PortabilityRecovery() );
		$migrations->register( new Version8ContentPlan() );
		$migrations->register( new Version9SEOScan() );
		$migrations->register( new Version11IssueRecommendation() );
		$migrations->register( new Version12ContentRevision() );
		$migrations->register( new Version13QualityContentVersion() );
		$migrations->register( new Version14KeywordArticleStructure() );
		$migrations->register( new Version15DefaultSearchIntents( $wpdb ) );
		$migrations->register( new Version16ContentScanning( $wpdb ) );
		$migrations->register( new Version17RemoveLegacySerpCredentials() );
		$migrations->register( new Version18ContentIntelligenceFoundation() );


		$registry = new ProviderRegistry();
		$registry->register( new OpenAIProvider( $http ) );
		$registry->register( new GeminiProvider( $http ) );
		$registry->register( new CustomAIProvider( $http ) );
		$router = new AIRouter( $registry );
		$usageRecorder = new AIUsageRecorder( $database, new AICostCalculator() );
		$providerIdentityResolver = new WordPressAIProviderIdentityResolver( $wpdb );
		$aiTaskRepository = new WordPressAIExecutionTaskRepository( $wpdb );
		$modelCatalog = new WordPressAIModelCatalog( $wpdb );

		$licenseEndpoint = (string) $configuration->get( 'license_server_endpoint', '' );
		if ( '' === $licenseEndpoint ) {
			$licenseEndpoint = (string) get_option( 'amanah_seo_license_api_endpoint', 'https://api.amanahseoai.my.id' );
		}

		$licenseCache = new LicenseCache();
		$licenseClient = new LicenseClient(
			$http,
			$licenseCache,
			new LicenseSecretStore(),
			new LicenseResponseValidator(),
			'Amanah SEO Engine V2',
			$licenseEndpoint
		);
		$updateClient = new UpdateClient( null, new UpdateEligibility(), new UpdatePreflight(), new UpdatePackageVerifier() );
		$updateServer = new UpdateServerClient(
			$http,
			'Amanah SEO Engine V2',
			(string) $configuration->get( 'update_server_endpoint', '' )
		);
		$updateService = new UpdateService(
			$updateClient,
			$updateServer,
			new UpdateExecutor(),
			new UpdateSafety(),
			$migrations,
			'0.1.0'
		);
		$keyword_repository = new WordPressKeywordRepository( $wpdb );
		$list_keywords = new ListKeywords( $keyword_repository );
		$search_intent_repository = new WordPressSearchIntentRepository( $wpdb );
		$create_search_intent = new CreateSearchIntent( $search_intent_repository, new SearchIntentService() );
		$list_search_intents = new ListSearchIntents( $search_intent_repository );
		$resolve_search_intent = new ResolveSearchIntent( $search_intent_repository, new SearchIntentService() );
		$assign_keyword_search_intent = new AssignKeywordSearchIntent( $keyword_repository, $search_intent_repository );
		$analyze_keyword = new AnalyzeKeyword( $keyword_repository, new KeywordAnalysisService() );
		$analyze_keyword_relationships = new AnalyzeKeywordRelationships( $keyword_repository, new KeywordAnalysisService(), new KeywordRelationshipService() );
		$cluster_repository = new WordPressClusterRepository( $wpdb );
		$resolve_cluster = new ResolveCluster( $cluster_repository );
		$list_clusters = new ListClusters( $cluster_repository );
		$assign_keyword_cluster = new AssignKeywordCluster( $keyword_repository, $cluster_repository, new ClusterService() );
		$owner_url_repository = new WordPressOwnerUrlRepository( $wpdb );
		$create_owner_url = new CreateOwnerUrl( $owner_url_repository );
		$resolve_owner_url = new ResolveOwnerUrl( $owner_url_repository );
		$create_keyword = new CreateKeyword( $keyword_repository, $resolve_owner_url );
		$keyword_intelligence = new KeywordIntelligenceEngine(
			array(
				new GoogleAutocompleteCandidateSource( $http ),
			)
		);
		$generate_potential_support_keywords = new GeneratePotentialSupportKeywords( $keyword_repository, $keyword_intelligence );
		$create_support_keywords = new CreateSupportKeywordsFromCandidates( $create_keyword );
		$scan_pillar_article = new ScanPillarArticle( $keyword_repository, $owner_url_repository, new PillarArticleScanner( $http ) );
		$assign_keyword_owner_url = new AssignKeywordOwnerUrl( $keyword_repository, $owner_url_repository, new OwnerUrlService() );
		$find_owner_url = new FindOwnerUrl( $owner_url_repository );
		$strategy_repository = new WordPressContentStrategyRepository( $wpdb );
		$generate_keyword_strategy = new GenerateKeywordStrategy(
			$keyword_repository,
			$search_intent_repository,
			$cluster_repository,
			$owner_url_repository,
			$strategy_repository,
			$analyze_keyword_relationships,
			new ContentStrategyService()
		);
		$plan_repository = new WordPressContentPlanRepository( $wpdb );
		$generate_content_plan = new GenerateContentPlan( $strategy_repository, $plan_repository, new ContentPlanService() );
		$generate_strategy_workflow = new GenerateStrategyWorkflow( $generate_keyword_strategy, $generate_content_plan );
		$brief_repository = new WordPressContentBriefRepository( $wpdb );
		$generate_content_brief = new GenerateContentBrief( $plan_repository, $strategy_repository, $brief_repository, new ContentBriefService() );

		$services->register( 'keyword_repository', $keyword_repository );
		$services->register( 'create_keyword', $create_keyword );
		$services->register( 'keyword_intelligence', $keyword_intelligence );
		$services->register( 'generate_potential_support_keywords', $generate_potential_support_keywords );
		$services->register( 'create_support_keywords', $create_support_keywords );
		$services->register( 'scan_pillar_article', $scan_pillar_article );
		$services->register( 'list_keywords', $list_keywords );
		$services->register( 'search_intent_repository', $search_intent_repository );
		$services->register( 'create_search_intent', $create_search_intent );
		$services->register( 'list_search_intents', $list_search_intents );
		$services->register( 'resolve_search_intent', $resolve_search_intent );
		$services->register( 'assign_keyword_search_intent', $assign_keyword_search_intent );
		$services->register( 'analyze_keyword', $analyze_keyword );
		$services->register( 'analyze_keyword_relationships', $analyze_keyword_relationships );
		$services->register( 'cluster_repository', $cluster_repository );
		$services->register( 'resolve_cluster', $resolve_cluster );
		$services->register( 'assign_keyword_cluster', $assign_keyword_cluster );
		$services->register( 'owner_url_repository', $owner_url_repository );
		$services->register( 'create_owner_url', $create_owner_url );
		$services->register( 'resolve_owner_url', $resolve_owner_url );
		$services->register( 'assign_keyword_owner_url', $assign_keyword_owner_url );
		$services->register( 'find_owner_url', $find_owner_url );
		$services->register( 'content_strategy_repository', $strategy_repository );
		$services->register( 'generate_keyword_strategy', $generate_keyword_strategy );
		$services->register( 'generate_strategy_workflow', $generate_strategy_workflow );
		$services->register( 'content_plan_repository', $plan_repository );
		$services->register( 'generate_content_plan', $generate_content_plan );
		$services->register( 'content_brief_repository', $brief_repository );
		$services->register( 'generate_content_brief', $generate_content_brief );
		$blueprint_repository = new WordPressPageBlueprintRepository( $wpdb );
		$intelligence_run_repository = new WordPressIntelligenceRunRepository( $wpdb );
		$intelligence_finding_repository = new WordPressIntelligenceFindingRepository( $wpdb );
		$content_requirement_repository = new WordPressContentRequirementRepository( $wpdb );
		$requirement_snapshot_repository = new WordPressRequirementSnapshotRepository( $wpdb );
		$page_blueprint_builder = new PageBlueprintBuilder( $content_requirement_repository, $requirement_snapshot_repository );
		$generate_page_blueprint = new GeneratePageBlueprint( $brief_repository, $blueprint_repository, new PageBlueprintService(), $page_blueprint_builder );
		$content_intelligence = new ContentIntelligenceService( $intelligence_run_repository, $intelligence_finding_repository, $content_requirement_repository, $requirement_snapshot_repository );
		$save_content_outline = new SaveContentOutline( $brief_repository, $blueprint_repository, $page_blueprint_builder );
		$services->register( 'page_blueprint_repository', $blueprint_repository );
		$services->register( 'page_blueprint_builder', $page_blueprint_builder );
		$services->register( 'generate_page_blueprint', $generate_page_blueprint );
		$services->register( 'save_content_outline', $save_content_outline );
		$services->register( 'intelligence_run_repository', $intelligence_run_repository );
		$services->register( 'intelligence_finding_repository', $intelligence_finding_repository );
		$services->register( 'content_requirement_repository', $content_requirement_repository );
		$services->register( 'requirement_snapshot_repository', $requirement_snapshot_repository );
		$services->register( 'content_intelligence', $content_intelligence );

		$services->register( 'configuration', $configuration );
		$services->register( 'database', $database );
		$services->register( 'cache', $cache );
		$services->register( 'http', $http );
		$services->register( 'diagnostics', $diagnostics );
		$services->register( 'migration_manager', $migrations );
		$services->register( 'environment_guard', new EnvironmentGuard() );
		$services->register( 'security', new WordPressSecurity() );
		$services->register( 'content_publisher', new WordPressContentPublisher() );
		$services->register( 'scheduler', $scheduler );
		$services->register( 'job_cleanup', $jobCleanup );
		$services->register( 'job_queue', new WordPressQueueAdapter( $scheduler ) );
		$services->register( 'lifecycle', new WordPressLifecycle( $configuration ) );
		$services->register( 'job_repository', $jobRepository );
		$services->register( 'job_handlers', $jobHandlers );
		$services->register( 'job_worker', $jobWorker );
		$services->register( 'job_manager', $jobManager );
		$portabilityRepository = new WordPressPortabilityRepository( $wpdb );
		$backupRepository = new WordPressPortabilityBackupRepository( $wpdb );
		$portabilityService = new PortabilityService( $portabilityRepository, null, null, $backupRepository, null, $jobManager );
		$jobHandlers->register( new PortabilityRestoreJobHandler( $portabilityService ) );
		$services->register( 'portability_repository', $portabilityRepository );
		$services->register( 'portability_backup_repository', $backupRepository );
		$services->register( 'portability_service', $portabilityService );
		$services->register( 'ai_provider_registry', $registry );
		$services->register( 'ai_context_assembler', new AIContextAssembler() );
		$services->register( 'ai_router', $router );
		$services->register( 'ai_engine', new AIEngine( $router, new AIContextAssembler(), null, $usageRecorder, $providerIdentityResolver ) );
		$services->register( 'ai_execution_task_repository', $aiTaskRepository );
		$services->register( 'ai_execution_service', new AIExecutionService( $services->get( 'ai_engine' ), 2, $aiTaskRepository, $providerIdentityResolver ) );
		$content_scan_repository = new WordPressContentScanRepository( $wpdb );
		$serp_result_repository = new WordPressSerpResultRepository( $wpdb );
		$scan_analysis_repository = new WordPressScanAnalysisRepository( $wpdb );
		$serp_provider = new GoogleSearchSerpProvider( $http );
		$run_content_scanning = new RunContentScanning( $keyword_repository, $strategy_repository, $content_scan_repository, $serp_result_repository, $scan_analysis_repository, $keyword_intelligence, $serp_provider, new ExternalPageFetcher( $http ), new HtmlContentExtractor(), new ContentScanningAnalyzer(), $services->get( 'ai_execution_service' ), new SecretStore(), $content_intelligence );
		$save_scanning_review = new SaveScanningReview( $content_scan_repository );
		$generate_outline_from_scanning = new GenerateOutlineFromScanning( $content_scan_repository, $strategy_repository, $brief_repository, $services->get( 'ai_execution_service' ), new SecretStore() );
		$services->register( 'content_scan_repository', $content_scan_repository );
		$services->register( 'serp_result_repository', $serp_result_repository );
		$services->register( 'scan_analysis_repository', $scan_analysis_repository );
		$services->register( 'run_content_scanning', $run_content_scanning );
		$services->register( 'save_scanning_review', $save_scanning_review );
		$services->register( 'generate_outline_from_scanning', $generate_outline_from_scanning );
		$content_repository = new WordPressContentRepository( $wpdb );
		$content_revision_repository = new WordPressContentRevisionRepository( $wpdb );
		$content_reader = new WordPressContentReader();
		$generate_content = new GenerateContent( $blueprint_repository, $content_repository, new ContentValidationService(), $services->get( 'ai_execution_service' ), new SecretStore(), $configuration, $services->get( 'content_publisher' ) );
		$create_manual_content = new CreateManualContent( $blueprint_repository, $content_repository, new ContentValidationService(), $services->get( 'content_publisher' ) );
		$services->register( 'content_repository', $content_repository );
		$services->register( 'content_revision_repository', $content_revision_repository );
		$services->register( 'content_reader', $content_reader );
		$sync_wordpress_content = new SyncWordPressContent( $content_repository, $content_revision_repository, $content_reader );
		$update_content = new UpdateContent( $content_repository, $content_revision_repository );
		$services->register( 'sync_wordpress_content', $sync_wordpress_content );
		$services->register( 'update_content', $update_content );
		$services->register( 'generate_content', $generate_content );
		$services->register( 'quality_engine', new DeterministicQualityEngine() );
		$services->register( 'pre_publish_validator', new PrePublishValidator() );
		$services->register( 'quality_result_repository', new WordPressQualityResultRepository( $wpdb ) );
		$evaluate_content_quality = new EvaluateContentQuality( $content_repository, $keyword_repository, $blueprint_repository, $services->get( 'quality_result_repository' ), $services->get( 'quality_engine' ), $sync_wordpress_content );
		$review_content = new ReviewContent( $content_repository );
		$publish_content = new PublishContent( $content_repository, $services->get( 'quality_result_repository' ), $services->get( 'content_publisher' ), $services->get( 'pre_publish_validator' ), $sync_wordpress_content );
		$services->register( 'evaluate_content_quality', $evaluate_content_quality );
		$services->register( 'review_content', $review_content );
		$services->register( 'publish_content', $publish_content );
		$hooks = new PluginHooks( $configuration, $diagnostics, $settings, $jobWorker, $scheduler, $licenseClient, $updateService, $create_keyword, $list_keywords, $generate_potential_support_keywords, $create_support_keywords, $scan_pillar_article, $create_search_intent, $list_search_intents, $resolve_cluster, $list_clusters, $find_owner_url, $generate_strategy_workflow, $strategy_repository, $generate_content_plan, $plan_repository, $generate_content_brief, $brief_repository, $generate_page_blueprint, $blueprint_repository, $generate_content, $content_repository, $create_manual_content, $evaluate_content_quality, $review_content, $publish_content, $sync_wordpress_content, $update_content );
		$hooks->setCleanup( $jobCleanup );
		$services->register( 'ai_model_discovery', new AIModelDiscoveryService( $registry, $modelCatalog ) );
		$services->register( 'ai_model_catalog', $modelCatalog );
		$services->register( 'ai_secret_store', new SecretStore() );
		$services->register( 'ai_usage_recorder', $usageRecorder );
		$services->register( 'ai_provider_identity_resolver', $providerIdentityResolver );
		$quality_result_repository = $services->get( 'quality_result_repository' );
		$dashboard_summary_service = new DashboardSummaryService(
			$keyword_repository,
			$owner_url_repository,
			$strategy_repository,
			$plan_repository,
			$brief_repository,
			$blueprint_repository,
			$content_repository,
			$quality_result_repository
		);
		$seo_scan_repository = new WordPressSEOScanRepository( $wpdb );
		$website_inventory = new WordPressWebsiteInventory( $http );
		$seo_scan_service = new SEOScanService( $dashboard_summary_service, $seo_scan_repository, $website_inventory );
		$seo_intelligence_service = new SEOIntelligenceService( $keyword_repository, $owner_url_repository, $seo_scan_repository );
		$services->register( 'seo_scan_repository', $seo_scan_repository );
		$services->register( 'dashboard_summary_service', $dashboard_summary_service );
		$services->register( 'seo_scan_service', $seo_scan_service );
		$services->register( 'seo_intelligence_service', $seo_intelligence_service );
		$issue_repository = new WordPressIssueRepository( $wpdb );
		$recommendation_repository = new WordPressRecommendationRepository( $wpdb );
		$issue_recommendation_service = new IssueRecommendationService( $issue_repository, $recommendation_repository, $seo_intelligence_service );
		$services->register( 'issue_repository', $issue_repository );
		$services->register( 'recommendation_repository', $recommendation_repository );
		$services->register( 'issue_recommendation_service', $issue_recommendation_service );
		$services->register( 'license_cache', $licenseCache );
		$services->register( 'license_client', $licenseClient );
		$services->register( 'entitlement_resolver', new EntitlementResolver() );
		$services->register( 'update_client', $updateClient );
		$services->register( 'update_server', $updateServer );
		$services->register( 'update_service', $updateService );
		$services->register( 'hooks', $hooks );

		$hooks->register();
	}
}
