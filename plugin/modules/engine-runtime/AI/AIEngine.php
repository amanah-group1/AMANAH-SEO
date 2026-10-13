<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

final class AIEngine {
 private $router;
 private $context;
 private $structured_validator;
 private $usage_recorder;
 private $provider_identity_resolver;
 private $max_attempts;
 private $max_revisions;

 public function __construct(
  AIRouter $router,
  AIContextAssembler $context,
  ?AIStructuredValidator $structured_validator=null,
  ?AIUsageRecorderInterface $usage_recorder=null,
  ?AIProviderIdentityResolverInterface $provider_identity_resolver=null,
  int $max_attempts=2,
  int $max_revisions=1
 ) {
  $this->router=$router; $this->context=$context; $this->structured_validator=$structured_validator?:new AIStructuredValidator();
  $this->usage_recorder=$usage_recorder; $this->provider_identity_resolver=$provider_identity_resolver;
  $this->max_attempts=max(1,$max_attempts); $this->max_revisions=max(0,$max_revisions);
 }

 public function execute(AIRequest $request,string $provider,array $credentials,array $required_capabilities=array(),array $fallback=array(),int $task_id=0): AIResponse {
  $last=null;
  foreach($this->router->candidates($provider,$fallback) as $candidate){
   try{$selected=$this->router->select($candidate,$request->getModel(),$required_capabilities);}catch(\RuntimeException $e){$last=$e->getMessage();continue;}
   $adapter=$selected['provider'];
   $candidateCredentials=$this->credentialsForProvider($credentials,$candidate);
   $validation=$adapter->validateCredentials($candidateCredentials);
   if(empty($validation['valid'])){$last=$validation['category']??'authentication_error';continue;}
   for($attempt=1;$attempt<=$this->max_attempts;$attempt++){
    try{
     $response=$this->generate($request,$adapter,$candidateCredentials,$task_id);
     return $response;
    }catch(\RuntimeException $e){
     $last=$e->getMessage();
     if(!in_array($last,array('timeout','network_error','provider_unavailable_error','rate_limit_error','system'),true))break;
    }catch(\Throwable $e){$last='system';break;}
   }
  }
  throw new \RuntimeException($last?:'ai_unavailable');
 }

 private function generate(AIRequest $request,$adapter,array $credentials,int $task_id): AIResponse {
  $context=$this->context->assemble(array_keys($request->getContext()),$request->getContext());
  $currentPrompt=$request->getPrompt();
  $revision=0;
  while(true){
   $response=$adapter->generate(new AIRequest($request->getTaskType(),$request->getModel(),$currentPrompt,$context,$request->isStructured(),$request->getRequiredKeys()),$credentials);$this->recordUsage($task_id,$response);
   if($request->isStructured()){
    $validation=$this->structured_validator->validate($response->getContent(),$request->getRequiredKeys());
    if(empty($validation['valid'])){
     if($revision<$this->max_revisions){
      ++$revision;
      $currentPrompt=$request->getPrompt()."\n\nREVISION REQUIRED: Return valid JSON containing these required keys: ".implode(', ',$request->getRequiredKeys()).". Do not add commentary.";
      continue;
     }
     throw new \RuntimeException('response_validation_error');
    }
   }
   return $response;
  }
 }

 private function recordUsage(int $task_id,AIResponse $response): void { if($task_id<=0||null===$this->usage_recorder)return; $providerId=null!==$this->provider_identity_resolver?$this->provider_identity_resolver->resolveId($response->getProvider()):null; $this->usage_recorder->record($task_id,$providerId,$response->getProvider(),$response->getModel(),$response->getUsage()); }

 private function credentialsForProvider(array $credentials,string $provider): array {
  if(isset($credentials[$provider])&&is_array($credentials[$provider]))return $credentials[$provider];
  return $credentials;
 }
}
