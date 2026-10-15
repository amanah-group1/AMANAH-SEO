<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\License;
defined( 'ABSPATH' ) || exit;
final class EntitlementResolver {
 private $grace;
 public function __construct( ?GracePeriodPolicy $grace = null ) { $this->grace = $grace ?? new GracePeriodPolicy(); }
 public function allows( LicenseState $state, string $entitlement ): bool {
  if ( $state->isActive() ) return $state->hasEntitlement( $entitlement );
  if ( 'premium_updates' === $entitlement && $this->grace->allowsCachedState( $state ) ) return $state->hasEntitlement( $entitlement );
  return false;
 }
 public function resolve( LicenseState $state ): array { return $state->getEntitlements(); }
}
