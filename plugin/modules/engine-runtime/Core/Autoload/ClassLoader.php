<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Core\Autoload;

defined( 'ABSPATH' ) || exit;

/**
 * Minimal PSR-4 compatible autoloader for the plugin runtime.
 */
final class ClassLoader {

	/**
	 * Namespace prefix.
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Namespace base directory.
	 *
	 * @var string
	 */
	private $base_directory;

	public function __construct( string $prefix, string $base_directory ) {
		$this->prefix         = trim( $prefix, '\\' ) . '\\';
		$this->base_directory = rtrim( $base_directory, DIRECTORY_SEPARATOR );
	}

	public function register(): void {
		spl_autoload_register( array( $this, 'load' ) );
	}

	public function load( string $class ): void {
		if ( 0 !== strpos( $class, $this->prefix ) ) {
			return;
		}

		$relative_class = substr( $class, strlen( $this->prefix ) );
		$file           = $this->base_directory . DIRECTORY_SEPARATOR . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		if ( is_file( $file ) ) {
			require_once $file;
		}
	}
}
