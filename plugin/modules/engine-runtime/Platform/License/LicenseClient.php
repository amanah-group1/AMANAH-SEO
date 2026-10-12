<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\License;
use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;
defined( 'ABSPATH' ) || exit;
final class LicenseClient implements LicenseClientInterface {
 private $http; private $cache; private $secret; private $validator; private $product; private $endpoint;
 public function __construct(HttpClientInterface $http,LicenseCache $cache,LicenseSecretStore $secret,LicenseResponseValidator $validator,string $product,string $endpoint=''){
  $this->http=$http;$this->cache=$cache;$this->secret=$secret;$this->validator=$validator;$this->product=$product;$this->endpoint=rtrim($endpoint,'/');
 }
 public function activate(string $licenseKey): LicenseState {
  if(''===$this->endpoint||''===trim($licenseKey))throw new \InvalidArgumentException('license_activation_unavailable');
  $state=$this->request('/activate',$licenseKey);$this->secret->set($licenseKey);$this->cache->save($state);return $state;
 }
 public function validate(): LicenseState {
  $cached=$this->cache->get();$key=$this->secret->get();
  if(null===$key||''===$this->endpoint)return $cached??new LicenseState(array('status'=>'unconfigured','product'=>$this->product,'entitlements'=>array()));
  try{$state=$this->request('/validate',$key);$this->cache->save($state);return $state;}catch(\Throwable $e){return $cached??new LicenseState(array('status'=>'unknown','product'=>$this->product,'entitlements'=>array()));}
 }
 public function getState(): LicenseState { return $this->cache->get()??new LicenseState(array('status'=>'unconfigured','product'=>$this->product,'entitlements'=>array())); }
 public function hasEntitlement(string $entitlement): bool { return (new EntitlementResolver())->allows($this->validate(),$entitlement); }
 private function request(string $path,string $licenseKey): LicenseState {
  if(0!==strpos($this->endpoint,'https://'))throw new \InvalidArgumentException('license_endpoint_must_use_https');
  $response=$this->http->request('POST',$this->endpoint.$path,array('timeout'=>10,'headers'=>array('Content-Type'=>'application/json'),'body'=>wp_json_encode(array('license_key'=>$licenseKey,'product'=>$this->product))));
  if(is_wp_error($response))throw new \RuntimeException('license_server_unavailable');
  $code=(int)wp_remote_retrieve_response_code($response);if($code<200||$code>=300)throw new \RuntimeException('license_http_error');
  $body=json_decode((string)wp_remote_retrieve_body($response),true);if(!is_array($body))throw new \InvalidArgumentException('license_response_invalid');
  return $this->validator->validate($body,$this->product);
 }
}
