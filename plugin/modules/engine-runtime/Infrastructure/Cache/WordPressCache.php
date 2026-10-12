<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Cache;

defined( 'ABSPATH' ) || exit;

final class WordPressCache implements CacheInterface {

	private const GROUP = 'amanah_seo_engine';

	public function get( string $key, $default = null ) {
		$found = false;
		$value = wp_cache_get( $key, self::GROUP, false, $found );

		return $found ? $value : $default;
	}

	public function set( string $key, $value, int $ttl = 300 ): bool {
		return wp_cache_set( $key, $value, self::GROUP, $ttl );
	}

	public function delete( string $key ): bool {
		return wp_cache_delete( $key, self::GROUP );
	}
}
