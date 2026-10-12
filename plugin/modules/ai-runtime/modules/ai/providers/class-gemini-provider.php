<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_Gemini_Provider implements AMANAH_SEO_AI_AI_Provider_Interface {
	private $credentials;
	private $model;
	private $user_id;
	public function __construct( $credentials, $model, $user_id = null ) { $this->credentials=$credentials; $this->model=$model; $this->user_id=$user_id; }
	public function get_name() { return 'gemini'; }
	public function generate( AMANAH_SEO_AI_AI_Request $request ) {
		$key = $this->credentials->get( 'gemini_api_key', $this->user_id );
		if ( ! $key ) throw new AMANAH_SEO_AI_AI_Exception( 'Gemini credentials are not configured.', 'configuration' );
		$url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode( $this->model ) . ':generateContent';
		$response = wp_remote_post( $url, array( 'timeout'=>60, 'headers'=>array( 'Content-Type'=>'application/json', 'x-goog-api-key'=>$key ), 'body'=>wp_json_encode( array( 'contents'=>array( array( 'role'=>'user', 'parts'=>array( array( 'text'=>$request->get_prompt() ) ) ) ) ) ) ) );
		if ( is_wp_error( $response ) ) throw new AMANAH_SEO_AI_AI_Exception( $response->get_error_message(), 'transport' );
		$status = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $status < 200 || $status >= 300 ) {
			$category = 429 === $status || $status >= 500 ? 'retryable' : ( in_array( $status, array(401,403), true ) ? 'authentication' : 'provider' );
			$provider_message = '';
			if ( is_array( $body ) && isset( $body['error']['message'] ) ) {
				$provider_message = sanitize_text_field( $body['error']['message'] );
			}
			$diagnostic_cause = 429 === $status ? 'provider_quota_exceeded' : ( 404 === $status ? 'provider_model_not_found' : ( 400 === $status ? 'provider_request_invalid' : ( in_array( $status, array(401,403), true ) ? 'authentication_failed' : ( $status >= 500 ? 'provider_server_failure' : 'provider_request_failure' ) ) ) );
			$message = 'Gemini provider request failed.';
			if ( '' !== $provider_message ) {
				$message .= ' ' . $provider_message;
			}
			throw new AMANAH_SEO_AI_AI_Exception( $message, $category, $status, $diagnostic_cause );
		}
		$text = isset( $body['candidates'][0]['content']['parts'][0]['text'] ) ? $body['candidates'][0]['content']['parts'][0]['text'] : '';
		if ( '' === $text ) throw new AMANAH_SEO_AI_AI_Exception( 'Gemini returned an empty response.', 'provider' );
		$usage = isset( $body['usageMetadata'] ) ? $body['usageMetadata'] : array();
		$input = isset( $usage['promptTokenCount'] ) ? $usage['promptTokenCount'] : null;
		$output = isset( $usage['candidatesTokenCount'] ) ? $usage['candidatesTokenCount'] : null;
		return new AMANAH_SEO_AI_AI_Response( $text, 'gemini', $this->model, $input, $output );
	}
}
