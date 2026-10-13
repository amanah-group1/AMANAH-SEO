<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Platform\Update\UpdateClient;
use Amanah\SEOEngine\Platform\Update\UpdateEligibility;
use Amanah\SEOEngine\Platform\Update\UpdateMetadata;
use Amanah\SEOEngine\Platform\Update\UpdatePackageVerifier;
use Amanah\SEOEngine\Platform\Update\UpdatePreflight;
use PHPUnit\Framework\TestCase;

final class Phase6UpdateSystemTest extends TestCase {

	public function test_update_is_unavailable_without_metadata(): void {
		$client = new UpdateClient( null, new UpdateEligibility(), new UpdatePreflight(), new UpdatePackageVerifier() );

		$this->assertFalse( $client->is_update_available() );
		$this->assertSame( array( 'update_unavailable' ), $client->evaluate( true )['reasons'] );
	}

	public function test_update_requires_entitlement(): void {
		$metadata = new UpdateMetadata( '0.2.0', 'https://updates.example.test/package.zip', str_repeat( 'a', 64 ), '7.4', '6.0' );
		$client   = new UpdateClient( $metadata, new UpdateEligibility(), new UpdatePreflight(), new UpdatePackageVerifier() );

		$result = $client->evaluate( false );

		$this->assertTrue( $result['available'] );
		$this->assertContains( 'update_not_entitled', $result['reasons'] );
		$this->assertFalse( $result['eligible'] );
	}

	public function test_package_checksum_is_verified(): void {
		$metadata = new UpdateMetadata( '0.2.0', 'https://updates.example.test/package.zip', hash( 'sha256', 'amanah-update-test' ), '7.4', '6.0' );
		$client   = new UpdateClient( $metadata, new UpdateEligibility(), new UpdatePreflight(), new UpdatePackageVerifier() );
		$path     = tempnam( sys_get_temp_dir(), 'amanah-update-' );

		file_put_contents( $path, 'amanah-update-test' );

		$this->assertTrue( $client->verifyPackage( $path ) );

		file_put_contents( $path, 'tampered' );
		$this->assertFalse( $client->verifyPackage( $path ) );

		unlink( $path );
	}
}
