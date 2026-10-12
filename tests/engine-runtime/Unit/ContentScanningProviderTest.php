<?php

declare( strict_types=1 );

use PHPUnit\Framework\TestCase;

final class ContentScanningProviderTest extends TestCase {

	public function test_legacy_serp_dependency_is_removed_from_content_scanning(): void {
		$root = dirname( __DIR__, 2 );
		$this->assertFileDoesNotExist( $root . '/plugin/Infrastructure/Serp/DataForSeoSerpProvider.php' );

		foreach (
			array(
				'/plugin/Infrastructure/Serp/GoogleSearchSerpProvider.php',
				'/plugin/Application/UseCase/RunContentScanning.php',
				'/plugin/Presentation/Admin/ContentScanningAdmin.php',
				'/plugin/Presentation/Admin/AdminMenu.php',
				'/plugin/Core/Bootstrap/Plugin.php',
			) as $path
		) {
			$full = $root . $path;
			$this->assertFileExists( $full );
			$content = (string) file_get_contents( $full );
			$this->assertStringNotContainsString( 'DataForSEO', $content, $path );
			$this->assertStringNotContainsString( 'dataforseo', $content, $path );
		}
	}

	public function test_google_search_provider_is_registered_and_uses_existing_gemini_credentials(): void {
		$root = dirname( __DIR__, 2 );
		$plugin = (string) file_get_contents( $root . '/plugin/Core/Bootstrap/Plugin.php' );
		$run = (string) file_get_contents( $root . '/plugin/Application/UseCase/RunContentScanning.php' );

		$this->assertStringContainsString( 'GoogleSearchSerpProvider', $plugin );
		$this->assertStringContainsString( '$this->secrets->get(\'gemini\')', $run );
		$this->assertStringContainsString( "'api_key'=>$gemini_secret", $run );
	}

	public function test_google_search_provider_uses_interactions_api_and_citations(): void {
		$provider = (string) file_get_contents(
			dirname( __DIR__, 2 ) . '/plugin/Infrastructure/Serp/GoogleSearchSerpProvider.php'
		);

		$this->assertStringContainsString( "private const MODEL = 'gemini-3.5-flash-lite';", $provider );
		$this->assertStringContainsString( 'https://generativelanguage.googleapis.com/v1beta/interactions', $provider );
		$this->assertStringContainsString( "'type' => 'google_search'", $provider );
		$this->assertStringContainsString( "'url_citation' !==", $provider );
		$this->assertStringNotContainsString( ':generateContent', $provider );
		$this->assertStringNotContainsString( 'groundingMetadata', $provider );
	}

	public function test_legacy_serp_credentials_are_removed_by_migration_17(): void {
		$migration = (string) file_get_contents(
			dirname( __DIR__, 2 ) . '/plugin/Infrastructure/Migration/Migrations/Version17RemoveLegacySerpCredentials.php'
		);

		$this->assertStringContainsString( 'return 17;', $migration );
		$this->assertStringContainsString( "delete( 'dataforseo' )", $migration );
	}
}
