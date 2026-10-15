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
		$had_license = isset( $stored[ $module ] );
		unset( $stored[ $module ] );
		$updated = update_option( self::OPTION, $stored, false );
		return true === $updated || ! $had_license;
	}

	public static function render() {
		if ( ! current_user_can( 'manage_amanah_seo' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage AMANAH SEO module access.', 'amanah-seo' ), '', array( 'response' => 403 ) );
		}

		echo '<div class="wrap amanah-seo-access-page">';
		echo '<style>
			.amanah-seo-access-page{max-width:none;box-sizing:border-box}
			.amanah-seo-access-page .amanah-seo-access-copy{box-sizing:border-box;display:block;width:100%;max-width:1200px;height:auto;max-height:none;overflow:visible;white-space:normal;overflow-wrap:anywhere;margin:16px 0 26px;padding:28px 32px;background:#eef6ff;border:1px solid #c6def5;border-left:5px solid #2271b1;border-radius:8px;line-height:1.7;box-shadow:0 4px 16px rgba(28,75,120,.07)}
			.amanah-seo-access-page .amanah-seo-access-copy h2{font-size:clamp(22px,2.2vw,30px);line-height:1.25;margin:0 0 14px;color:#123b61}
			.amanah-seo-access-page .amanah-seo-access-copy p{font-size:15px;line-height:1.75;margin:0 0 13px}
			.amanah-seo-access-page .amanah-seo-access-copy .amanah-seo-access-hook{font-size:17px;font-weight:600;color:#174a75}
			.amanah-seo-access-page .amanah-seo-access-copy .amanah-seo-access-close{margin:18px 0 0;padding-top:15px;border-top:1px solid #c6def5;color:#123b61;font-weight:700}
			.amanah-seo-access-page .amanah-seo-access-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:20px;align-items:start;width:100%}
			.amanah-seo-access-page .amanah-seo-access-column{box-sizing:border-box;min-width:0;padding:20px;background:#fff;border:1px solid #dcdcde;border-radius:5px}
			.amanah-seo-access-page .amanah-seo-access-column h2:first-child{margin-top:0}
			.amanah-seo-access-page .amanah-seo-access-column .card{box-sizing:border-box;width:100%;max-width:none;margin:16px 0;padding:16px}
			.amanah-seo-access-page .amanah-seo-access-ai form{max-width:100%}
			.amanah-seo-access-page .amanah-seo-access-ai .form-table th{width:32%;padding-left:0}
			.amanah-seo-access-page .amanah-seo-access-ai .form-table td{padding-right:0}
			.amanah-seo-access-page .amanah-seo-access-ai input.regular-text{max-width:100%;width:100%}
			.amanah-seo-access-page .amanah-seo-license-label{display:block;margin:14px 0 6px;font-weight:600}
			.amanah-seo-access-page .amanah-seo-license-state{margin:0 0 8px;color:#50575e}
			.amanah-seo-access-page .amanah-seo-license-card .regular-text{width:100%;max-width:100%;box-sizing:border-box;margin:0 0 12px}
			@media(max-width:1000px){.amanah-seo-access-page .amanah-seo-access-grid{grid-template-columns:minmax(0,1fr)}}
			@media(max-width:782px){.amanah-seo-access-page .amanah-seo-access-copy{padding:22px 20px}.amanah-seo-access-page .amanah-seo-access-column{padding:16px}}
		</style>';

		echo '<section class="amanah-seo-access-copy" aria-labelledby="amanah-seo-access-copy-title">';
		echo '<h2 id="amanah-seo-access-copy-title">' . esc_html__( '🚀 Apakah Bisnis Anda Sudah Siap Ditemukan oleh AI?', 'amanah-seo' ) . '</h2>';
		echo '<p class="amanah-seo-access-hook">' . esc_html__( 'Calon pelanggan Anda sedang mencari jawaban. Apakah bisnis Anda akan muncul sebagai pilihan mereka, atau justru terlewatkan?', 'amanah-seo' ) . '</p>';
		echo '<p>' . esc_html__( 'Dunia pencarian sedang berubah. Kini, bisnis tidak cukup hanya memiliki website dan konten. Mesin pencari serta sistem AI juga perlu memahami siapa Anda, apa keahlian Anda, dan mengapa bisnis Anda layak dipercaya.', 'amanah-seo' ) . '</p>';
		echo '<p>' . esc_html__( 'Tanpa fondasi identitas bisnis yang jelas, informasi yang terarah, dan konten yang relevan, peluang untuk ditemukan oleh calon pelanggan bisa terlewatkan.', 'amanah-seo' ) . '</p>';
		echo '<p><strong>' . esc_html__( 'Kenalkan bisnis Anda dengan lebih terarah bersama AMANAH SEO.', 'amanah-seo' ) . '</strong></p>';
		echo '<p>' . esc_html__( 'AMANAH SEO menyatukan pengelolaan lisensi modul dan konfigurasi AI dalam satu dashboard. Kelola akses AI dan ENGINE, atur penyedia serta model AI, dan bangun fondasi SEO bisnis Anda secara lebih terstruktur—semuanya dari satu tempat.', 'amanah-seo' ) . '</p>';
		echo '<p class="amanah-seo-access-close">' . esc_html__( 'Mulai dari fondasi yang tepat. Persiapkan bisnis Anda untuk menghadapi evolusi pencarian.', 'amanah-seo' ) . '</p>';
		echo '</section>';

		if ( isset( $_GET['license_saved'] ) && in_array( sanitize_key( wp_unslash( $_GET['license_saved'] ) ), array( 'ai', 'engine' ), true ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Module license key stored securely. Entitlement status depends on the configured licensing service.', 'amanah-seo' ) . '</p></div>';
		}

		$license_statuses = get_option( 'amanah_seo_module_license_statuses', array() );
		$license_statuses = is_array( $license_statuses ) ? $license_statuses : array();

		echo '<div class="amanah-seo-access-grid">';
		echo '<section class="amanah-seo-access-column amanah-seo-access-licenses" aria-labelledby="amanah-seo-access-licenses-title">';
		echo '<h2 id="amanah-seo-access-licenses-title">' . esc_html__( 'Settings & Access', 'amanah-seo' ) . '</h2>';
		echo '<p>' . esc_html__( 'Kelola lisensi AMANAH SEO AI dan AMANAH SEO ENGINE. Kunci disimpan terenkripsi dan tidak ditampilkan kembali setelah disimpan.', 'amanah-seo' ) . '</p>';

		foreach ( array( 'ai' => 'AMANAH SEO AI', 'engine' => 'AMANAH SEO ENGINE' ) as $module => $label ) {
			$configured = null !== self::get_license( $module );
			$status = isset( $license_statuses[ $module ]['status'] ) ? sanitize_key( $license_statuses[ $module ]['status'] ) : 'not_checked';
			echo '<div class="card amanah-seo-license-card">';
			echo '<h3>' . esc_html( $label ) . '</h3>';
			echo '<p class="amanah-seo-license-state">' . esc_html( $configured ? 'License key is stored.' : 'No license key is stored.' ) . '</p>';
			echo '<p class="amanah-seo-license-state">' . esc_html( 'License service status: ' . $status ) . '</p>';
			echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
			echo '<input type="hidden" name="action" value="amanah_seo_save_module_license">';
			echo '<input type="hidden" name="module" value="' . esc_attr( $module ) . '">';
			wp_nonce_field( 'amanah_seo_save_module_license_' . $module );
			echo '<label class="amanah-seo-license-label" for="amanah-seo-license-' . esc_attr( $module ) . '">' . esc_html__( 'License Key', 'amanah-seo' ) . '</label>';
			echo '<input class="regular-text" type="password" autocomplete="new-password" required maxlength="512" id="amanah-seo-license-' . esc_attr( $module ) . '" name="license_key" value="">';
			submit_button( __( 'Save License Key', 'amanah-seo' ), 'primary', 'submit', false );
			echo '</form></div>';
		}
		echo '</section>';

		echo '<section class="amanah-seo-access-column amanah-seo-access-ai" aria-labelledby="amanah-seo-access-ai-title">';
		echo '<h2 id="amanah-seo-access-ai-title">' . esc_html__( 'AI Provider & Model', 'amanah-seo' ) . '</h2>';
		echo '<p>' . esc_html__( 'Atur provider AI, model, dan API key dari satu halaman AMANAH SEO. API key yang sudah tersimpan tidak ditampilkan kembali.', 'amanah-seo' ) . '</p>';
		if ( class_exists( 'AMANAH_SEO_AI_AI_Settings' ) && class_exists( 'AMANAH_SEO_AI_AI_Credentials' ) && class_exists( 'AMANAH_SEO_AI_AI_Model_Catalog' ) && class_exists( 'AMANAH_SEO_AI_AI_Settings_Controller' ) ) {
			$ai_settings = new AMANAH_SEO_AI_AI_Settings();
			$ai_credentials = new AMANAH_SEO_AI_AI_Credentials( $ai_settings );
			$ai_catalog = new AMANAH_SEO_AI_AI_Model_Catalog();
			$ai_controller = new AMANAH_SEO_AI_AI_Settings_Controller( $ai_settings, $ai_credentials, $ai_catalog );
			$ai_controller->render();
		} else {
			echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'Komponen pengaturan AI belum tersedia pada runtime plugin ini.', 'amanah-seo' ) . '</p></div>';
		}
		echo '</section></div></div>';
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
