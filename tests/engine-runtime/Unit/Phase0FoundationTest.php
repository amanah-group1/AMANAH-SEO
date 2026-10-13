<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Core\Bootstrap\Plugin;
use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;
use Amanah\SEOEngine\Infrastructure\Configuration\ConfigurationStoreInterface;
use PHPUnit\Framework\TestCase;

final class Phase0FoundationTest extends TestCase {

	public function test_plugin_bootstrap_registers_service_registry(): void {
		$services = Plugin::boot();

		$this->assertTrue( $services->has( 'plugin' ) );
		$this->assertInstanceOf( Plugin::class, $services->get( 'plugin' ) );
	}

	public function test_configuration_defaults_to_version_one(): void {
		$store = new class implements ConfigurationStoreInterface {

			private $values = array();

			public function get( string $key, $default = null ) {
				return array_key_exists( $key, $this->values ) ? $this->values[ $key ] : $default;
			}

			public function set( string $key, $value ): void {
				$this->values[ $key ] = $value;
			}

			public function delete( string $key ): void {
				unset( $this->values[ $key ] );
			}
		};

		$configuration = new Configuration( $store );

		$this->assertSame( 1, $configuration->getVersion() );
	}
}
