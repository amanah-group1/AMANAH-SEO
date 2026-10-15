<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\License;
defined( 'ABSPATH' ) || exit;
final class LicenseCache {
 private const OPTION='amanah_seo_license_state';
 public function get(): ?LicenseState { $data=get_option(self::OPTION,null); return is_array($data)?new LicenseState($data):null; }
 public function save(LicenseState $state): void { update_option(self::OPTION,$state->toArray(),false); }
 public function clear(): void { delete_option(self::OPTION); }
}
