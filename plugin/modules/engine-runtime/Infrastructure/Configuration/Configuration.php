<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Configuration;

defined( 'ABSPATH' ) || exit;

final class Configuration {

	private const OPTION_KEY = 'amanah_seo_engine_configuration';

	private $store;

	public function __construct( ConfigurationStoreInterface $store ) {
		$this->store = $store;
	}

	public function get( string $key, $default = null ) {
		$config = $this->store->get( self::OPTION_KEY, array() );

		if ( ! is_array( $config ) || ! array_key_exists( $key, $config ) ) {
			return $default;
		}

		return $config[ $key ];
	}

	public function set( string $key, $value ): void {
		$config = $this->store->get( self::OPTION_KEY, array() );
		$config = is_array( $config ) ? $config : array();
		$config[ $key ] = $value;

		$this->store->set( self::OPTION_KEY, $config );
	}

	public function all(): array {
		$config = $this->store->get( self::OPTION_KEY, array() );

		return is_array( $config ) ? $config : array();
	}

	public function initializeDefaults(): void {
		$config = $this->all();
		$defaults = array(
			'configuration_version' => 1,
			'ai_enabled'            => true,
			'ai_provider'           => 'gemini',
			'ai_model'              => 'gemini-2.5-flash',
			'debug_logging'         => false,
			'automatic_publishing'  => false,
			'background_processing' => true,
			'delete_data_on_uninstall' => false,
			'license_server_endpoint' => '',
			'update_server_endpoint' => '',
			'update_channel' => 'stable',
		);

		$changed = false;

		// Migrate the temporary zero-configuration AI mode back to the proven Gemini path.
		if ( 'amanah_seo_engine' === (string) ( $config['ai_provider'] ?? '' ) ) {
			$config['ai_provider'] = 'gemini';
			$config['ai_enabled'] = true;
			if ( 'amanah-default-v1' === (string) ( $config['ai_model'] ?? '' ) || '' === (string) ( $config['ai_model'] ?? '' ) ) {
				$config['ai_model'] = 'gemini-2.5-flash';
			}
			$changed = true;
		}
		$active_provider = sanitize_key( (string) get_option( 'amanah_seo_engine_active_ai_provider', '' ) );
		if ( 'amanah_seo_engine' === $active_provider ) {
			update_option( 'amanah_seo_engine_active_ai_provider', 'gemini', false );
		}
		$active_model = sanitize_text_field( (string) get_option( 'amanah_seo_engine_active_ai_model', '' ) );
		if ( 'amanah-default-v1' === $active_model ) {
			update_option( 'amanah_seo_engine_active_ai_model', 'gemini-2.5-flash', false );
		}
		foreach ( $defaults as $key => $value ) {
			if ( ! array_key_exists( $key, $config ) ) {
				$config[ $key ] = $value;
				$changed = true;
			}
		}

		if ( $changed ) {
			$this->store->set( self::OPTION_KEY, $config );
		}
	}

	public function getVersion(): int {
		return (int) $this->get( 'configuration_version', 1 );
	}
}
