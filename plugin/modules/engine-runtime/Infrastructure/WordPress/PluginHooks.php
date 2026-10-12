<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\WordPress;
use Amanah\SEOEngine\Core\Bootstrap\Plugin;
use Amanah\SEOEngine\Application\Job\JobWorker;
use Amanah\SEOEngine\Application\UseCase\CreateKeyword;
use Amanah\SEOEngine\Application\UseCase\ListKeywords;
use Amanah\SEOEngine\Application\UseCase\FindOwnerUrl;
use Amanah\SEOEngine\Application\UseCase\GenerateStrategyWorkflow;
use Amanah\SEOEngine\Application\UseCase\GenerateContentPlan;
use Amanah\SEOEngine\Application\UseCase\GenerateContentBrief;
use Amanah\SEOEngine\Application\UseCase\GeneratePageBlueprint;
use Amanah\SEOEngine\Application\UseCase\GenerateContent;
use Amanah\SEOEngine\Application\UseCase\CreateManualContent;
use Amanah\SEOEngine\Application\UseCase\EvaluateContentQuality;
use Amanah\SEOEngine\Application\UseCase\ReviewContent;
use Amanah\SEOEngine\Application\UseCase\PublishContent;
use Amanah\SEOEngine\Application\UseCase\SyncWordPressContent;
use Amanah\SEOEngine\Application\UseCase\UpdateContent;
use Amanah\SEOEngine\Application\UseCase\GeneratePotentialSupportKeywords;
use Amanah\SEOEngine\Application\UseCase\CreateSupportKeywordsFromCandidates;
use Amanah\SEOEngine\Application\UseCase\ScanPillarArticle;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentPlanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\UseCase\CreateSearchIntent;
use Amanah\SEOEngine\Application\UseCase\ListSearchIntents;
use Amanah\SEOEngine\Application\UseCase\ResolveCluster;
use Amanah\SEOEngine\Application\UseCase\ListClusters;
use Amanah\SEOEngine\Application\Port\SchedulerInterface;
use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;
use Amanah\SEOEngine\Infrastructure\BackgroundJob\JobCleanup;
use Amanah\SEOEngine\Infrastructure\Diagnostics\Diagnostics;
use Amanah\SEOEngine\Infrastructure\WordPress\Settings\SettingsRegistrar;
use Amanah\SEOEngine\Presentation\Admin\AdminMenu;
use Amanah\SEOEngine\Platform\License\LicenseClient;
use Amanah\SEOEngine\Platform\Update\UpdateService;
defined( 'ABSPATH' ) || exit;
final class PluginHooks {
 private $configuration;private $diagnostics;private $settings;private $admin_menu;private $job_worker;private $scheduler;private $cleanup;
 public function __construct(Configuration $configuration,Diagnostics $diagnostics,SettingsRegistrar $settings,?JobWorker $job_worker=null,?SchedulerInterface $scheduler=null,?LicenseClient $license_client=null,?UpdateService $update_service=null,?CreateKeyword $create_keyword=null,?ListKeywords $list_keywords=null,?GeneratePotentialSupportKeywords $generate_potential_support_keywords=null,?CreateSupportKeywordsFromCandidates $create_support_keywords=null,?ScanPillarArticle $scan_pillar_article=null,?CreateSearchIntent $create_search_intent=null,?ListSearchIntents $list_search_intents=null,?ResolveCluster $resolve_cluster=null,?ListClusters $list_clusters=null,?FindOwnerUrl $find_owner_url=null,?GenerateStrategyWorkflow $generate_strategy=null,?ContentStrategyRepositoryInterface $strategy_repository=null,?GenerateContentPlan $generate_content_plan=null,?ContentPlanRepositoryInterface $plan_repository=null,?GenerateContentBrief $generate_brief=null,?ContentBriefRepositoryInterface $brief_repository=null,?GeneratePageBlueprint $generate_page_blueprint=null,?PageBlueprintRepositoryInterface $page_blueprint_repository=null,?GenerateContent $generate_content=null,?ContentRepositoryInterface $content_repository=null,?CreateManualContent $create_manual_content=null,?EvaluateContentQuality $evaluate_content_quality=null,?ReviewContent $review_content=null,?PublishContent $publish_content=null,?SyncWordPressContent $sync_wordpress_content=null,?UpdateContent $update_content=null){$this->configuration=$configuration;$this->diagnostics=$diagnostics;$this->settings=$settings;$this->admin_menu=new AdminMenu($configuration,$license_client,$update_service,$create_keyword,$list_keywords,$generate_potential_support_keywords,$create_support_keywords,$scan_pillar_article,$create_search_intent,$list_search_intents,$resolve_cluster,$list_clusters,$find_owner_url,$generate_strategy,$strategy_repository,$generate_content_plan,$plan_repository,$generate_brief,$brief_repository,$generate_page_blueprint,$page_blueprint_repository,$generate_content,$content_repository,$create_manual_content,$evaluate_content_quality,$review_content,$publish_content,$sync_wordpress_content,$update_content);$this->job_worker=$job_worker;$this->scheduler=$scheduler;$this->cleanup=null;}
 public function register():void{
  add_action('init',array($this,'runMigrations'),0);add_action('admin_init',array($this,'handleContentEditorSave'),5);add_action('admin_init',array($this,'handleContentProductionPost'),6);add_action('admin_init',array($this->settings,'register'));add_action('admin_footer',array($this,'handleContentEditorReturn'));add_action('admin_init',array($this->admin_menu,'handleIssueRecommendationAction'),20);add_action('admin_menu',array($this->admin_menu,'register'));add_action('init',array($this,'initializeConfiguration'),1);
  if(null!==$this->job_worker){add_action('amanah_seo_engine_run_jobs',array($this,'runJobs'));add_action('amanah_seo_engine_cleanup_jobs',array($this,'cleanupJobs'));if(null!==$this->scheduler){$this->scheduler->scheduleRecurring('amanah_seo_engine_run_jobs',time()+60,60,array());$this->scheduler->scheduleRecurring('amanah_seo_engine_cleanup_jobs',time()+3600,86400,array());}}
 }
 public function runMigrations():void{Plugin::boot()->get('migration_manager')->migrate();}
 public function handleContentProductionPost():void{
  $page=sanitize_key((string)($_GET['page']??''));
  if('amanah-seo-engine-content-production'!==$page)return;
  if('POST'!==$_SERVER['REQUEST_METHOD']||!isset($_POST['amanah_page_blueprint_action']))return;
  $this->admin_menu->handleContentProductionPost();
 }
 public function handleContentEditorSave():void{
  $page=sanitize_key((string)($_GET['page']??''));
  if(!in_array($page,array('amanah-seo-engine-content-production','amanah-seo-engine-content-editor'),true))return;
  if(!isset($_POST['amanah_content_editor_action']))return;
  $this->admin_menu->handleContentEditorSave();
 }
 public function initializeConfiguration():void{$this->configuration->initializeDefaults();}
 public function runJobs():void{if(null===$this->job_worker||!$this->configuration->get('background_processing',true))return;for($i=0;$i<5;$i++){if(!$this->job_worker->runOnce())break;}}

