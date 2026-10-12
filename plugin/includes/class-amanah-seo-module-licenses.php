<?php
/**
 * Core-owned module license storage and administration.
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Module_Licenses {
	private const OPTION = 'amanah_seo_module_license_secrets';
	private const CIPHER = 'aes-256-gcm';

	public static function register() {
		add_action( 'admin_post_amanah_seo_save_module_license', array( __CLASS__, 'save' ) );
	}

	private static function valid_module( $module ): bool {
		return in_array( $module, array( 'ai', 'engine' ), true );
	}

	private static function encryption_key(): string {
		return hash( 'sha256', defined( 'AUTH_KEY' ) ? AUTH_KEY : 'amanah-seo-engine', true );
	}

	private static function encrypt( string $secret ): string {
		$iv = random_bytes( openssl_cipher_iv_length( self::CIPHER ) );
		$tag = '';
		$ciphertext = openssl_encrypt( $secret, self::CIPHER, self::encryption_key(), OPENSSL_RAW_DATA, $iv, $tag, '', 16 );
		if ( false === $ciphertext ) {
			throw new RuntimeException( 'module_license_encryption_failed' );
		}
		return 'v1:' . base64_encode( $iv . $tag . $ciphertext );
	}

	private static function decrypt( $stored ): ?string {
		if ( ! is_string( $stored ) || 0 !== strpos( $stored, 'v1:' ) ) {
			return null;
		}
		$raw = base64_decode( substr( $stored, 3 ), true );
		$iv_length = openssl_cipher_iv_length( self::CIPHER );
		if ( false === $raw || strlen( $raw ) < $iv_length + 16 ) {
			return null;
		}
		$iv = substr( $raw, 0, $iv_length );
		$tag = substr( $raw, $iv_length, 16 );
		$ciphertext = substr( $raw, $iv_length + 16 );
		$plain = openssl_decrypt( $ciphertext, self::CIPHER, self::encryption_key(), OPENSSL_RAW_DATA, $iv, $tag );
		return false === $plain ? null : $plain;
	}

	public static function set_license( string $module, string $license_key ): bool {
		if ( ! self::valid_module( $module ) || '' === trim( $license_key ) || strlen( $license_key ) > 512 ) {
			return false;
		}
		try {
			$stored = get_option( self::OPTION, array() );
			$stored = is_array( $stored ) ? $stored : array();
			$stored[ $module ] = self::encrypt( trim( $license_key ) );
			return update_option( self::OPTION, $stored, false );
		} catch ( Throwable $exception ) {
			error_log( '[AMANAH SEO] Module license storage failed: ' . $exception->getMessage() );
			return false;
		}
	}

	public static function get_license( string $module ): ?string {
		if ( ! self::valid_module( $module ) ) {
			return null;
		}
		$stored = get_option( self::OPTION, array() );
		if ( is_array( $stored ) && isset( $stored[ $module ] ) ) {
			$plain = self::decrypt( $stored[ $module ] );
			if ( null !== $plain ) {
				return $plain;
			}
		}

		// Read the historical ENGINE secret option without deleting or rewriting it.
		if ( 'ai' === $module ) {
			$status = self::activate_ai_license( $license_key );
			self::save_license_status( 'ai', $status );
			if ( 'unknown' === $status['status'] ) {
				error_log( '[AMANAH SEO] AI license activation could not be verified: ' . ( $status['reason'] ?? 'unknown' ) );
			}
		} else {
			try {
				$services = \\Amanah\\SEOEngine\\Core\\Bootstrap\\Plugin::boot();
				$state = $services->get( 'license_client' )->activate( $license_key );
				self::save_license_status( 'engine', array( 'status' => $state->getStatus(), 'entitlements' => $state->getEntitlements() ) );
			} catch ( \\Throwable $exception ) {
				self::save_license_status( 'engine', array( 'status' => 'unknown', 'entitlements' => array() ) );
				error_log( '[AMANAH SEO] ENGINE license activation deferred: ' . $exception->getMessage() );
			}
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'amanah-seo-access', 'license_saved' => $module ), admin_url( 'admin.php' ) ) );
		exit;
	}
}
