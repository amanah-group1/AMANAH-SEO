<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_Plugin_Core {
	private $loader;private $ai_client;private $settings_controller;private $entity_controller;private $content_controller;private $content_generation_controller;
	public function __construct(){ $this->loader=new AMANAH_SEO_AI_Loader();$this->load_ai_engine();$this->ai_client=new AMANAH_SEO_AI_AI_Client();$this->load_entity_module();$this->load_authority_module();$this->load_content_module();$this->load_article_generation_module();$this->load_core_007();$this->load_settings_surface();}
	private function load_ai_engine(){foreach(array('modules/ai/class-ai-exception.php','modules/ai/class-ai-settings.php','modules/ai/class-ai-credentials.php','modules/ai/class-ai-provider-interface.php','modules/ai/class-ai-request.php','modules/ai/class-ai-response.php','modules/ai/class-ai-provider-resolver.php','modules/ai/providers/class-gemini-provider.php','modules/ai/providers/class-openai-provider.php','modules/ai/class-ai-prompt-manager.php','modules/ai/class-ai-safety-validator.php','modules/ai/class-ai-response-handler.php','modules/ai/class-ai-retry-manager.php','modules/ai/class-ai-token-manager.php','modules/ai/class-ai-cost-manager.php','modules/ai/class-ai-logger.php','modules/ai/class-ai-client.php','modules/ai/class-ai-model-catalog.php') as $file)require_once AMANAH_SEO_AI_PATH.$file;}
	private function load_entity_module(){foreach(array('modules/entity/class-entity-model.php','modules/entity/class-entity-manager.php','modules/entity/class-entity-controller.php','modules/entity/class-entity-rest-controller.php','modules/entity/class-entity-admin.php') as $file)require_once AMANAH_SEO_AI_PATH.$file;$model=new AMANAH_SEO_AI_Entity_Model();$manager=new AMANAH_SEO_AI_Entity_Manager($model,$this->ai_client);$this->entity_controller=new AMANAH_SEO_AI_Entity_Controller($model,$manager);(new AMANAH_SEO_AI_Entity_Admin($this->entity_controller,$model))->register();}
	private function load_authority_module(){foreach(array('modules/authority/class-authority-model.php','modules/authority/class-authority-controller.php','modules/authority/class-authority-admin.php') as $file)require_once AMANAH_SEO_AI_PATH.$file;$authority_model=new AMANAH_SEO_AI_Authority_Model();$entity_model=new AMANAH_SEO_AI_Entity_Model();$authority_controller=new AMANAH_SEO_AI_Authority_Controller($authority_model,$entity_model);(new AMANAH_SEO_AI_Authority_Admin($authority_controller,$authority_model,$entity_model))->register();}
	private function load_content_module(){foreach(array('modules/content/class-content-analysis-input.php','modules/content/class-content-analysis.php','modules/content/class-content-analysis-result.php','modules/content/class-content-analysis-repository-interface.php','modules/content/class-wordpress-content-analysis-repository.php','modules/content/class-generation-requirements.php','modules/content/class-generation-requirements-builder.php','modules/content/class-content-analyzer.php','modules/content/class-content-controller.php') as $file)require_once AMANAH_SEO_AI_PATH.$file;$repository=new AMANAH_SEO_AI_WordPress_Content_Analysis_Repository();$builder=new AMANAH_SEO_AI_Generation_Requirements_Builder();$analyzer=new AMANAH_SEO_AI_Content_Analyzer($this->ai_client,$repository,$builder);$this->content_controller=new AMANAH_SEO_AI_Content_Controller($analyzer,$repository);$this->content_controller->register();}
	private function load_article_generation_module(){foreach(array('modules/content/class-article-package.php','modules/content/class-article-package-validator.php','modules/content/class-article-package-repository.php','modules/content/class-quality-evaluator.php','modules/content/class-quality-gate.php','modules/content/class-content-generator.php','modules/content/class-content-generation-controller.php') as $file)require_once AMANAH_SEO_AI_PATH.$file;$validator=new AMANAH_SEO_AI_Article_Package_Validator();$repository=new AMANAH_SEO_AI_Article_Package_Repository();$evaluator=new AMANAH_SEO_AI_Quality_Evaluator($this->ai_client);$analysis_repository=new AMANAH_SEO_AI_WordPress_Content_Analysis_Repository();$generator=new AMANAH_SEO_AI_Content_Generator($this->ai_client,$validator,$repository,$evaluator,$analysis_repository);$this->content_generation_controller=new AMANAH_SEO_AI_Content_Generation_Controller($generator,$repository);$this->content_generation_controller->register();}

	/**
	 * Load CORE-007 generation APIs through the existing governed AI client.
	 * Admin navigation is intentionally omitted until reviewed generation screens exist.
	 */
	private function load_core_007() {
		$files = array(
			'modules/faq/class-faq-validator.php',
			'modules/faq/class-faq-repository.php',
			'modules/faq/class-faq-entity-context-resolver.php',
			'modules/faq/class-faq-content-analysis-resolver.php',
			'modules/faq/class-faq-generator.php',
			'modules/faq/class-faq-controller.php',
			'modules/schema/class-schema-validator.php',
			'modules/schema/class-schema-repository.php',
			'modules/schema/class-schema-generator.php',
			'modules/schema/class-schema-type-resolver.php',
			'modules/schema/class-schema-controller.php',
		);
		foreach ( $files as $file ) {
			require_once AMANAH_SEO_AI_PATH . $file;
		}

		$faq_repository = new AMANAH_SEO_AI_FAQ_Repository();
		$faq_validator = new AMANAH_SEO_AI_FAQ_Validator();
		$entity_resolver = new AMANAH_SEO_AI_FAQ_Entity_Context_Resolver();
		$analysis_resolver = new AMANAH_SEO_AI_FAQ_Content_Analysis_Resolver();
		$faq_generator = new AMANAH_SEO_AI_FAQ_Generator( $this->ai_client, $faq_repository, $faq_validator, $entity_resolver, $analysis_resolver );
		( new AMANAH_SEO_AI_FAQ_Controller( $faq_generator ) )->register();

		$schema_repository = new AMANAH_SEO_AI_Schema_Repository();
		$schema_validator = new AMANAH_SEO_AI_Schema_Validator();
		$schema_generator = new AMANAH_SEO_AI_Schema_Generator( $this->ai_client, $schema_repository, $schema_validator );
		( new AMANAH_SEO_AI_Schema_Controller( $schema_generator ) )->register();
	}

	private function load_settings_surface(){if(!is_admin())return;require_once AMANAH_SEO_AI_PATH.'admin/class-ai-settings-controller.php';require_once AMANAH_SEO_AI_PATH.'admin/class-admin-menu.php';$settings=new AMANAH_SEO_AI_AI_Settings();$credentials=new AMANAH_SEO_AI_AI_Credentials($settings);$model_catalog=new AMANAH_SEO_AI_AI_Model_Catalog();$this->settings_controller=new AMANAH_SEO_AI_AI_Settings_Controller($settings,$credentials,$model_catalog);$this->settings_controller->register();$admin_menu=new AMANAH_SEO_AI_Admin_Menu($this->settings_controller);$admin_menu->register();}
	public function run(){$this->loader->run();}public function get_ai_client(){return $this->ai_client;}public function get_entity_controller(){return $this->entity_controller;}
}
