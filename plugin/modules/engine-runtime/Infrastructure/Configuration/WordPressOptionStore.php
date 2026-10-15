<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Configuration;

defined( 'ABSPATH' ) || exit;

final class WordPressOptionStore implements ConfigurationStoreInterface {

	public function get( string $key, $default = null ) {
		return get_option( $key, $default );
	}

	public function set( string $key, $value ): void {
		update_option( $key, $value, false );
	}

	public function delete( string $key ): void {
		delete_option( $key );
	}
}
