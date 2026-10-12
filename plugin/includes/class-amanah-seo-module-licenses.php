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
		if ( 'engine' === $module ) {
			$legacy = self::decrypt( get_option( 'amanah_seo_license_secret', '' ) );
			if ( null !== $legacy ) {
				return $legacy;
			}
		}

		// Migrate a legacy AI license record into encrypted Core storage, retaining
		// the original row for compatibility and recovery.
		if ( 'ai' === $module ) {
			global $wpdb;
			if ( isset( $wpdb ) && is_object( $wpdb ) && method_exists( $wpdb, 'get_var' ) && method_exists( $wpdb, 'prepare' ) ) {
				$table = $wpdb->prefix . 'amanah_license';
				$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
				if ( $table === $table_exists ) {
					$legacy = $wpdb->get_var( $wpdb->prepare(
						"SELECT license_key FROM {$table} WHERE user_id = %d ORDER BY id DESC LIMIT 1",
						get_current_user_id()
					) );
					if ( is_string( $legacy ) && '' !== $legacy && self::set_license( 'ai', $legacy ) ) {
						return $legacy;
					}
				}
			}
		}
		return null;
	}

	public static function delete_license( string $module ): bool {
		if ( ! self::valid_module( $module ) ) {
			return false;
		}
		$stored = get_option( self::OPTION, array() );
		$stored = is_array( $stored ) ? $stored : array();
		unset( $stored[ $module ] );
		$updated = update_option( self::OPTION, $stored, false );
		return true === $updated || ! isset( $stored[ $module ] );
	}

	public static function render() {
		if ( ! current_user_can( 'manage_amanah_seo' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage AMANAH SEO module access.', 'amanah-seo' ), '', array( 'response' => 403 ) );
		}
		echo '<div class="wrap"><h1>' . esc_html__( 'Settings & Access', 'amanah-seo' ) . '</h1>';
		echo '<p>' . esc_html__( 'Manage the separate AMANAH SEO AI and ENGINE module license keys here. Keys are stored encrypted and are never displayed after saving.', 'amanah-seo' ) . '</p>';
		if ( isset( $_GET['license_saved'] ) && in_array( sanitize_key( wp_unslash( $_GET['license_saved'] ) ), array( 'ai', 'engine' ), true ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Module license key stored securely. Entitlement status depends on the configured licensing service.', 'amanah-seo' ) . '</p></div>';
		}
		foreach ( array( 'ai' => 'AMANAH SEO AI', 'engine' => 'AMANAH SEO ENGINE' ) as $module => $label ) {
			$configured = null !== self::get_license( $module );
			echo '<div class="card" style="max-width:900px;margin:20px 0;padding:20px">';
			echo '<h2>' . esc_html( $label ) . '</h2>';
			echo '<p>' . esc_html( $configured ? 'License key is stored.' : 'No license key is stored.' ) . '</p>';
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
			echo '<input type="hidden" name="action" value="amanah_seo_save_module_license">';
			echo '<input type="hidden" name="module" value="' . esc_attr( $module ) . '">';
			wp_nonce_field( 'amanah_seo_save_module_license_' . $module );
			echo '<label for="amanah-seo-license-' . esc_attr( $module ) . '">' . esc_html__( 'License Key', 'amanah-seo' ) . '</label><br>';
			echo '<input class="regular-text" type="password" autocomplete="new-password" required maxlength="512" id="amanah-seo-license-' . esc_attr( $module ) . '" name="license_key" value=""> ';
			submit_button( __( 'Save License Key', 'amanah-seo' ), 'primary', 'submit', false );
			echo '</form></div>';
		}
		echo '</div>';
	}

	public static function save() {
		if ( ! current_user_can( 'manage_amanah_seo' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage AMANAH SEO module access.', 'amanah-seo' ), '', array( 'response' => 403 ) );
		}
		$module = isset( $_POST['module'] ) ? sanitize_key( wp_unslash( $_POST['module'] ) ) : '';
		if ( ! self::valid_module( $module ) ) {
			wp_die( esc_html__( 'Invalid module selection.', 'amanah-seo' ), '', array( 'response' => 400 ) );
		}
		check_admin_referer( 'amanah_seo_save_module_license_' . $module );
		$license_key = isset( $_POST['license_key'] ) ? trim( (string) wp_unslash( $_POST['license_key'] ) ) : '';
		if ( ! self::set_license( $module, $license_key ) ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'amanah-seo-access', 'license_error' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'amanah-seo-access', 'license_saved' => $module ), admin_url( 'admin.php' ) ) );
		exit;
	}
}
