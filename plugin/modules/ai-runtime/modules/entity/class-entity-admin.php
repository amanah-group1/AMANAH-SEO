<?php
defined('ABSPATH')||exit;
class AMANAH_SEO_AI_Entity_Admin{
 private $controller;private $model;
 public function __construct($controller,$model){$this->controller=$controller;$this->model=$model;}
 public function register(){add_action('admin_post_amanah_save_entity',array($this->controller,'handle_save'));add_action('admin_post_amanah_delete_entity',array($this->controller,'handle_delete'));add_action('admin_post_amanah_analyze_entity',array($this->controller,'handle_analyze'));add_action('rest_api_init',array($this,'register_rest'));add_action('admin_menu',array($this,'register_menu'),20);}
 public function register_rest(){$rest=new AMANAH_SEO_AI_Entity_REST_Controller($this->controller,$this->model);$rest->register();}
 public function register_menu(){add_submenu_page('amanah-seo',__('Entity Profile','amanah-seo-ai'),__('Entity Profile','amanah-seo-ai'),'manage_amanah_seo','amanah-entity-profile',array($this,'render'));}
 public function render(){if(!current_user_can('manage_amanah_seo'))wp_die(esc_html__('You are not allowed to view entities.','amanah-seo-ai'),403);$entities=$this->model->get_current_user_entities(get_current_user_id());$editing=!empty($_GET['entity_id'])?$this->model->get_entity(absint($_GET['entity_id']),get_current_user_id(),true):null;$message=isset($_GET['entity_message'])?sanitize_text_field(wp_unslash($_GET['entity_message'])):'';$analysis=isset($_GET['entity_analysis'])?json_decode(base64_decode(sanitize_text_field(wp_unslash($_GET['entity_analysis']))),true):null;require AMANAH_SEO_AI_PATH.'modules/entity/views/entity-form.php';}
}
