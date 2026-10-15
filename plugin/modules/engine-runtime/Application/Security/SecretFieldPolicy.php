<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Security;

defined( 'ABSPATH' ) || exit;

final class SecretFieldPolicy {

	private const SENSITIVE_KEYS = array(
		'api_key',
		'apikey',
		'authorization',
		'password',
		'secret',
		'token',
		'credential',
		'credentials',
		'license_key',
		'private_key',
	);

	public static function isSensitiveKey( string $key ): bool {
		$normalized = preg_replace( '/[^a-z0-9]+/', '_', strtolower( $key ) ) ?? strtolower( $key );
		return in_array( $normalized, self::SENSITIVE_KEYS, true );
	}
}
