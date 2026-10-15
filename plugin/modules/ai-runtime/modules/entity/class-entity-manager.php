<?php
defined('ABSPATH')||exit;
class AMANAH_SEO_AI_Entity_Manager{
 private $model;private $ai_client;
 public function __construct($model,$ai_client=null){$this->model=$model;$this->ai_client=$ai_client;}
 public function analyze($entity){if(!$this->ai_client)throw new AMANAH_SEO_AI_AI_Exception('AI service is unavailable.','configuration');return $this->ai_client->generate('entity_analysis',array('entity'=>wp_json_encode(array('entity_name'=>$entity->entity_name,'entity_type'=>$entity->entity_type,'industry'=>$entity->industry,'description'=>$entity->description,'location'=>$entity->location,'expertise'=>$entity->expertise,'products'=>$entity->products,'services'=>$entity->services,'target_audience'=>$entity->target_audience))),get_current_user_id());}
}
