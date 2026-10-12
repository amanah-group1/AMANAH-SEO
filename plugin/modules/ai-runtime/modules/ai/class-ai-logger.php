<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_AI_Logger {
	public function log( $task, AMANAH_SEO_AI_AI_Response $response, $prompt, $cost = null ) {
		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix.'amanah_ai_logs',
			array(
				'user_id'=>get_current_user_id() ?: null,
				'provider'=>$response->get_provider(),
				'model'=>$response->get_model(),
				'task_type'=>sanitize_key($task),
				'prompt'=>wp_kses_post($prompt),
				'response'=>wp_kses_post($response->get_content()),
				'input_token'=>$response->get_input_tokens(),
				'output_token'=>$response->get_output_tokens(),
				'cost'=>$cost,
			),
			array('%d','%s','%s','%s','%s','%s','%d','%d','%f')
		);
	}
}
