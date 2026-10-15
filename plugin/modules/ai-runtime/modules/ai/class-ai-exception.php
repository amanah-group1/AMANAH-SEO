<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Exception extends Exception {
	private $category;
	private $diagnostic_cause;
	private $diagnostic_message;

	public function __construct( $message, $category = 'unknown', $code = 0, $diagnostic_cause = 'unknown' ) {
		$this->category = sanitize_key( $category );
		$this->diagnostic_cause = sanitize_key( $diagnostic_cause );
		$this->diagnostic_message = sanitize_text_field( $message );

		if ( 'unknown' === $this->diagnostic_cause || '' === $this->diagnostic_cause ) {
			$this->diagnostic_cause = $this->derive_diagnostic_cause( $message );
		}

		parent::__construct( $message, $code );
	}

	public function get_category() {
		return $this->category;
	}

	public function get_diagnostic_cause() {
		return $this->diagnostic_cause;
	}

	public function get_diagnostic_message() {
		return $this->diagnostic_message;
	}

	private function derive_diagnostic_cause( $message ) {
		$message = strtolower( (string) $message );

		if ( 'configuration' === $this->category ) {
			return 'configuration_missing';
		}

		if ( 'authentication' === $this->category ) {
			return 'authentication_failed';
		}

		if ( 'transport' === $this->category ) {
			return 'transport_failure';
		}

		if ( 'retryable' === $this->category ) {
			return 'provider_request_retryable_failure';
		}

		if ( 'validation' === $this->category ) {
			if ( false !== strpos( $message, 'could not be parsed' ) ) {
				return 'response_json_parse_failure';
			}

			if ( false !== strpos( $message, 'missing a required field' ) ) {
				return 'response_contract_missing_field';
			}

			if ( false !== strpos( $message, 'format is invalid' ) ) {
				return 'response_format_invalid';
			}

			if ( false !== strpos( $message, 'unsupported ai task' ) ) {
				return 'unsupported_ai_task';
			}

			if ( false !== strpos( $message, 'request is invalid' ) ) {
				return 'ai_request_invalid';
			}

			return 'response_validation_failure';
		}

		if ( 'provider' === $this->category ) {
			if ( false !== strpos( $message, 'empty response' ) ) {
				return 'provider_empty_response';
			}

			return 'provider_request_failure';
		}

		return 'unknown_ai_failure';
	}
}
