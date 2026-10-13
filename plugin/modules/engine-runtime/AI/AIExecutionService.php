<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

final class AIExecutionService {
 private $engine; private $maxAttempts; private $tasks; private $resolver;
 public function __construct(AIEngine $engine,int $maxAttempts=2,?AIExecutionTaskRepositoryInterface $tasks=null,?AIProviderIdentityResolverInterface $resolver=null){$this->engine=$engine;$this->maxAttempts=max(1,$maxAttempts);$this->tasks=$tasks;$this->resolver=$resolver;}
 public function executeTask(AIRequest $request,string $provider,array $credentials,array $requiredCapabilities=array(),array $fallback=array(),int $taskId=0): AIExecutionResult {
  $created=false; $attempts=0; $lastError=null;
  if(0===$taskId&&null!==$this->tasks){$providerId=null!==$this->resolver?$this->resolver->resolveId($provider):null;$taskId=$this->tasks->create(substr(hash('sha256',$request->getTaskType().'|'.microtime(true)),0,32),$request->getTaskType(),$providerId,$request->getModel(),$request->getContext());$created=$taskId>0;}
  while($attempts<$this->maxAttempts){++$attempts;try{$response=$this->engine->execute($request,$provider,$credentials,$requiredCapabilities,$fallback,$taskId);if($created)$this->tasks->complete($taskId,'completed',array('provider'=>$response->getProvider(),'model'=>$response->getModel()));return new AIExecutionResult('completed',$response,null,$attempts,$response->getProvider());}catch(\Throwable $e){$lastError=$e->getMessage();if(!in_array($lastError,array('timeout','network_error','provider_unavailable_error','rate_limit_error','system'),true))break;}}
  if($created)$this->tasks->complete($taskId,'failed',array(),$lastError?:'system');
  return new AIExecutionResult('failed',null,$lastError?:'system',$attempts,null);
 }
}
