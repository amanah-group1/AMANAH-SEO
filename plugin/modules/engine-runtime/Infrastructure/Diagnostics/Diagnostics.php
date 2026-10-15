<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Diagnostics;

use Amanah\SEOEngine\Application\Security\SecretFieldPolicy;

defined( 'ABSPATH' ) || exit;

final class Diagnostics implements LoggerInterface {

	private $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function debug( string $event_type, string $message, array $context = array() ): void {
		$this->write( 'debug', $event_type, $message, $context );
	}

	public function info( string $event_type, string $message, array $context = array() ): void {
		$this->write( 'info', $event_type, $message, $context );
	}

	public function warning( string $event_type, string $message, array $context = array() ): void {
		$this->write( 'warning', $event_type, $message, $context );
	}

	public function error( string $event_type, string $message, array $context = array() ): void {
		$this->write( 'error', $event_type, $message, $context );
	}

	private function write( string $level, string $event_type, string $message, array $context ): void {
		$encoded_context = wp_json_encode( $this->sanitize( $context ) );

		$this->wpdb->insert(
			$this->wpdb->prefix . 'amanah_seo_diagnostics',
			array(
				'level'      => $level,
				'event_type' => sanitize_key( $event_type ),
				'message'    => wp_strip_all_tags( $message ),
				'context'    => is_string( $encoded_context ) ? $encoded_context : null,
				'created_at' => current_time( 'mysql', true ),
			),
			array( '%s', '%s', '%s', '%s', '%s' )
		);
	}

	private function sanitize( array $context ): array {
		foreach ( $context as $key => $value ) {
			if ( SecretFieldPolicy::isSensitiveKey( (string) $key ) ) {
				$context[ $key ] = '[REDACTED]';
				continue;
			}

			if ( is_array( $value ) ) {
				$context[ $key ] = $this->sanitize( $value );
			}
		}

		return $context;
	}
}
