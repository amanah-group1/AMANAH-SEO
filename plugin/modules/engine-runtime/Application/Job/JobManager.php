<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
use Amanah\SEOEngine\Application\Port\QueueAdapterInterface;
use Amanah\SEOEngine\Application\Security\SecretFieldPolicy;
defined( 'ABSPATH' ) || exit;
final class JobManager {
 private $repository; private $queue;
 public function __construct(JobRepositoryInterface $repository,QueueAdapterInterface $queue){$this->repository=$repository;$this->queue=$queue;}
 public function enqueue(string $jobType,array $payload,string $idempotencyKey,string $priority=JobContract::NORMAL,int $delay=0,int $timeout=300,int $maxAttempts=3): JobRecord {
  if(!in_array($priority,array(JobContract::HIGH,JobContract::NORMAL,JobContract::LOW),true))throw new \InvalidArgumentException('Invalid job priority.');
  if(''===trim($idempotencyKey))throw new \InvalidArgumentException('Idempotency key is required.');
  $payload=$this->validatePayload($payload);$existing=$this->repository->findByIdempotencyKey($idempotencyKey);if(null!==$existing)return $existing;
  $scheduledAt=time()+max(0,$delay);$record=$this->repository->create(array('job_id'=>substr(hash('sha256',$jobType.'|'.$idempotencyKey.'|'.microtime(true)),0,32),'job_type'=>$jobType,'status'=>JobContract::QUEUED,'priority'=>$priority,'payload'=>$payload,'attempt_count'=>0,'max_attempts'=>max(1,$maxAttempts),'scheduled_at'=>$scheduledAt,'timeout_seconds'=>max(1,$timeout),'idempotency_key'=>$idempotencyKey,'cancellation_requested'=>0));
  $this->queue->enqueue($record);return $record;
 }
 public function enqueueBatch(string $jobType,array $items,string $idempotencyPrefix,int $chunkSize=50,string $priority=JobContract::NORMAL): array {
  $chunks=BatchChunker::split($items,$chunkSize);$jobs=array();
  foreach($chunks as $index=>$chunk){$jobs[]=$this->enqueue($jobType,array('items'=>$chunk,'chunk_index'=>$index,'chunk_count'=>count($chunks),'batch'=>true),$idempotencyPrefix.':'.$index,$priority);}
  return $jobs;
 }
 public function cancel(string $jobId): bool{return $this->repository->cancel($jobId);}
 public function requestCancellation(string $jobId): bool{return $this->repository->requestCancellation($jobId);}
 private function validatePayload(array $payload): array {
  foreach($payload as $key=>$value){if(SecretFieldPolicy::isSensitiveKey((string)$key))throw new \InvalidArgumentException('Secrets are not allowed in job payload.');if(is_array($value))$this->validatePayload($value);}
  return $payload;
 }
}