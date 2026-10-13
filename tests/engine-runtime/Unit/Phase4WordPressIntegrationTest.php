<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Application\Port\ContentPublisherInterface;
use Amanah\SEOEngine\Application\Port\SchedulerInterface;
use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;
use Amanah\SEOEngine\Infrastructure\Configuration\ConfigurationStoreInterface;
use Amanah\SEOEngine\Infrastructure\WordPress\Content\WordPressContentPublisher;
use Amanah\SEOEngine\Infrastructure\WordPress\Cron\WpCronAdapter;
use PHPUnit\Framework\TestCase;

final class Phase4WordPressIntegrationTest extends TestCase {

	public function test_configuration_initializes_safe_wordpress_defaults(): void {
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
		$configuration->initializeDefaults();

		$this->assertTrue( $configuration->get( 'ai_enabled' ) );
		$this->assertFalse( $configuration->get( 'debug_logging' ) );
		$this->assertFalse( $configuration->get( 'automatic_publishing' ) );
		$this->assertTrue( $configuration->get( 'background_processing' ) );
		$this->assertFalse( $configuration->get( 'delete_data_on_uninstall' ) );
	}

	public function test_wordpress_adapters_respect_application_contracts(): void {
		$this->assertInstanceOf( ContentPublisherInterface::class, new WordPressContentPublisher() );
		$this->assertInstanceOf( SchedulerInterface::class, new WpCronAdapter() );
	}
}
