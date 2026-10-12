<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\Security\SecretFieldPolicy;
use PHPUnit\Framework\TestCase;

final class Phase11HardeningTest extends TestCase {

	public function test_secret_policy_covers_normalized_sensitive_variants(): void {
		$this->assertTrue( SecretFieldPolicy::isSensitiveKey( 'api_key' ) );
		$this->assertTrue( SecretFieldPolicy::isSensitiveKey( 'api-key' ) );
		$this->assertTrue( SecretFieldPolicy::isSensitiveKey( 'APIKEY' ) );
		$this->assertTrue( SecretFieldPolicy::isSensitiveKey( 'private-key' ) );
		$this->assertTrue( SecretFieldPolicy::isSensitiveKey( 'license key' ) );
		$this->assertFalse( SecretFieldPolicy::isSensitiveKey( 'provider_id' ) );
	}

	public function test_http_client_uses_safe_wordpress_transport(): void {
		$source = file_get_contents( __DIR__ . '/../../plugin/Infrastructure/Http/WordPressHttpClient.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( 'wp_safe_remote_request', $source );
		$this->assertStringNotContainsString( 'wp_remote_request( esc_url_raw', $source );
	}

	public function test_secret_store_uses_authenticated_gcm_and_keeps_legacy_read_support(): void {
		$source = file_get_contents( __DIR__ . '/../../plugin/Infrastructure/AI/SecretStore.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( "aes-256-gcm", $source );
		$this->assertStringContainsString( 'GCM_VERSION', $source );
		$this->assertStringContainsString( 'decryptLegacyCbc', $source );
	}

	public function test_portability_sanitizer_uses_shared_secret_policy(): void {
		$source = file_get_contents( __DIR__ . '/../../plugin/Application/Portability/SecretSanitizer.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( 'SecretFieldPolicy::isSensitiveKey', $source );
	}

	public function test_job_payload_reuses_shared_secret_policy(): void {
		$source = file_get_contents( __DIR__ . '/../../plugin/Application/Job/JobManager.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( 'SecretFieldPolicy::isSensitiveKey', $source );
	}

	public function test_admin_screen_labels_use_translation_boundary(): void {
		$source = file_get_contents( __DIR__ . '/../../plugin/Presentation/Admin/AdminScreenRegistry.php' );

		$this->assertIsString( $source );
		$this->assertSame( 20, substr_count( $source, "__( '" ) );
	}

	public function test_uninstall_removes_plugin_credentials_and_portability_operations(): void {
		$source = file_get_contents( __DIR__ . '/../../plugin/Infrastructure/WordPress/Lifecycle/WordPressLifecycle.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( "'amanah_seo_portability_operations'", $source );
		$this->assertStringContainsString( "delete_option( 'amanah_seo_ai_credentials' )", $source );
	}
}
