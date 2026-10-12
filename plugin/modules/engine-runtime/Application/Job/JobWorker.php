<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
final class JobWorker {
 private $repository; private $handlers; private $retryPolicy; private $workerId; private $concurrency; private $costProtection; private $observer;
 public function __construct(JobRepositoryInterface $repository,JobHandlerRegistry $handlers,RetryPolicy $retryPolicy,string $workerId='',?ConcurrencyPolicy $concurrency=null,?AICostProtection $costProtection=null,?JobObserverInterface $observer=null){
  $this->repository=$repository;$this->handlers=$handlers;$this->retryPolicy=$retryPolicy;$this->workerId=''!==$workerId?$workerId:substr(hash('sha256',uniqid('',true)),0,16);$this->concurrency=$concurrency?:new ConcurrencyPolicy();$this->costProtection=$costProtection?:new AICostProtection();$this->observer=$observer?:new NullJobObserver();
 }
 public function runOnce(int $timestamp=0): bool {
  $timestamp=$timestamp>0?$timestamp:time();$this->repository->recoverStale($timestamp);$job=$this->repository->findNextDue($timestamp);if(null===$job)return false;
  $payload=$job->getPayload();$provider=(string)($payload['provider']??'');$activeGlobal=$this->repository->countProcessing();$activeType=$this->repository->countProcessing($job->getType());$activeProvider=''!==$provider?$this->repository->countProcessing('',$provider):0;
  if(!$this->concurrency->canStart($activeGlobal,$activeType,$activeProvider,$job->getType(),$provider)){ $this->observer->blocked($job,'concurrency');return false; }
  try{$this->costProtection->assertAllowed($payload);}catch(JobExecutionException $e){$this->repository->fail($job->getJobId(),$e->getCategory(),$e->getMessage());$this->observer->failed($job,$e->getCategory());return true;}
  $lease=$timestamp+max(30,$job->getTimeout());if(!$this->repository->claim($job->getJobId(),$this->workerId,$lease))return false;$job=$this->repository->findByJobId($job->getJobId());if(null===$job)return false;
  if($job->isCancellationRequested()){$this->repository->cancel($job->getJobId());$this->observer->cancelled($job);return true;}
  $handler=$this->handlers->get($job->getType());if(null===$handler){$this->repository->fail($job->getJobId(),'configuration','No handler is registered for this job type.');$this->observer->failed($job,'configuration');return true;}
  $started=microtime(true);$this->observer->started($job);
  try{
   $result=$handler->handle($job);if(microtime(true)-$started>$job->getTimeout())throw new JobExecutionException('timeout','Job execution timeout exceeded.',true);
   $normalized=new JobExecutionResult(is_array($result)?$result:array());if($normalized->getTotal()>0)$this->repository->updateProgress($job->getJobId(),$normalized->getProcessed(),$normalized->getTotal());
   if($normalized->isPartial())$this->repository->complete($job->getJobId(),array_merge($normalized->all(),array('partial_failure_summary'=>array('failed_count'=>count($normalized->getFailures()),'failures'=>$normalized->getFailures()))));else $this->repository->complete($job->getJobId(),$normalized->all());
   $this->observer->completed($job,$normalized->all());
  }catch(JobExecutionException $e){
   if($this->retryPolicy->shouldRetry($e->getCategory(),$job->getAttemptCount(),$job->getMaxAttempts())&&$e->isRetryable()){$this->repository->scheduleRetry($job->getJobId(),$timestamp+$this->retryPolicy->nextDelay($job->getAttemptCount()+1),$e->getCategory(),$e->getMessage());$this->observer->retryScheduled($job,$e->getCategory());}
   else{$this->repository->fail($job->getJobId(),$e->getCategory(),$e->getMessage());$this->observer->failed($job,$e->getCategory());}
  }catch(\Throwable $e){
   if($this->retryPolicy->shouldRetry('system',$job->getAttemptCount(),$job->getMaxAttempts())){$this->repository->scheduleRetry($job->getJobId(),$timestamp+$this->retryPolicy->nextDelay($job->getAttemptCount()+1),'system','Unhandled job execution failure.');$this->observer->retryScheduled($job,'system');}
   else{$this->repository->fail($job->getJobId(),'system','Unhandled job execution failure.');$this->observer->failed($job,'system');}
  }
  return true;
 }
}