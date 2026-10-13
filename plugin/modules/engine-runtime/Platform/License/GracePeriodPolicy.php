<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\License;
defined( 'ABSPATH' ) || exit;
final class GracePeriodPolicy {
 private $seconds;
 public function __construct( int $seconds = 86400 ) { $this->seconds = max( 0, $seconds ); }
 public function allowsCachedState( LicenseState $state, int $now = 0 ): bool {
  if ( ! in_array( $state->getStatus(), array( 'active', 'unknown' ), true ) ) return false;
  $validated = $state->getValidatedAt();
  if ( null === $validated || '' === $validated ) return false;
  $timestamp = strtotime( $validated );
  if ( false === $timestamp ) return false;
  $now = $now > 0 ? $now : time();
  return $now >= $timestamp && ( $now - $timestamp ) <= $this->seconds;
 }
}
