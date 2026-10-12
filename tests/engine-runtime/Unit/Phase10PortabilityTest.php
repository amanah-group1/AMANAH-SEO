<?php
declare( strict_types=1 );

use Amanah\SEOEngine\Application\Portability\PortabilityBackupRepositoryInterface;
use Amanah\SEOEngine\Application\Portability\PortabilityFormat;
use Amanah\SEOEngine\Application\Portability\PortabilityRepositoryInterface;
use Amanah\SEOEngine\Application\Portability\PortabilityService;
use Amanah\SEOEngine\Application\Portability\SecretSanitizer;
use PHPUnit\Framework\TestCase;

final class Phase10PortabilityTest extends TestCase {
	private function service( ?array &$captured = null ): PortabilityService {
		$repo = new class( $captured ) implements PortabilityRepositoryInterface {
			private $captured;
			public function __construct( ?array &$captured = null ) { $this->captured =& $captured; }
			public function export( array $scopes ): array { return array( 'keywords' => array( array( 'id' => 1, 'keyword' => 'jual iphone', 'api_key' => 'x' ) ) ); }
			public function previewImport( array $document, array $resolutions = array() ): array { return array( 'new_records' => 1, 'updated_records' => 0, 'conflicts' => 0, 'rejected_records' => 0 ); }
			public function import( array $document, string $mode = 'merge', array $resolutions = array() ): array { $this->captured = array( 'document' => $document, 'mode' => $mode ); return array( 'status' => 'completed' ); }
		};
		return new PortabilityService( $repo );
	}

	public function test_secret_sanitizer_removes_credentials_recursively(): void {
		$result = ( new SecretSanitizer() )->sanitize( array( 'provider' => 'openai', 'api_key' => 'secret', 'nested' => array( 'token' => 'hidden', 'value' => 'kept' ) ) );
		$this->assertSame( array( 'provider' => 'openai', 'nested' => array( 'value' => 'kept' ) ), $result );
	}

	public function test_export_is_versioned_and_scoped(): void {
		$result = $this->service()->export( array( PortabilityFormat::SCOPE_SEO ) );
		$this->assertSame( 1, $result['format_version'] );
		$this->assertSame( PortabilityFormat::PRODUCT, $result['product'] );
		$this->assertSame( array( PortabilityFormat::SCOPE_SEO ), $result['data_scope'] );
		$this->assertSame( 7, $result['schema_version'] );
		$this->assertArrayNotHasKey( 'api_key', $result['records']['keywords'][0] );
	}

	public function test_import_rejects_unknown_format(): void {
		$service = $this->service();
		$this->expectException( InvalidArgumentException::class );
		$service->previewImport( array( 'format_version' => 99, 'product' => PortabilityFormat::PRODUCT, 'type' => PortabilityFormat::TYPE, 'plugin_version' => '0.1.0', 'schema_version' => 6, 'data_scope' => array( PortabilityFormat::SCOPE_SEO ), 'records' => array() ) );
	}

	public function test_import_rejects_unknown_scope(): void {
		$service = $this->service();
		$this->expectException( InvalidArgumentException::class );
		$service->previewImport( array( 'format_version' => 1, 'product' => PortabilityFormat::PRODUCT, 'type' => PortabilityFormat::TYPE, 'plugin_version' => '0.1.0', 'schema_version' => 6, 'data_scope' => array( 'wordpress_posts' ), 'records' => array() ) );
	}

	public function test_import_requires_explicit_confirmation(): void {
		$service = $this->service();
		$document = array( 'format_version' => 1, 'product' => PortabilityFormat::PRODUCT, 'type' => PortabilityFormat::TYPE, 'plugin_version' => '0.1.0', 'schema_version' => 6, 'data_scope' => array( PortabilityFormat::SCOPE_SEO ), 'records' => array( 'keywords' => array( array( 'id' => 1, 'keyword' => 'jual iphone' ) ) ) );
		$preview = $service->previewImport( $document );
		$this->assertTrue( $preview['requires_confirmation'] );
		$this->expectException( InvalidArgumentException::class );
		$service->import( $document );
	}

	public function test_replace_requires_confirmation_and_is_supported(): void {
		$service = $this->service();
		$document = array( 'format_version' => 1, 'product' => PortabilityFormat::PRODUCT, 'type' => PortabilityFormat::TYPE, 'plugin_version' => '0.1.0', 'schema_version' => 6, 'data_scope' => array( PortabilityFormat::SCOPE_SEO ), 'records' => array( 'keywords' => array( array( 'id' => 1, 'keyword' => 'jual iphone' ) ) ) );
		$preview = $service->previewImport( $document, 'replace' );
		$this->assertSame( 'replace', $preview['mode'] );
		$this->assertSame( array( 'status' => 'completed' ), $service->import( $document, 'replace', $preview['confirmation_token'] ) );
	}


