<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\WordPress\Settings;

use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;

defined( 'ABSPATH' ) || exit;

final class SettingsRegistrar {

	private const GROUP = 'amanah_seo_engine_settings';

	private $configuration;

	public function __construct( Configuration $configuration ) {
		$this->configuration = $configuration;
	}

	public function register(): void {
		register_setting(
			self::GROUP,
			'amanah_seo_engine_configuration',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitizeConfiguration' ),
				'default'           => array(),
			)
		);
	}

	public function sanitizeConfiguration( $value ): array {
		$value = is_array( $value ) ? $value : array();

		$configuration = $this->configuration->all();
		$configuration['debug_logging'] = ! empty( $value['debug_logging'] );
		$configuration['automatic_publishing'] = ! empty( $value['automatic_publishing'] );
		$configuration['license_server_endpoint'] = isset( $value['license_server_endpoint'] ) ? esc_url_raw( (string) $value['license_server_endpoint'] ) : (string) ( $configuration['license_server_endpoint'] ?? '' );
		$configuration['update_server_endpoint'] = isset( $value['update_server_endpoint'] ) ? esc_url_raw( (string) $value['update_server_endpoint'] ) : (string) ( $configuration['update_server_endpoint'] ?? '' );
		$channel = isset( $value['update_channel'] ) ? sanitize_key( (string) $value['update_channel'] ) : 'stable';
		$configuration['update_channel'] = in_array( $channel, array( 'stable', 'beta', 'development' ), true ) ? $channel : 'stable';

		return $configuration;
	}
}
