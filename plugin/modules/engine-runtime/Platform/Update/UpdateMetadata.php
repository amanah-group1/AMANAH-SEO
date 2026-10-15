<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\Update;
defined( 'ABSPATH' ) || exit;
final class UpdateMetadata {
 private $version;private $package_url;private $checksum;private $requires_php;private $requires_wordpress;private $channel;private $entitlement;private $product;
 public function __construct(string $version,string $package_url,string $checksum,string $requires_php,string $requires_wordpress,string $channel='stable',string $entitlement='premium_updates',string $product='Amanah SEO Engine V2'){
  $this->version=$version;$this->package_url=$package_url;$this->checksum=$checksum;$this->requires_php=$requires_php;$this->requires_wordpress=$requires_wordpress;$this->channel=$channel;$this->entitlement=$entitlement;$this->product=$product;
 }
 public function getVersion():string{return $this->version;} public function getPackageUrl():string{return $this->package_url;} public function getChecksum():string{return $this->checksum;} public function getRequiresPhp():string{return $this->requires_php;} public function getRequiresWordPress():string{return $this->requires_wordpress;} public function getChannel():string{return $this->channel;} public function getEntitlement():string{return $this->entitlement;} public function getProduct():string{return $this->product;}
}
