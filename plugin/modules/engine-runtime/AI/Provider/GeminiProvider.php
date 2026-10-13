<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI\Provider;

use Amanah\SEOEngine\AI\AIRequest;
use Amanah\SEOEngine\AI\AIResponse;
use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;

defined( 'ABSPATH' ) || exit;

final class GeminiProvider implements AIProviderInterface {

	private $http;

	public function __construct( HttpClientInterface $http ) {
		$this->http = $http;
	}

	public function getProviderIdentifier(): string {
		return 'gemini';
	}

	public function getProviderName(): string {
		return 'Gemini';
	}

	public function getCapabilities(): array {
		return array(
			ProviderCapabilities::TEXT_GENERATION,
			ProviderCapabilities::STRUCTURED_OUTPUT,
			ProviderCapabilities::MODEL_DISCOVERY,
			ProviderCapabilities::USAGE_REPORTING,
		);
	}

	public function validateCredentials( array $credentials ): array {
		$api_key = isset( $credentials['api_key'] ) ? trim( (string) $credentials['api_key'] ) : '';

		if ( '' === $api_key ) {
			return array(
				'valid'    => false,
				'category' => 'authentication_error',
				'message'  => 'Gemini API key is missing.',
			);
		}

		$response = $this->http->request(
			'GET',
			'https://generativelanguage.googleapis.com/v1beta/models',
			array(
				'headers' => array( 'x-goog-api-key' => $api_key ),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'valid'       => false,
				'category'    => 'network_error',
				'message'     => $response->get_error_message(),
				'http_status' => 0,
			);
		}

		$status = (int) wp_remote_retrieve_response_code( $response );

		if ( $status >= 400 ) {
			$error = $this->normalizeError( $response );
			return array_merge(
				array(
					'valid'       => false,
					'http_status' => $status,
				),
				$error
			);
		}

		$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		$models = isset( $body['models'] ) && is_array( $body['models'] ) ? $body['models'] : array();

		return array(
			'valid'        => true,
			'category'     => null,
			'http_status'  => $status,
			'models_count' => count( $models ),
		);
	}

	public function discoverModels( array $credentials ): array {
		$api_key = isset( $credentials['api_key'] ) ? trim( (string) $credentials['api_key'] ) : '';

		$response = $this->http->request(
			'GET',
			'https://generativelanguage.googleapis.com/v1beta/models',
			array(
				'headers' => array( 'x-goog-api-key' => $api_key ),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) || (int) wp_remote_retrieve_response_code( $response ) >= 400 ) {
			return array();
		}

		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		$out  = array();

		foreach ( (array) ( $body['models'] ?? array() ) as $model ) {
			if ( ! isset( $model['name'] ) ) {
				continue;
			}

			$methods = isset( $model['supportedGenerationMethods'] ) && is_array( $model['supportedGenerationMethods'] )
				? $model['supportedGenerationMethods']
				: array();

			if ( ! empty( $methods ) && ! in_array( 'generateContent', $methods, true ) ) {
				continue;
			}

			$id = preg_replace( '#^models/#', '', (string) $model['name'] );

			$out[] = array(
				'model_identifier' => $id,
				'display_name'     => isset( $model['displayName'] ) && '' !== trim( (string) $model['displayName'] )
					? (string) $model['displayName']
					: $id,
				'capabilities'     => $this->getCapabilities(),
				'metadata'         => array( 'supported_generation_methods' => $methods ),
			);
		}

		return $out;
	}

	public function generate( AIRequest $request, array $credentials ): AIResponse {
		$this->prepareExecutionWindow();

		$api_key = isset( $credentials['api_key'] ) ? trim( (string) $credentials['api_key'] ) : '';
		$url     = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode( $request->getModel() ) . ':generateContent';

		$parts = array( array( 'text' => $request->getPrompt() ) );

		if ( $request->getContext() ) {
			$parts[] = array( 'text' => "Context:\n" . wp_json_encode( $request->getContext() ) );
		}

		$payload = array(
			'contents' => array(
				array(
					'parts' => $parts,
				),
			),
		);

		if ( $request->isStructured() ) {
			$payload['generationConfig'] = array(
				'responseMimeType' => 'application/json',
			);
		}

		$response = null;
		$max_attempts = 3;

		for ( $attempt = 1; $attempt <= $max_attempts; $attempt++ ) {
			$response = $this->http->request(
				'POST',
				$url,
				array(
					'headers' => array(
						'Content-Type'   => 'application/json',
						'x-goog-api-key' => $api_key,
					),
					'body'    => wp_json_encode( $payload ),
					'timeout' => 60,
				)
			);

			if ( is_wp_error( $response ) ) {
				throw new \RuntimeException( 'network_error: ' . $response->get_error_message() );
			}

			$status = (int) wp_remote_retrieve_response_code( $response );

			if ( ! $this->isTransientStatus( $status ) || $attempt === $max_attempts ) {
				break;
			}

			sleep( 3 * ( 2 ** ( $attempt - 1 ) ) );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		$effective_model = $request->getModel();

		// A 503 from Gemini can be model-specific capacity pressure. Keep the
		// project on Gemini, but fall back to a current Flash-Lite model so
		// generation can continue without requiring a provider switch.
		if ( 503 === $status && 'gemini-3.5-flash-lite' !== $request->getModel() ) {
			$fallback_model = 'gemini-3.5-flash-lite';
			$fallback_url   = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode( $fallback_model ) . ':generateContent';

			for ( $attempt = 1; $attempt <= 2; $attempt++ ) {
				$response = $this->http->request(
					'POST',
					$fallback_url,
					array(
						'headers' => array(
							'Content-Type'   => 'application/json',
							'x-goog-api-key' => $api_key,
						),
						'body'    => wp_json_encode( $payload ),
						'timeout' => 60,
					)
				);

				if ( is_wp_error( $response ) ) {
					throw new \RuntimeException( 'network_error: ' . $response->get_error_message() );
				}

				$status = (int) wp_remote_retrieve_response_code( $response );

				if ( 503 !== $status || $attempt === 2 ) {
					break;
				}

				sleep( 3 );
			}

			if ( $status < 400 ) {
				$effective_model = $fallback_model;
			}
		}

		if ( $status >= 400 ) {
			$error  = $this->normalizeError( $response );
			$detail = $this->formatErrorDetail( $error, $status );
			throw new \RuntimeException( (string) $error['category'] . ': ' . $detail );
		}

		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) ) {
			throw new \RuntimeException( 'invalid_response_error: Gemini returned an invalid JSON response.' );
		}

