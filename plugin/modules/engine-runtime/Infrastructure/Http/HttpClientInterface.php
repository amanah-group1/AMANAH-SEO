<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Http;

defined( 'ABSPATH' ) || exit;

interface HttpClientInterface {

	public function request( string $method, string $url, array $args = array() );
}
