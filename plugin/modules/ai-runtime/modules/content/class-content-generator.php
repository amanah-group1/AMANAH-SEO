<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_Content_Generator {
	private $ai_client,$validator,$repository,$evaluator,$analysis_repository;
	public function __construct($ai_client,$validator,$repository,$evaluator,$analysis_repository=null){$this->ai_client=$ai_client;$this->validator=$validator;$this->repository=$repository;$this->evaluator=$evaluator;$this->analysis_repository=$analysis_repository;}
	private function ai_error($code,$error){return new WP_Error($code,$error->getMessage(),array('status'=>502,'diagnostic'=>array('category'=>$error->get_category(),'cause'=>$error->get_diagnostic_cause())));}
	private function attach_requirements(array $brief){
		if(!empty($brief['generation_requirements'])&&is_array($brief['generation_requirements'])){$brief['generation_requirements']=AMANAH_SEO_AI_Generation_Requirements::normalize($brief['generation_requirements']);return $brief;}
		if($this->analysis_repository&&!empty($brief['post_id'])){$analysis=$this->analysis_repository->find_latest_by_content(absint($brief['post_id']));if($analysis&&'COMPLETED'===$analysis->get_status()){$a=$analysis->get_analysis();if(!empty($a['generation_requirements']))$brief['generation_requirements']=AMANAH_SEO_AI_Generation_Requirements::normalize($a['generation_requirements']);}}
		return $brief;
	}
	public function generate(array $brief,$user_id){
		foreach(array('topic','primary_keyword','search_intent','content_type','target_audience') as $k){if(!isset($brief[$k])||''===trim((string)$brief[$k]))return new WP_Error('generation_brief_incomplete','Approved Content Brief is missing: '.$k.'.');}
		if(empty($brief['content_brief_id']))return new WP_Error('generation_brief_invalid','A valid approved Content Brief ID is required.');
		$brief=$this->attach_requirements($brief); $version=$this->repository->next_version($brief['content_brief_id']);
		try{$response=$this->ai_client->generate('content_generation',array('brief'=>$brief),$user_id);}catch(AMANAH_SEO_AI_AI_Exception $error){return $this->ai_error('generation_ai_failure',$error);}
		$result=isset($response['result'])&&is_array($response['result'])?$response['result']:array();$raw=isset($result['article_package'])&&is_array($result['article_package'])?$result['article_package']:array();$valid=$this->validator->validate($raw);if(is_wp_error($valid))return $valid;
		try{$quality=$this->evaluator->evaluate($raw,$brief,$user_id);}catch(AMANAH_SEO_AI_AI_Exception $error){return $this->ai_error('generation_quality_failure',$error);}
		$latest=$this->repository->latest($brief['content_brief_id']);$package_id=$latest?$latest->get_package_id():wp_generate_uuid4();$parent=$latest?$latest->get_version():null;$status=(new AMANAH_SEO_AI_Quality_Gate())->decide($quality);
		$package=new AMANAH_SEO_AI_Article_Package($package_id,$brief['content_brief_id'],$version,$parent,$status,$raw,$quality,array('user_id'=>absint($user_id),'provider'=>isset($response['provider'])?sanitize_text_field($response['provider']):'','model'=>isset($response['model'])?sanitize_text_field($response['model']):'','operation'=>'generation','generation_requirements_attached'=>!empty($brief['generation_requirements'])));
		if(!$this->repository->save($package))return new WP_Error('generation_persistence_failed','Article Package could not be saved.');return $package->to_array();
	}
	public function refine(array $brief,array $current_package,$user_id,$instruction=''){
		foreach(array('topic','primary_keyword','search_intent','content_type','target_audience') as $k){if(!isset($brief[$k])||''===trim((string)$brief[$k]))return new WP_Error('refinement_brief_incomplete','Content Brief context is incomplete.');}
		$brief=$this->attach_requirements($brief);$latest=$this->repository->latest(absint($brief['content_brief_id']??0));if(!$latest)return new WP_Error('refinement_source_missing','No existing Article Package is available for refinement.');
		try{$response=$this->ai_client->generate('content_refinement',array('brief'=>$brief,'current_package'=>$current_package,'refinement_instruction'=>$instruction),$user_id);}catch(AMANAH_SEO_AI_AI_Exception $error){return $this->ai_error('refinement_ai_failure',$error);}
		$result=isset($response['result'])&&is_array($response['result'])?$response['result']:array();$raw=isset($result['article_package'])&&is_array($result['article_package'])?$result['article_package']:array();$valid=$this->validator->validate($raw);if(is_wp_error($valid))return $valid;
		try{$quality=$this->evaluator->evaluate($raw,$brief,$user_id);}catch(AMANAH_SEO_AI_AI_Exception $error){return $this->ai_error('refinement_quality_failure',$error);}
		$status=(new AMANAH_SEO_AI_Quality_Gate())->decide($quality);$version=$latest->get_version()+1;$package=new AMANAH_SEO_AI_Article_Package($latest->get_package_id(),$latest->get_content_brief_id(),$version,$latest->get_version(),$status,$raw,$quality,array('user_id'=>absint($user_id),'operation'=>'refinement','instruction'=>sanitize_textarea_field($instruction),'generation_requirements_attached'=>!empty($brief['generation_requirements'])));
		if(!$this->repository->save($package))return new WP_Error('refinement_persistence_failed','Refined Article Package could not be saved.');return $package->to_array();
	}
}
