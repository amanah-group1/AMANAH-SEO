<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\Update;
defined( 'ABSPATH' ) || exit;
final class UpdateSourcePolicy {
 public function isTrusted( string $configuredEndpoint, string $packageUrl ): bool {
  if ( 0 !== strpos( $configuredEndpoint, 'https://' ) || 0 !== strpos( $packageUrl, 'https://' ) ) return false;
  $endpoint = wp_parse_url( $configuredEndpoint );
  $package = wp_parse_url( $packageUrl );
  if ( ! is_array( $endpoint ) || ! is_array( $package ) ) return false;
  $host = strtolower( (string) ( $endpoint['host'] ?? '' ) );
  $packageHost = strtolower( (string) ( $package['host'] ?? '' ) );
  return '' !== $host && $host === $packageHost;
 }
}
