<?php
defined( 'ABSPATH' ) || exit;
class AMANAH_SEO_AI_AI_Provider_Resolver {
	private $settings;
	private $credentials;
	public function __construct( $settings, $credentials ) {
		$this->settings = $settings;
		$this->credentials = $credentials;
	}
	public function resolve( $user_id = null ) {
		$name = sanitize_key( (string) $this->settings->get( 'ai_provider', $user_id ) );
		if ( ! in_array($name,array('gemini','openai'),true) ) throw new AMANAH_SEO_AI_AI_Exception( 'No valid AI provider is configured.', 'configuration' );
		return $this->build($name,$user_id);
	}
	public function resolve_fallback( $user_id = null ) {
		$primary = sanitize_key( (string) $this->settings->get( 'ai_provider', $user_id ) );
		$fallback = 'gemini' === $primary ? 'openai' : 'gemini';
		$model_key = $fallback.'_model';
		$model = sanitize_text_field( (string) $this->settings->get($model_key,$user_id) );
		$key = $fallback.'_api_key';
		if ( '' === $model || ! $this->settings->get($key,$user_id) ) return null;
		return $this->build($fallback,$user_id);
	}
	private function build( $name, $user_id ) {
		$model = sanitize_text_field( (string) $this->settings->get($name.'_model',$user_id) );
		if ( '' === $model ) throw new AMANAH_SEO_AI_AI_Exception( 'AI provider model is not configured.', 'configuration' );
		if ( 'gemini' === $name ) return new AMANAH_SEO_AI_Gemini_Provider($this->credentials,$model,$user_id);
		return new AMANAH_SEO_AI_OpenAI_Provider($this->credentials,$model,$user_id);
	}
}
