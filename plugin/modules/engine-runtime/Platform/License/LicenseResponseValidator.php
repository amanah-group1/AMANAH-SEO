<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\License;
defined( 'ABSPATH' ) || exit;
final class LicenseResponseValidator {
 public function validate(array $response,string $expectedProduct): LicenseState {
  foreach(array('license','product','status','plan','entitlements','validated_at') as $key){if(!array_key_exists($key,$response))throw new \InvalidArgumentException('license_response_missing_'.$key);}
  if((string)$response['product']!==$expectedProduct)throw new \InvalidArgumentException('license_product_mismatch');
  if(!is_array($response['license'])||!is_string($response['status'])||!is_array($response['entitlements']))throw new \InvalidArgumentException('license_response_invalid');
  $allowed=array('unconfigured','active','expired','revoked','suspended','invalid','unknown');
  if(!in_array($response['status'],$allowed,true))throw new \InvalidArgumentException('license_status_invalid');
  return new LicenseState(array(
   'license_id'=>(string)($response['license']['id']??''),
   'product'=>(string)$response['product'],'status'=>(string)$response['status'],
   'plan'=>(string)$response['plan'],'entitlements'=>array_values(array_map('strval',$response['entitlements'])),
   'expiration'=>isset($response['expiration'])?(string)$response['expiration']:null,
   'validated_at'=>(string)$response['validated_at'],
   'next_validation'=>isset($response['next_validation'])?(string)$response['next_validation']:null,
   'metadata'=>is_array($response['metadata']??null)?$response['metadata']:array()
  ));
 }
}
