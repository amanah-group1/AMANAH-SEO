<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Platform\Update;
use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;
defined( 'ABSPATH' ) || exit;
final class UpdateServerClient {
 private $http; private $endpoint; private $product;
 public function __construct(HttpClientInterface $http,string $product,string $endpoint){$this->http=$http;$this->product=$product;$this->endpoint=rtrim($endpoint,'/');}
 public function fetch(string $channel='stable'): ?UpdateMetadata {
  if(''===$this->endpoint||0!==strpos($this->endpoint,'https://'))return null;
  $response=$this->http->request('GET',$this->endpoint,array('timeout'=>10,'headers'=>array('Accept'=>'application/json')));
  if(is_wp_error($response)||(int)wp_remote_retrieve_response_code($response)<200||(int)wp_remote_retrieve_response_code($response)>=300)return null;
  $data=json_decode((string)wp_remote_retrieve_body($response),true);
  if(!is_array($data)||($data['product']??'')!==$this->product||($data['channel']??'')!==$channel)return null;
  foreach(array('version','package_url','checksum','requires_php','requires_wordpress') as $key)if(!isset($data[$key]))return null;
  return new UpdateMetadata((string)$data['version'],(string)$data['package_url'],(string)$data['checksum'],(string)$data['requires_php'],(string)$data['requires_wordpress'],$channel,(string)($data['entitlement']??'premium_updates'),$this->product);
 }
 public function download(UpdateMetadata $metadata): ?string {
  $url=$metadata->getPackageUrl();if(0!==strpos($url,'https://'))return null;
  $path=tempnam(sys_get_temp_dir(),'amanah-update-');if(false===$path)return null;
  $response=$this->http->request('GET',$url,array('timeout'=>30,'stream'=>true,'filename'=>$path));
  if(is_wp_error($response)||(int)wp_remote_retrieve_response_code($response)<200||(int)wp_remote_retrieve_response_code($response)>=300){@unlink($path);return null;}
  return is_file($path)?$path:null;
 }
}
