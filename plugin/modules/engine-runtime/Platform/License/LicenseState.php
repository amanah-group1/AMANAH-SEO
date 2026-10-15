<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\License;
defined( 'ABSPATH' ) || exit;
final class LicenseState {
 private $data;
 public function __construct( array $data ){ $this->data=$data; }
 public function getStatus(): string { return (string)($this->data['status']??'unknown'); }
 public function getProduct(): string { return (string)($this->data['product']??''); }
 public function getPlan(): string { return (string)($this->data['plan']??''); }
 public function getEntitlements(): array { return is_array($this->data['entitlements']??null)?$this->data['entitlements']:array(); }
 public function getExpiration(): ?string { return isset($this->data['expiration'])?(string)$this->data['expiration']:null; }
 public function isActive(): bool { return 'active'===$this->getStatus(); }
 public function hasEntitlement(string $entitlement): bool { return in_array($entitlement,$this->getEntitlements(),true); }
 public function toArray(): array { return $this->data; }
}
