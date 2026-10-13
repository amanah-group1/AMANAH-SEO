<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\Update;
defined( 'ABSPATH' ) || exit;
final class UpdatePackageVerifier {
 public function verify( UpdateMetadata $metadata, string $package_path, string $trustedEndpoint = '' ): bool {
  if ( 'Amanah SEO Engine V2' !== $metadata->getProduct() || ! is_file( $package_path ) || ! is_readable( $package_path ) ) return false;
  if ( '' !== $trustedEndpoint && ! ( new UpdateSourcePolicy() )->isTrusted( $trustedEndpoint, $metadata->getPackageUrl() ) ) return false;
  if ( '' === $trustedEndpoint && 0 !== strpos( $metadata->getPackageUrl(), 'https://' ) ) return false;
  $expected = strtolower( trim( $metadata->getChecksum() ) );
  if ( ! preg_match( '/^[a-f0-9]{64}$/', $expected ) ) return false;
  $actual = hash_file( 'sha256', $package_path );
  return is_string( $actual ) && hash_equals( $expected, strtolower( $actual ) );
 }
}
