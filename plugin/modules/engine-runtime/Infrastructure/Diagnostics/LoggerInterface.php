<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Diagnostics;

defined( 'ABSPATH' ) || exit;

interface LoggerInterface {

	public function debug( string $event_type, string $message, array $context = array() ): void;

	public function info( string $event_type, string $message, array $context = array() ): void;

	public function warning( string $event_type, string $message, array $context = array() ): void;

	public function error( string $event_type, string $message, array $context = array() ): void;
}
