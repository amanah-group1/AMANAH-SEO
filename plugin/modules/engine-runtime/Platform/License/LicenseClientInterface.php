<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\License;
defined( 'ABSPATH' ) || exit;
interface LicenseClientInterface {
 public function activate(string $licenseKey): LicenseState;
 public function validate(): LicenseState;
 public function getState(): LicenseState;
 public function hasEntitlement(string $entitlement): bool;
}
