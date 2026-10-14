<?php
/**
 * Core-owned module license storage and administration.
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Module_Licenses {
	private const OPTION = 'amanah_seo_module_license_secrets';
	private const SOURCES_OPTION = 'amanah_seo_module_license_sources';
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
			$updated = update_option( self::OPTION, $stored, false );
			if ( ! $updated && get_option( self::OPTION, array() ) !== $stored ) {
				return false;
			}
			if ( 'ai' === $module ) {
				$sources = get_option( self::SOURCES_OPTION, array() );
				$sources = is_array( $sources ) ? $sources : array();
				$sources['ai'] = 'explicit';
				$source_updated = update_option( self::SOURCES_OPTION, $sources, false );
				if ( ! $source_updated && get_option( self::SOURCES_OPTION, array() ) !== $sources ) {
					return false;
				}
			}
			return true;
		} catch ( Throwable $exception ) {
			error_log( '[AMANAH SEO] Module license storage failed: ' . $exception->getMessage() );
			return false;
		}
	}

	/**
	 * Check a module entitlement from the last validated licensing-service response.
	 * Fail closed for missing, malformed, stale-status, or non-active records.
	 */
	public static function has_entitlement( string $module, string $entitlement ): bool {
		if ( ! self::valid_module( $module ) || '' === trim( $entitlement ) ) {
			return false;
		}
		$statuses = get_option( 'amanah_seo_module_license_statuses', array() );
		if ( ! is_array( $statuses ) || ! isset( $statuses[ $module ] ) || ! is_array( $statuses[ $module ] ) ) {
			return false;
		}
		$record = $statuses[ $module ];
		if ( ! isset( $record['status'], $record['entitlements'] ) || ! in_array( $record['status'], array( 'active', 'grace' ), true ) || ! is_array( $record['entitlements'] ) ) {
			return false;
		}
		foreach ( $record['entitlements'] as $granted ) {
			if ( ! is_string( $granted ) || '' === trim( $granted ) ) {
				return false;
			}
		}
		if ( ! in_array( sanitize_key( $entitlement ), array_map( 'sanitize_key', $record['entitlements'] ), true ) ) {
			return false;
		}
		$now = time();
		$expires_at = isset( $record['expires_at'] ) && is_string( $record['expires_at'] ) ? trim( $record['expires_at'] ) : '';
		if ( '' !== $expires_at && 'LIFETIME' !== strtoupper( $expires_at ) ) {
			$expires_timestamp = strtotime( $expires_at . ' UTC' );
			if ( false === $expires_timestamp || $expires_timestamp < $now ) {
				return false;
			}
		}
		if ( 'grace' === $record['status'] ) {
			$grace_until = isset( $record['grace_until'] ) && is_string( $record['grace_until'] ) ? trim( $record['grace_until'] ) : '';
			$grace_timestamp = '' !== $grace_until ? strtotime( $grace_until . ' UTC' ) : false;
			if ( false === $grace_timestamp || $grace_timestamp < $now ) {
				return false;
			}
		}
		$validated_at = isset( $record['validated_at'] ) && is_string( $record['validated_at'] ) ? strtotime( $record['validated_at'] ) : false;
		if ( false === $validated_at || ( $now - $validated_at ) >= DAY_IN_SECONDS ) {
			if ( 'ai' !== $module ) {
				return false;
			}
			$key = self::get_license( 'ai' );
			if ( ! is_string( $key ) || '' === $key ) {
				return false;
			}
			$refreshed = self::activate_ai_license( $key, true );
			$refreshed = self::apply_ai_grace_fallback( $refreshed, $record );
			self::save_license_status( $module, $refreshed );
			if ( ! in_array( $refreshed['status'] ?? '', array( 'active', 'grace' ), true ) || ! is_array( $refreshed['entitlements'] ?? null ) || ! in_array( sanitize_key( $entitlement ), array_map( 'sanitize_key', array_filter( $refreshed['entitlements'], 'is_string' ) ), true ) ) {
				return false;
			}
			$refreshed_expiry = isset( $refreshed['expires_at'] ) && is_string( $refreshed['expires_at'] ) ? trim( $refreshed['expires_at'] ) : '';
			if ( '' !== $refreshed_expiry && 'LIFETIME' !== strtoupper( $refreshed_expiry ) ) {
				$refreshed_expiry_timestamp = strtotime( $refreshed_expiry . ' UTC' );
				if ( false === $refreshed_expiry_timestamp || $refreshed_expiry_timestamp < $now ) {
					return false;
				}
			}
			if ( 'grace' === ( $refreshed['status'] ?? '' ) ) {
				$until = isset( $refreshed['grace_until'] ) ? strtotime( (string) $refreshed['grace_until'] ) : false;
				if ( false === $until || $until < $now ) {
					return false;
				}
			}
		}
		return true;
	}

	private static function apply_ai_grace_fallback( array $status, array $previous ): array {
		if ( ! in_array( $status['status'] ?? '', array( 'unknown' ), true ) || ! in_array( $status['reason'] ?? '', array( 'service_unavailable', 'service_error' ), true ) ) {
			return $status;
		}
		if ( ! in_array( $previous['status'] ?? '', array( 'active', 'grace' ), true ) || ! is_array( $previous['entitlements'] ?? null ) ) {
			return $status;
		}
		$last_validated = isset( $previous['validated_at'] ) && is_string( $previous['validated_at'] ) ? strtotime( $previous['validated_at'] ) : false;
		if ( false === $last_validated || time() - $last_validated > 604800 || empty( $previous['entitlements'] ) ) {
			return $status;
		}
		$grace_until = gmdate( 'c', $last_validated + 604800 );
		if ( strtotime( $grace_until ) < time() ) {
			return $status;
		}
		return array_merge(
			$previous,
			array(
				'status'      => 'grace',
				'grace_until' => $grace_until,
				'reason'      => $status['reason'],
			)
		);
	}

	public static function get_license( string $module ): ?string {
		if ( ! self::valid_module( $module ) ) {
			return null;
		}
		$stored = get_option( self::OPTION, array() );
		if ( is_array( $stored ) && isset( $stored[ $module ] ) ) {
			$plain = self::decrypt( $stored[ $module ] );
			if ( null !== $plain ) {
				// Older releases may already have silently imported a legacy AI key.
				// If the legacy row still exists and no explicit-save marker exists,
				// preserve the secret but do not treat it as configured or entitled.
				if ( 'ai' === $module ) {
					$sources = get_option( self::SOURCES_OPTION, array() );
					$explicitly_saved = is_array( $sources ) && isset( $sources['ai'] ) && 'explicit' === $sources['ai'];
					if ( ! $explicitly_saved && self::legacy_ai_license_record_exists() ) {
						return null;
					}
				}
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

		// Legacy AI license records are detected separately and are never imported
		// implicitly. The user must provide an official key through Settings & Access.
		return null;
	}

	/**
	 * Detect a legacy AI license row without reading its secret or changing data.
	 *
	 * @return bool True when a legacy record exists for the current user.
	 */
	public static function legacy_ai_license_record_exists(): bool {
		global $wpdb;
		if ( ! isset( $wpdb ) || ! is_object( $wpdb ) || ! method_exists( $wpdb, 'get_var' ) || ! method_exists( $wpdb, 'prepare' ) || ! isset( $wpdb->prefix ) ) {
			return false;
		}
		$table = $wpdb->prefix . 'amanah_license';
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
		if ( $table !== $table_exists ) {
			return false;
		}
		$legacy_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM {$table} WHERE user_id = %d ORDER BY id DESC LIMIT 1",
			get_current_user_id()
		) );
		return null !== $legacy_id && false !== $legacy_id && '' !== (string) $legacy_id;
	}

	public static function delete_license( string $module ): bool {
		if ( ! self::valid_module( $module ) ) {
			return false;
		}
		$stored = get_option( self::OPTION, array() );
		$stored = is_array( $stored ) ? $stored : array();
		$had_license = isset( $stored[ $module ] );
		unset( $stored[ $module ] );
		$updated = update_option( self::OPTION, $stored, false );
		return true === $updated || ! $had_license;
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
		$license_statuses = get_option( 'amanah_seo_module_license_statuses', array() );
		$license_statuses = is_array( $license_statuses ) ? $license_statuses : array();
		foreach ( array( 'ai' => 'AMANAH SEO AI', 'engine' => 'AMANAH SEO ENGINE' ) as $module => $label ) {
			$configured = null !== self::get_license( $module );
			$legacy_ai_record = ! $configured && 'ai' === $module && self::legacy_ai_license_record_exists();
			$status = isset( $license_statuses[ $module ]['status'] ) ? sanitize_key( $license_statuses[ $module ]['status'] ) : 'not_checked';
			if ( ! $configured ) {
				$status = $legacy_ai_record ? 'legacy_detected_not_imported' : 'not_configured';
			}
			echo '<div class="card" style="max-width:900px;margin:20px 0;padding:20px">';
			echo '<h2>' . esc_html( $label ) . '</h2>';
			echo '<p>' . esc_html( $configured ? 'License key is stored.' : 'No license key is stored.' ) . '</p>';
			if ( $legacy_ai_record ) {
				echo '<p>' . esc_html__( 'A legacy AI license record exists, but it has not been imported. Enter an official license key to configure AI access. The existing record is preserved.', 'amanah-seo' ) . '</p>';
			}
			echo '<p>' . esc_html( 'License service status: ' . $status ) . '</p>';
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

	private static function activate_ai_license( string $license_key, bool $validate_existing = false ): array {
		$base_url = 'https://api.amanahseoai.my.id';
		$endpoint_path = $validate_existing ? '/v1/validate' : '/v1/activate';
		$endpoint = $base_url . $endpoint_path;
		$site_id = get_option( 'amanah_seo_ai_license_site_id', '' );
		if ( ! is_string( $site_id ) || ! preg_match( '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $site_id ) ) {
			$site_id = wp_generate_uuid4();
			update_option( 'amanah_seo_ai_license_site_id', $site_id, false );
		}
		$site_id = strtolower( $site_id );
		$domain = strtolower( trim( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) );
		if ( '' === $domain ) {
			return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_site_domain' );
		}

		$request_id = wp_generate_uuid4();
		$timestamp = time();
		$signature_payload = $request_id . "\n" . $timestamp . "\n" . strtoupper( $endpoint_path ) . "\n" . $site_id;
		$signature = hash_hmac( 'sha256', $signature_payload, $license_key );
		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout'     => 15,
				'redirection' => 0,
				'headers'     => array( 'Content-Type' => 'application/json' ),
				'body'        => wp_json_encode(
					array(
						'request_id' => $request_id,
						'timestamp'  => $timestamp,
						'license_key' => $license_key,
						'signature'  => $signature,
						'product_id' => 'amanah-seo-ai',
						'site_id'    => $site_id,
						'domain'     => $domain,
					)
				),
			)
		);
		if ( is_wp_error( $response ) ) {
			return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'service_unavailable' );
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) ) {
			return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_response' );
		}
		if ( ! array_key_exists( 'success', $body ) || ! is_bool( $body['success'] ) ) {
			return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_response' );
		}
		if ( false === $body['success'] ) {
			$error = isset( $body['error'] ) && is_array( $body['error'] ) ? $body['error'] : array();
			$error_code = isset( $error['code'] ) && is_string( $error['code'] ) ? strtoupper( sanitize_key( $error['code'] ) ) : 'SERVER_ERROR';
			$statuses = array(
				'LICENSE_NOT_FOUND' => 'invalid',
				'LICENSE_INVALID' => 'invalid',
				'LICENSE_EXPIRED' => 'expired',
				'LICENSE_SUSPENDED' => 'suspended',
				'LICENSE_REVOKED' => 'revoked',
				'PRODUCT_MISMATCH' => 'invalid',
				'ACTIVATION_LIMIT_REACHED' => 'invalid',
				'SITE_NOT_FOUND' => 'invalid',
				'SITE_MISMATCH' => 'invalid',
				'AUTHENTICATION_FAILED' => 'invalid',
			);
			$known_license_error = in_array( $error_code, array_keys( $statuses ), true );
			if ( $known_license_error ) {
				$reason = 'license_rejected';
			} elseif ( 0 === $code ) {
				$reason = 'service_unavailable';
			} elseif ( $code >= 500 && $code < 600 ) {
				$reason = 'service_error';
			} else {
				// Unknown client-side/service responses are not evidence of a transient
				// outage and must never trigger the cached-entitlement grace fallback.
				$reason = 'invalid_response';
			}
			return array(
				'status' => $statuses[ $error_code ] ?? 'unknown',
				'entitlements' => array(),
				'reason' => $reason,
			);
		}
		if ( $code < 200 || $code >= 300 || ! isset( $body['data'] ) || ! is_array( $body['data'] ) ) {
			return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_response' );
		}

		$data = $body['data'];
		if ( ! isset( $data['status'], $data['entitlements'] ) || ! is_string( $data['status'] ) || ! is_array( $data['entitlements'] ) ) {
			return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_response' );
		}
		$service_status = strtoupper( $data['status'] );
		$allowed_statuses = array( 'ACTIVE', 'GRACE', 'NOT_ENTITLED', 'EXPIRED', 'REVOKED', 'SUSPENDED', 'INVALID', 'UNCONFIGURED', 'UNKNOWN' );
		if ( ! in_array( $service_status, $allowed_statuses, true ) ) {
			return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_response' );
		}
		foreach ( $data['entitlements'] as $entitlement ) {
			if ( ! is_string( $entitlement ) || '' === trim( $entitlement ) ) {
				return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_response' );
			}
		}
		$entitlements = array_values( array_unique( array_map( 'sanitize_key', $data['entitlements'] ) ) );
		if ( in_array( $service_status, array( 'ACTIVE', 'GRACE' ), true ) && empty( $entitlements ) ) {
			return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_response' );
		}
		$validated_at = gmdate( 'c' );
		$grace_until = isset( $data['grace_until'] ) && is_string( $data['grace_until'] ) ? sanitize_text_field( $data['grace_until'] ) : '';
		if ( 'GRACE' === $service_status ) {
			$grace_timestamp = '' !== $grace_until ? strtotime( $grace_until ) : false;
			if ( false === $grace_timestamp || $grace_timestamp < time() ) {
				return array( 'status' => 'unknown', 'entitlements' => array(), 'reason' => 'invalid_response' );
			}
		}
		return array(
			'status'       => strtolower( $service_status ),
			'entitlements' => $entitlements,
			'validated_at' => $validated_at,
			'expires_at'   => isset( $data['expires_at'] ) && is_string( $data['expires_at'] ) ? sanitize_text_field( $data['expires_at'] ) : '',
			'grace_until'  => $grace_until,
			'plan'         => isset( $data['plan'] ) && is_string( $data['plan'] ) ? sanitize_text_field( $data['plan'] ) : '',
			'reason'       => '',
		);
	}
	private static function save_license_status( string $module, array $status ): void {
		$all = get_option( 'amanah_seo_module_license_statuses', array() );
		$all = is_array( $all ) ? $all : array();
		$all[ $module ] = $status;
		update_option( 'amanah_seo_module_license_statuses', $all, false );
	}

	private static function store_and_activate_ai_license( string $license_key, ?string $existing_license_key = null ): bool {
		if ( ! self::set_license( 'ai', $license_key ) ) {
			return false;
		}
		$same_license_key = is_string( $existing_license_key ) && '' !== $existing_license_key && hash_equals( $existing_license_key, $license_key );
		$status = self::activate_ai_license( $license_key, $same_license_key );
		$previous_statuses = get_option( 'amanah_seo_module_license_statuses', array() );
		$previous_ai_status = is_array( $previous_statuses ) && isset( $previous_statuses['ai'] ) && is_array( $previous_statuses['ai'] ) ? $previous_statuses['ai'] : array();
		if ( $same_license_key ) {
			$status = self::apply_ai_grace_fallback( $status, $previous_ai_status );
		}
		self::save_license_status( 'ai', $status );
		if ( 'unknown' === ( $status['status'] ?? '' ) ) {
			error_log( '[AMANAH SEO] AI license activation could not be verified: ' . ( $status['reason'] ?? 'unknown' ) );
		}
		return true;
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
		$existing_license_key = self::get_license( $module );
		if ( 'ai' === $module ) {
			if ( ! self::store_and_activate_ai_license( $license_key, $existing_license_key ) ) {
				wp_safe_redirect( add_query_arg( array( 'page' => 'amanah-seo-access', 'license_error' => '1' ), admin_url( 'admin.php' ) ) );
				exit;
			}
		} elseif ( ! self::set_license( $module, $license_key ) ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'amanah-seo-access', 'license_error' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}
		if ( 'ai' !== $module ) {
			try {
				$services = \Amanah\SEOEngine\Core\Bootstrap\Plugin::boot();
				$state = $services->get( 'license_client' )->activate( $license_key );
				self::save_license_status( 'engine', array( 'status' => $state->getStatus(), 'entitlements' => $state->getEntitlements() ) );
			} catch ( \Throwable $exception ) {
				self::save_license_status( 'engine', array( 'status' => 'unknown', 'entitlements' => array() ) );
				error_log( '[AMANAH SEO] ENGINE license activation deferred: ' . $exception->getMessage() );
			}
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'amanah-seo-access', 'license_saved' => $module ), admin_url( 'admin.php' ) ) );
		exit;
	}
}
