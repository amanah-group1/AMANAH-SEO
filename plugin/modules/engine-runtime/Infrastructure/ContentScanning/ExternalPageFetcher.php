<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\ContentScanning;
use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;
defined('ABSPATH') || exit;
final class ExternalPageFetcher {
 private $http;
 public function __construct(HttpClientInterface $http){$this->http=$http;}
 public function fetch(string $url):array{
  $r=$this->http->request('GET',$url,array('timeout'=>10,'redirection'=>3,'headers'=>array('User-Agent'=>'Amanah SEO Engine Content Scanner/1.0','Accept'=>'text/html,application/xhtml+xml')));
  if(is_wp_error($r))return array('status'=>'failed','http_status'=>0,'error'=>$r->get_error_message(),'html'=>'');
  $status=(int)wp_remote_retrieve_response_code($r);$html=(string)wp_remote_retrieve_body($r);
  return array('status'=>$status>=200&&$status<400?'analyzed':'failed','http_status'=>$status,'error'=>$status>=400?'HTTP '.$status:'','html'=>$html);
 }
}