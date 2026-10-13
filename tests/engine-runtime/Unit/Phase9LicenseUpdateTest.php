<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Tests\Unit;

use Amanah\SEOEngine\Platform\License\EntitlementResolver;
use Amanah\SEOEngine\Platform\License\LicenseResponseValidator;
use Amanah\SEOEngine\Platform\License\LicenseState;
use Amanah\SEOEngine\Platform\Update\UpdateClient;
use Amanah\SEOEngine\Platform\Update\UpdateEligibility;
use Amanah\SEOEngine\Platform\Update\UpdateMetadata;
use Amanah\SEOEngine\Platform\Update\UpdatePackageVerifier;
use Amanah\SEOEngine\Platform\Update\UpdatePreflight;
use PHPUnit\Framework\TestCase;

final class Phase9LicenseUpdateTest extends TestCase {

 public function test_license_response_is_normalized_and_entitlement_is_resolved(): void {
  $state=(new LicenseResponseValidator())->validate(array(
   'license'=>array('id'=>'LIC-1'),'product'=>'Amanah SEO Engine V2','status'=>'active','plan'=>'pro',
   'entitlements'=>array('seo_core','premium_updates'),'validated_at'=>'2026-09-27T00:00:00Z',
   'expiration'=>'2027-09-27T00:00:00Z',
  ),'Amanah SEO Engine V2');
  $this->assertTrue($state->isActive());
  $this->assertSame('pro',$state->getPlan());
  $this->assertTrue((new EntitlementResolver())->allows($state,'premium_updates'));
 }

 public function test_license_product_mismatch_is_rejected(): void {
  $this->expectException(\InvalidArgumentException::class);
  (new LicenseResponseValidator())->validate(array(
   'license'=>array('id'=>'LIC-1'),'product'=>'Other Product','status'=>'active','plan'=>'pro',
   'entitlements'=>array(),'validated_at'=>'2026-09-27T00:00:00Z',
  ),'Amanah SEO Engine V2');
 }

 public function test_update_requires_entitlement_and_compatibility(): void {
  $metadata=new UpdateMetadata('0.2.0','https://updates.example.test/package.zip',str_repeat('a',64),'7.4','6.0');
  $client=new UpdateClient($metadata,new UpdateEligibility(),new UpdatePreflight(),new UpdatePackageVerifier());
  $result=$client->evaluate(false);
  $this->assertFalse($result['eligible']);
  $this->assertContains('update_not_entitled',$result['reasons']);
 }

 public function test_update_package_requires_trusted_https_metadata_and_checksum(): void {
  $path=tempnam(sys_get_temp_dir(),'amanah-update-');
  file_put_contents($path,'amanah-update-test');
  $metadata=new UpdateMetadata('0.2.0','https://updates.example.test/package.zip',hash('sha256','amanah-update-test'),'7.4','6.0');
  $this->assertTrue((new UpdatePackageVerifier())->verify($metadata,$path));
  $metadataBad=new UpdateMetadata('0.2.0','http://updates.example.test/package.zip',hash('sha256','amanah-update-test'),'7.4','6.0');
  $this->assertFalse((new UpdatePackageVerifier())->verify($metadataBad,$path));
  unlink($path);
 }

 public function test_license_state_does_not_require_remote_server_for_cached_read(): void {
  $state=new LicenseState(array('status'=>'active','product'=>'Amanah SEO Engine V2','entitlements'=>array('seo_core')));
  $this->assertTrue($state->isActive());
  $this->assertTrue($state->hasEntitlement('seo_core'));
  $this->assertFalse($state->hasEntitlement('premium_updates'));
 }
}
