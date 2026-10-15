<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\AI;

defined( 'ABSPATH' ) || exit;

final class SecretStore {

	private const OPTION_KEY = 'amanah_seo_ai_credentials';
	private const GCM_CIPHER = 'aes-256-gcm';
	private const GCM_TAG_LENGTH = 16;
	private const GCM_VERSION = 'v2:';

	public function set( string $provider, string $secret ): void {
		$data = get_option( self::OPTION_KEY, array() );
		$data = is_array( $data ) ? $data : array();
		$data[ $provider ] = $this->encrypt( $secret );
		update_option( self::OPTION_KEY, $data, false );
	}

	public function get( string $provider ): ?string {
		$data = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $data ) || empty( $data[ $provider ] ) ) {
			return null;
		}

		return $this->decrypt( (string) $data[ $provider ] );
	}

	public function delete( string $provider ): void {
		$data = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $data ) ) {
			return;
		}

		unset( $data[ $provider ] );
		update_option( self::OPTION_KEY, $data, false );
	}

	private function key(): string {
		$seed = defined( 'AUTH_KEY' ) ? AUTH_KEY : 'amanah-seo-engine';
		return hash( 'sha256', $seed, true );
	}

	private function encrypt( string $value ): string {
		$iv_length = openssl_cipher_iv_length( self::GCM_CIPHER );
		$iv = random_bytes( $iv_length );
		$tag = '';

		$ciphertext = openssl_encrypt(
			$value,
			self::GCM_CIPHER,
			$this->key(),
			OPENSSL_RAW_DATA,
			$iv,
			$tag,
			'',
			self::GCM_TAG_LENGTH
		);

		if ( false === $ciphertext || self::GCM_TAG_LENGTH !== strlen( $tag ) ) {
			throw new \RuntimeException( 'secret_storage_error' );
		}

		return self::GCM_VERSION . base64_encode( $iv . $tag . $ciphertext );
	}

	private function decrypt( string $value ): ?string {
		if ( 0 === strpos( $value, self::GCM_VERSION ) ) {
			return $this->decryptGcm( substr( $value, strlen( self::GCM_VERSION ) ) );
		}

		return $this->decryptLegacyCbc( $value );
	}

	private function decryptGcm( string $encoded ): ?string {
		$raw = base64_decode( $encoded, true );
		$iv_length = openssl_cipher_iv_length( self::GCM_CIPHER );

		if ( false === $raw || strlen( $raw ) <= $iv_length + self::GCM_TAG_LENGTH ) {
			return null;
		}

		$iv = substr( $raw, 0, $iv_length );
		$tag = substr( $raw, $iv_length, self::GCM_TAG_LENGTH );
		$ciphertext = substr( $raw, $iv_length + self::GCM_TAG_LENGTH );

		$result = openssl_decrypt(
			$ciphertext,
			self::GCM_CIPHER,
			$this->key(),
			OPENSSL_RAW_DATA,
			$iv,
			$tag
		);

		return false === $result ? null : $result;
	}

	private function decryptLegacyCbc( string $value ): ?string {
		$raw = base64_decode( $value, true );
		if ( false === $raw || strlen( $raw ) < 17 ) {
			return null;
		}

		$result = openssl_decrypt(
			substr( $raw, 16 ),
			'AES-256-CBC',
			$this->key(),
			OPENSSL_RAW_DATA,
			substr( $raw, 0, 16 )
		);

		return false === $result ? null : $result;
	}
}
