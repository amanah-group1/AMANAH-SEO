<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Cache;

defined( 'ABSPATH' ) || exit;

interface CacheInterface {

	public function get( string $key, $default = null );

	public function set( string $key, $value, int $ttl = 300 ): bool;

	public function delete( string $key ): bool;
}
