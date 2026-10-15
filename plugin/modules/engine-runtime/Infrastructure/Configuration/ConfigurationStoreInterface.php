<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Configuration;

defined( 'ABSPATH' ) || exit;

interface ConfigurationStoreInterface {

	public function get( string $key, $default = null );

	public function set( string $key, $value ): void;

	public function delete( string $key ): void;
}
