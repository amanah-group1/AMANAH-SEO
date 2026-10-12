<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Http;

defined( 'ABSPATH' ) || exit;

final class WordPressHttpClient implements HttpClientInterface {

	public function request( string $method, string $url, array $args = array() ) {
		$args['method']  = strtoupper( $method );
		if ( function_exists( 'set_time_limit' ) ) { @set_time_limit( max( 30, (int) ( ( $args['timeout'] ?? 15 ) + 30 ) ) ); }
		if ( function_exists( 'ini_set' ) ) { @ini_set( 'max_execution_time', (string) max( 30, (int) ( ( $args['timeout'] ?? 15 ) + 30 ) ) ); }
		$args['timeout'] = isset( $args['timeout'] ) ? (float) $args['timeout'] : 15.0;

		return wp_safe_remote_request( esc_url_raw( $url ), $args );
	}
}
