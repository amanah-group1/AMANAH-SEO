<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI\Provider;

use Amanah\SEOEngine\AI\AIRequest;
use Amanah\SEOEngine\AI\AIResponse;
use Amanah\SEOEngine\Infrastructure\Http\HttpClientInterface;

defined( 'ABSPATH' ) || exit;

final class CustomAIProvider implements AIProviderInterface {

 private $http;

 public function __construct( HttpClientInterface $http ) { $this->http = $http; }
 public function getProviderIdentifier(): string { return 'custom_ai'; }
 public function getProviderName(): string { return 'Custom AI'; }
 public function getCapabilities(): array { return array( ProviderCapabilities::TEXT_GENERATION ); }

 public function validateCredentials( array $credentials ): array {
  $endpoint=trim((string)($credentials['endpoint']??'')); $apiKey=trim((string)($credentials['api_key']??''));
  if(''===$endpoint||''===$apiKey)return array('valid'=>false,'category'=>'invalid_request_error');
  $parts=parse_url($endpoint); if(false===$parts)return array('valid'=>false,'category'=>'invalid_request_error');
  $scheme=strtolower((string)($parts['scheme']??'')); $host=strtolower((string)($parts['host']??''));
  if('https'!==$scheme||''===$host||isset($parts['user'])||isset($parts['pass']))return array('valid'=>false,'category'=>'invalid_request_error');
  if(in_array($host,array('localhost','localhost.localdomain','127.0.0.1','::1'),true))return array('valid'=>false,'category'=>'invalid_request_error');
  if(false!==filter_var($host,FILTER_VALIDATE_IP)){ $publicIp=filter_var($host,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE); if(false===$publicIp)return array('valid'=>false,'category'=>'invalid_request_error'); }
  return array('valid'=>true,'category'=>null);
 }

 public function discoverModels(array $credentials): array { return array(); }

 public function generate(AIRequest $request,array $credentials): AIResponse {
  if(empty($this->validateCredentials($credentials)['valid']))throw new \RuntimeException('invalid_request_error');
  $headers=array('Content-Type'=>'application/json');
  if('bearer'===($credentials['authentication']??'api_key'))$headers['Authorization']='Bearer '.$credentials['api_key'];else$headers['X-API-Key']=$credentials['api_key'];
  $response=$this->http->request('POST',$credentials['endpoint'],array('headers'=>$headers,'body'=>wp_json_encode(array('model'=>$request->getModel(),'prompt'=>$request->getPrompt(),'context'=>$request->getContext(),'structured'=>$request->isStructured())),'timeout'=>60));
  if(is_wp_error($response))throw new \RuntimeException('network_error');
  $statusCode=(int)wp_remote_retrieve_response_code($response);
  if($statusCode>=400)throw new \RuntimeException($this->normalizeError($response)['category']);
  $body=json_decode(wp_remote_retrieve_body($response),true);
  $usage=isset($body['usage'])?array('input_tokens'=>$body['usage']['input_tokens']??null,'output_tokens'=>$body['usage']['output_tokens']??null,'total_tokens'=>$body['usage']['total_tokens']??null,'usage_status'=>'reported'):array('usage_status'=>'unknown');
  if(isset($body['usage']['estimated_cost'])&&is_numeric($body['usage']['estimated_cost'])){$usage['estimated_cost']=(float)$body['usage']['estimated_cost'];$usage['currency']=isset($body['usage']['currency'])?(string)$body['usage']['currency']:null;$usage['pricing_source']=isset($body['usage']['pricing_source'])?(string)$body['usage']['pricing_source']:'provider_reported';}
  return new AIResponse('custom_ai',$request->getModel(),(string)($body['content']??$body['text']??''),$usage,is_array($body)?$body:array());
 }

 public function normalizeError($response): array {
  $code=is_wp_error($response)?'network_error':(int)wp_remote_retrieve_response_code($response);
  if(401===$code||403===$code)return array('category'=>'authentication_error');
  if(404===$code)return array('category'=>'invalid_model_error');
  if(429===$code)return array('category'=>'rate_limit_error');
  if(408===$code||$code>=500)return array('category'=>'provider_unavailable_error');
  return array('category'=>'invalid_request_error');
 }
}