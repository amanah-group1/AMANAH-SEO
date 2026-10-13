<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_Quality_Gate {
	public function decide(array $evaluation){
		$severity=isset($evaluation['severity'])?sanitize_key($evaluation['severity']):'high';
		switch($severity){case 'critical':return 'NOT_READY';case 'high':return 'NEEDS_REFINEMENT';case 'medium':return 'IMPROVEMENT_RECOMMENDED';default:return 'READY_FOR_HUMAN_REVIEW';}
	}
}