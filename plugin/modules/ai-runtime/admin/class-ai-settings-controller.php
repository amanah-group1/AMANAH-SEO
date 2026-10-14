<?php
/**
 * AI configuration controller.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Settings_Controller {
	private $settings;
	private $credentials;
	private $model_catalog;

	public function __construct( $settings, $credentials, $model_catalog ) {
		$this->settings = $settings;
		$this->credentials = $credentials;
		$this->model_catalog = $model_catalog;
	}

	public function register() {
		add_action( 'admin_post_amanah_seo_ai_save_settings', array( $this, 'save' ) );
	}

	public function render() {
		if ( ! current_user_can( 'manage_amanah_seo' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage AMANAH SEO settings.', 'amanah-seo-ai' ) );
		}

		$provider = sanitize_key( (string) $this->settings->get( 'ai_provider' ) );
		$gemini_model = sanitize_text_field( (string) $this->settings->get( 'gemini_model' ) );
		$openai_model = sanitize_text_field( (string) $this->settings->get( 'openai_model' ) );
		$gemini_configured = false;
		$openai_configured = false;
		$gemini_models = array();
		$openai_models = array();

		try {
			$gemini_key = $this->credentials->get( 'gemini_api_key' );
			$gemini_configured = null !== $gemini_key;
		} catch ( AMANAH_SEO_AI_AI_Exception $exception ) {
			$gemini_key = '';
			$gemini_configured = false;
		}

		// The model selector must render its controlled catalog even when no API
		// key is stored. Credentials are required for execution, not for listing
		// the models available in the Settings UI.
		$gemini_models = $this->model_catalog->get_models( 'gemini', $gemini_key );

		try {
			$openai_key = $this->credentials->get( 'openai_api_key' );
			$openai_configured = null !== $openai_key;
		} catch ( AMANAH_SEO_AI_AI_Exception $exception ) {
			$openai_key = '';
			$openai_configured = false;
		}

		$openai_models = $this->model_catalog->get_models( 'openai', $openai_key );

		$current_model = 'gemini' === $provider ? $gemini_model : $openai_model;

		$data = array(
			'provider' => $provider,
			'current_model' => $current_model,
			'gemini_model' => $gemini_model,
			'openai_model' => $openai_model,
			'gemini_configured' => $gemini_configured,
			'openai_configured' => $openai_configured,
			'gemini_models' => $gemini_models,
			'openai_models' => $openai_models,
			'license' => $this->get_license_record(),
		);

		require AMANAH_SEO_AI_PATH . 'admin/views/ai-settings.php';
	}

	public function save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage AMANAH SEO AI settings.', 'amanah-seo-ai' ) );
		}

		check_admin_referer( 'amanah_seo_ai_save_settings' );

		$provider = isset( $_POST['ai_provider'] ) ? sanitize_key( wp_unslash( $_POST['ai_provider'] ) ) : '';
		$ai_model = isset( $_POST['ai_model'] ) ? sanitize_text_field( wp_unslash( $_POST['ai_model'] ) ) : '';

		if ( ! in_array( $provider, array( 'gemini', 'openai' ), true ) ) {
			$this->redirect_with_error( 'invalid_provider' );
		}

		$gemini_model = sanitize_text_field( (string) $this->settings->get( 'gemini_model' ) );
		$openai_model = sanitize_text_field( (string) $this->settings->get( 'openai_model' ) );

		$credential_key = $provider . '_api_key';
		$credential = isset( $_POST[ $credential_key ] ) ? trim( (string) wp_unslash( $_POST[ $credential_key ] ) ) : '';

		try {
			$discovery_key = '' !== $credential ? $credential : (string) $this->credentials->get( $credential_key );
		} catch ( AMANAH_SEO_AI_AI_Exception $exception ) {
			$discovery_key = '';
		}

		if ( '' !== $credential ) {
			try {
				if ( ! $this->credentials->set( $credential_key, $credential ) ) {
					$this->redirect_with_error( 'credential_save_failed' );
				}
			} catch ( AMANAH_SEO_AI_AI_Exception $exception ) {
				$this->redirect_with_error( 'credential_save_failed' );
			}
		}

		if ( '' !== $ai_model ) {
			$available_models = $this->model_catalog->get_models( $provider, $discovery_key );
			if ( ! in_array( $ai_model, $available_models, true ) ) {
				$this->redirect_with_error( 'invalid_model' );
			}

			if ( 'gemini' === $provider ) {
				$gemini_model = $ai_model;
			} else {
				$openai_model = $ai_model;
			}
		}

		if ( ! $this->settings->set( 'ai_provider', $provider ) || ! $this->settings->set( 'gemini_model', $gemini_model ) || ! $this->settings->set( 'openai_model', $openai_model ) ) {
			$this->redirect_with_error( 'save_failed' );
		}

		$redirect = add_query_arg(
			array( 'page' => 'amanah-seo-access', 'updated' => '1' ),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect );
		exit;
	}

	public function save_license() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage the AMANAH SEO AI license.', 'amanah-seo-ai' ) );
		}

		check_admin_referer( 'amanah_seo_ai_save_license' );

		$license_key = isset( $_POST['license_key'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) ) : '';
		if ( '' === $license_key ) {
			$this->redirect_with_license_error( 'empty_license' );
		}

		global $wpdb;
		$table = $wpdb->prefix . 'amanah_license';
		$user_id = get_current_user_id();
		$existing_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE user_id = %d ORDER BY id DESC LIMIT 1",
				$user_id
			)
		);

		$data = array(
			'user_id' => $user_id,
			'license_key' => $license_key,
			'domain' => wp_parse_url( home_url(), PHP_URL_HOST ),
			'status' => 'pending_activation',
			'last_check' => current_time( 'mysql', true ),
		);

		$formats = array( '%d', '%s', '%s', '%s', '%s' );

		if ( $existing_id ) {
			$updated = $wpdb->update( $table, $data, array( 'id' => absint( $existing_id ) ), $formats, array( '%d' ) );
		} else {
			$updated = $wpdb->insert( $table, $data, $formats );
		}

		if ( false === $updated ) {
			$this->redirect_with_license_error( 'save_failed' );
		}

		$redirect = add_query_arg(
			array( 'page' => 'amanah-seo-ai-settings', 'license_saved' => '1' ),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect );
		exit;
	}

	private function get_license_record() {
		global $wpdb;

		$table = $wpdb->prefix . 'amanah_license';
		$user_id = get_current_user_id();
		$record = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT license_key, plan, domain, status, expired_date, last_check FROM {$table} WHERE user_id = %d ORDER BY id DESC LIMIT 1",
				$user_id
			),
			ARRAY_A
		);

		if ( ! is_array( $record ) ) {
			return array(
				'license_key' => '',
				'plan' => '',
				'domain' => '',
				'status' => 'not_configured',
				'expired_date' => '',
				'last_check' => '',
			);
		}

		return $record;
	}

	private function redirect_with_error( $code ) {
		$redirect = add_query_arg(
			array( 'page' => 'amanah-seo-ai-settings', 'error' => sanitize_key( $code ) ),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}

	private function redirect_with_license_error( $code ) {
		$redirect = add_query_arg(
			array( 'page' => 'amanah-seo-ai-settings', 'license_error' => sanitize_key( $code ) ),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}
}