		$text = (string) ( $body['candidates'][0]['content']['parts'][0]['text'] ?? '' );

		if ( '' === trim( $text ) ) {
			throw new \RuntimeException( 'empty_response_error: Gemini returned no generated content.' );
		}

		$usage_metadata = $body['usageMetadata'] ?? array();
		$usage           = isset( $body['usageMetadata'] )
			? array(
				'input_tokens'  => $usage_metadata['promptTokenCount'] ?? null,
				'output_tokens' => $usage_metadata['candidatesTokenCount'] ?? null,
				'total_tokens'  => $usage_metadata['totalTokenCount'] ?? null,
				'usage_status'  => 'reported',
			)
			: array( 'usage_status' => 'unknown' );

		if ( isset( $body['usage']['estimated_cost'] ) && is_numeric( $body['usage']['estimated_cost'] ) ) {
			$usage['estimated_cost']  = (float) $body['usage']['estimated_cost'];
			$usage['currency']        = isset( $body['usage']['currency'] ) ? (string) $body['usage']['currency'] : null;
			$usage['pricing_source']  = isset( $body['usage']['pricing_source'] ) ? (string) $body['usage']['pricing_source'] : 'provider_reported';
		}

		return new AIResponse(
			'gemini',
			$effective_model,
			$text,
			$usage,
			$body
		);
	}

	private function isTransientStatus( int $status ): bool {
		return 408 === $status || 429 === $status || $status >= 500;
	}

	private function prepareExecutionWindow(): void {
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 180 );
		}

		if ( function_exists( 'ini_set' ) ) {
			@ini_set( 'max_execution_time', '180' );
		}
	}

	public function normalizeError( $response ): array {
		if ( is_wp_error( $response ) ) {
			return array(
				'category' => 'network_error',
				'message'  => $response->get_error_message(),
			);
		}

		$status  = (int) wp_remote_retrieve_response_code( $response );
		$body    = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		$error   = is_array( $body ) && isset( $body['error'] ) && is_array( $body['error'] ) ? $body['error'] : array();
		$message = isset( $error['message'] ) ? (string) $error['message'] : '';
		$reason  = '';

		foreach ( (array) ( $error['details'] ?? array() ) as $detail ) {
			if ( is_array( $detail ) && isset( $detail['reason'] ) && '' !== trim( (string) $detail['reason'] ) ) {
				$reason = (string) $detail['reason'];
				break;
			}
		}

		if ( 401 === $status || 403 === $status ) {
			return array(
				'category' => 'authentication_error',
				'reason'   => $reason,
				'message'  => $message,
			);
		}

		if ( 404 === $status ) {
			return array(
				'category' => 'invalid_model_error',
				'reason'   => $reason,
				'message'  => $message,
			);
		}

		if ( 429 === $status ) {
			return array(
				'category' => 'rate_limit_error',
				'reason'   => $reason,
				'message'  => $message,
			);
		}

		if ( 408 === $status || $status >= 500 ) {
			return array(
				'category' => 'provider_unavailable_error',
				'reason'   => $reason,
				'message'  => $message,
			);
		}

		return array(
			'category' => 'invalid_request_error',
			'reason'   => $reason,
			'message'  => $message,
		);
	}

	private function formatErrorDetail( array $error, int $status ): string {
		$parts = array( 'HTTP ' . $status );

		if ( ! empty( $error['reason'] ) ) {
			$parts[] = 'reason=' . sanitize_text_field( (string) $error['reason'] );
		}

		if ( ! empty( $error['message'] ) ) {
			$parts[] = sanitize_text_field( (string) $error['message'] );
		}

		return implode( ' | ', $parts );
	}
}