 public function handleContentEditorReturn():void{
  if(!is_admin()||'1'!==sanitize_text_field((string)($_GET['amanah_editor']??''))||!isset($_GET['post'])||'post.php'!==basename((string)($_SERVER['SCRIPT_NAME']??'')))return;
  $post_id=absint($_GET['post']);
  if($post_id<1||'post'!==get_post_type($post_id)||'draft'!==get_post_status($post_id)||!current_user_can('edit_post',$post_id))return;
  ?>
  <script>
  (function(){
   if(!window.wp||!window.wp.data)return;
   var closed=false,wasDirty=false,wasSaving=false,saveObserved=false;
   function finish(){
    if(closed)return;
    closed=true;
    window.setTimeout(function(){window.close();},250);
   }
   function checkEditorState(){
    if(closed)return;
    var editor=window.wp.data.select('core/editor');
    if(!editor)return;
    var dirty=typeof editor.isCurrentPostDirty==='function'&&editor.isCurrentPostDirty();
    var saving=typeof editor.isSavingPost==='function'&&editor.isSavingPost();
    var autosaving=typeof editor.isAutosavingPost==='function'&&editor.isAutosavingPost();
    if(dirty){
     wasDirty=true;
    }
    if(saving){
     wasSaving=true;
     saveObserved=true;
    }
    if(wasDirty&&wasSaving&&!saving&&!autosaving&&!dirty&&saveObserved){
     finish();
    }
   }
   window.wp.data.subscribe(checkEditorState);
   window.setTimeout(checkEditorState,1000);
  })();
  </script>  <?php
 }
 public function setCleanup(JobCleanup $cleanup):void{$this->cleanup=$cleanup;}
 public function cleanupJobs():void{if(null!==$this->cleanup)$this->cleanup->cleanup();}
}