	public function test_conflict_resolution_is_validated_and_bound_to_confirmation(): void {
		$service = $this->service();
		$document = array(
			'format_version' => 1,
			'product' => PortabilityFormat::PRODUCT,
			'type' => PortabilityFormat::TYPE, 'plugin_version' => '0.1.0', 'schema_version' => 6,
			'data_scope' => array( PortabilityFormat::SCOPE_SEO ),
			'records' => array( 'keywords' => array( array( 'id' => 1, 'keyword' => 'jual iphone' ) ) ),
		);
		$preview = $service->previewImport( $document, 'merge', array( 'keywords:1' => 'keep_local' ) );
		$this->assertSame( 'keep_local', $preview['resolutions']['keywords:1'] );
		$this->assertNotSame(
			$service->previewImport( $document, 'merge' )['confirmation_token'],
			$preview['confirmation_token']
		);
	}

	public function test_invalid_conflict_resolution_is_rejected(): void {
		$service = $this->service();
		$document = array(
			'format_version' => 1,
			'product' => PortabilityFormat::PRODUCT,
			'type' => PortabilityFormat::TYPE, 'plugin_version' => '0.1.0', 'schema_version' => 6,
			'data_scope' => array( PortabilityFormat::SCOPE_SEO ),
			'records' => array(),
		);
		$this->expectException( InvalidArgumentException::class );
		$service->previewImport( $document, 'merge', array( 'keywords:1' => 'delete_everything' ) );
	}

	public function test_import_rejects_missing_relationship(): void {
		$service = $this->service();
		$document = array( 'format_version' => 1, 'product' => PortabilityFormat::PRODUCT, 'type' => PortabilityFormat::TYPE, 'plugin_version' => '0.1.0', 'schema_version' => 6, 'data_scope' => array( PortabilityFormat::SCOPE_SEO ), 'records' => array( 'keywords' => array( array( 'id' => 1, 'keyword' => 'jual iphone', 'cluster_id' => 99 ) ) ) );
		$this->expectException( InvalidArgumentException::class );
		$service->previewImport( $document );
	}

	public function test_import_rejects_unsafe_owner_url(): void {
		$service = $this->service();
		$document = array( 'format_version' => 1, 'product' => PortabilityFormat::PRODUCT, 'type' => PortabilityFormat::TYPE, 'plugin_version' => '0.1.0', 'schema_version' => 6, 'data_scope' => array( PortabilityFormat::SCOPE_SEO ), 'records' => array( 'owner_urls' => array( array( 'id' => 1, 'url' => 'javascript:alert(1)' ) ) ) );
		$this->expectException( InvalidArgumentException::class );
		$service->previewImport( $document );
	}
	public function test_backup_is_stored_and_restore_preview_uses_compatibility_gate(): void {
		$backupRepo = new class implements PortabilityBackupRepositoryInterface {
			private $data = array();
			public function create( string $backupId, array $document, array $metadata = array() ): array { $this->data[ $backupId ] = array( 'backup_id' => $backupId, 'status' => 'ready', 'document' => $document, 'metadata' => $metadata ); return $this->data[ $backupId ]; }
			public function find( string $backupId ): ?array { return isset( $this->data[ $backupId ] ) ? $this->data[ $backupId ] : null; }
			public function markStatus( string $backupId, string $status, array $metadata = array() ): bool { return isset( $this->data[ $backupId ] ); }
			public function delete( string $backupId ): bool { unset( $this->data[ $backupId ] ); return true; }
		};
		$repo = new class implements PortabilityRepositoryInterface {
			public function export( array $scopes ): array { return array( 'keywords' => array() ); }
			public function previewImport( array $document, array $resolutions = array() ): array { return array( 'new_records' => 0, 'updated_records' => 0, 'conflicts' => 0, 'rejected_records' => 0 ); }
			public function import( array $document, string $mode = 'merge', array $resolutions = array() ): array { return array( 'status' => 'completed', 'imported_records' => 0, 'operation_id' => 'op' ); }
		};
		$service = new PortabilityService( $repo, null, null, $backupRepo );
		$backup = $service->createBackup( array( PortabilityFormat::SCOPE_SEO ) );
		$this->assertSame( 'ready', $backup['status'] );
		$this->assertSame( 7, $backup['metadata']['schema_version'] );
		$preview = $service->previewBackupRestore( $backup['backup_id'] );
		$this->assertArrayHasKey( 'confirmation_token', $preview );
	}

	public function test_compatibility_gate_rejects_future_schema(): void {
		$gate = new \Amanah\SEOEngine\Application\Portability\PortabilityCompatibilityGate();
		$this->expectException( InvalidArgumentException::class );
		$gate->validate( array( 'schema_version' => 99, 'plugin_version' => '0.1.0' ) );
	}

}
