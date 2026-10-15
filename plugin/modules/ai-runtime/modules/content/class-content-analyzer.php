<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_Content_Analyzer {
	private $ai_client; private $repository; private $requirements_builder;
	public function __construct( $ai_client, $repository, $requirements_builder = null ) { $this->ai_client=$ai_client; $this->repository=$repository; $this->requirements_builder=$requirements_builder ?: new AMANAH_SEO_AI_Generation_Requirements_Builder(); }
	public function analyze( $post_id, $user_id, array $context = array() ) {
		$post_id=absint($post_id); $user_id=absint($user_id);
		if(!$post_id||!$user_id)return new WP_Error('invalid_parameter',__('A valid post is required.','amanah-seo-ai'),array('status'=>400));
		$post=get_post($post_id); if(!$post)return new WP_Error('not_found',__('Content was not found.','amanah-seo-ai'),array('status'=>404));
		if(!current_user_can('edit_post',$post_id))return new WP_Error('permission_denied',__('You are not allowed to analyze this content.','amanah-seo-ai'),array('status'=>403));
		$title=sanitize_text_field(get_the_title($post_id)); $content=(string)$post->post_content;
		$keyword=isset($context['keyword'])?sanitize_text_field($context['keyword']):''; $entity=isset($context['entity'])?sanitize_text_field($context['entity']):'';
		$input=new AMANAH_SEO_AI_Content_Analysis_Input($title,$content,$keyword,$entity); $validation=$this->validate_input($input); if(is_wp_error($validation))return $validation;
		$version=$this->repository->next_version($post_id); if(is_wp_error($version)||!$version)return new WP_Error('database_error',__('Content analysis version could not be prepared.','amanah-seo-ai'),array('status'=>500));
		$analysis_id=wp_generate_uuid4(); $content_version=$post->post_modified_gmt?$post->post_modified_gmt:(string)$post->post_modified;
		$content_reference=array('content_id'=>$post_id,'content_version'=>sanitize_text_field($content_version),'context'=>'wordpress_post');
		$input_context=array('title'=>$input->get_title(),'keyword'=>$input->get_keyword(),'entity'=>$input->get_entity());
		$pending=new AMANAH_SEO_AI_Content_Analysis($analysis_id,$content_reference,$input_context,array(),'PENDING',$version,array('user_id'=>$user_id));
		if(false===$this->repository->save($pending))return new WP_Error('database_error',__('Content analysis could not be initialized.','amanah-seo-ai'),array('status'=>500));
		try {
			$response=$this->ai_client->generate('content_analysis',$input->to_array(),$user_id); $result=isset($response['result'])&&is_array($response['result'])?$response['result']:array();
			if(!isset($result['analysis'])||!is_array($result['analysis'])||empty($result['analysis']))throw new AMANAH_SEO_AI_AI_Exception('AI response analysis is invalid.','validation',400,'response_contract_invalid_analysis');
			$contract=$this->validate_analysis_contract($result['analysis']); if(is_wp_error($contract))throw new AMANAH_SEO_AI_AI_Exception($contract->get_error_message(),'validation',400,'response_contract_invalid_analysis');
			$analysis=$this->sanitize_analysis($result['analysis']); $analysis['generation_requirements']=$this->requirements_builder->build($analysis);
			$traceability=array('user_id'=>$user_id,'provider'=>isset($response['provider'])?sanitize_text_field($response['provider']):'','model'=>isset($response['model'])?sanitize_text_field($response['model']):'');
			$completed=$pending->with_result($analysis,$traceability); if(!$this->repository->update($completed))return new WP_Error('database_error',__('Content analysis could not be completed in storage.','amanah-seo-ai'),array('status'=>500));
			return (new AMANAH_SEO_AI_Content_Analysis_Result($completed))->to_array();
		} catch(AMANAH_SEO_AI_AI_Exception $error) {
			$diagnostic=array('category'=>sanitize_key($error->get_category()),'cause'=>sanitize_key($error->get_diagnostic_cause()),'message'=>sanitize_text_field($error->getMessage()));
			$failed=$pending->with_failure(array_merge(array('user_id'=>$user_id),$diagnostic)); $this->repository->update($failed); $is_validation='validation'===$error->get_category();
			return new WP_Error($is_validation?'analysis_response_validation':'ai_execution_failure',$is_validation?__('The AI analysis response did not satisfy the content analysis contract.','amanah-seo-ai'):__('Content analysis could not be completed by the AI service.','amanah-seo-ai'),array('status'=>502,'diagnostic'=>$diagnostic,'analysis_id'=>$analysis_id));
		}
	}
	private function validate_input(AMANAH_SEO_AI_Content_Analysis_Input $input){foreach(array('title'=>$input->get_title(),'content'=>$input->get_content(),'entity'=>$input->get_entity()) as $field=>$value){if(''===trim((string)$value))return new WP_Error('missing_'.$field,sprintf(__('%s is required for content analysis.','amanah-seo-ai'),ucfirst($field)),array('status'=>400));}return true;}
	private function validate_analysis_contract(array $analysis){
		$required_arrays=array('strengths','coverage_gaps','weaknesses','recommendations','faq_suggestions','recommended_direction');
		foreach($required_arrays as $field){if(!isset($analysis[$field])||!is_array($analysis[$field]))return new WP_Error('missing_analysis_field','AI analysis field is missing or invalid: '.$field.'.');}
		if(!isset($analysis['overall_assessment'])||!is_scalar($analysis['overall_assessment'])||''===trim((string)$analysis['overall_assessment']))return new WP_Error('missing_analysis_field','AI analysis field is missing or invalid: overall_assessment.');
		if(!isset($analysis['content_understanding'])||!is_scalar($analysis['content_understanding'])||''===trim((string)$analysis['content_understanding']))return new WP_Error('missing_analysis_field','AI analysis field is missing or invalid: content_understanding.');
		if(!isset($analysis['score'])||!is_int($analysis['score'])||$analysis['score']<0||$analysis['score']>100)return new WP_Error('invalid_analysis_score','AI analysis score must be an integer from 0 to 100.');
		if(!isset($analysis['quality_dimensions'])||!is_array($analysis['quality_dimensions']))return new WP_Error('missing_analysis_field','AI analysis field is missing or invalid: quality_dimensions.');
		foreach(AMANAH_SEO_AI_Generation_Requirements::DIMENSIONS as $dimension){if(!isset($analysis['quality_dimensions'][$dimension])||!is_array($analysis['quality_dimensions'][$dimension]))return new WP_Error('missing_quality_dimension','AI analysis quality dimension is missing: '.$dimension.'.');$d=$analysis['quality_dimensions'][$dimension];if(!isset($d['assessment'])||!is_scalar($d['assessment'])||''===trim((string)$d['assessment']))return new WP_Error('invalid_quality_dimension','AI analysis quality dimension assessment is missing: '.$dimension.'.');if(!isset($d['status'])||!in_array($d['status'],array('ASSESSED','UNKNOWN / UNASSESSED','UNKNOWN/UNASSESSED'),true))return new WP_Error('invalid_quality_dimension','AI analysis quality dimension status is invalid: '.$dimension.'.');}
		return true;
	}
	private function sanitize_analysis(array $analysis){$clean=array();foreach($analysis as $key=>$value){$safe_key=sanitize_key($key);if(''!==$safe_key)$clean[$safe_key]=$this->sanitize_analysis_value($value);}return $clean;}
	private function sanitize_analysis_value($value){if(is_array($value)){ $clean=array();foreach($value as $key=>$item){$clean[is_string($key)?sanitize_key($key):$key]=$this->sanitize_analysis_value($item);}return $clean;}if(is_bool($value)||is_int($value)||is_float($value)||null===$value)return $value;return sanitize_textarea_field((string)$value);}
}
