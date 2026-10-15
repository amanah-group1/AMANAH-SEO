<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
final class JobRecord {
 private $data;
 public function __construct(array $data){$this->data=$data;}
 public function get(string $key,$default=null){return array_key_exists($key,$this->data)?$this->data[$key]:$default;}
 public function all():array{return $this->data;}
 public function getJobId():string{return(string)$this->get('job_id');}
 public function getType():string{return(string)$this->get('job_type');}
 public function getStatus():string{return(string)$this->get('status');}
 public function getPriority():string{return(string)$this->get('priority',JobContract::NORMAL);}
 public function getPayload():array{$p=$this->get('payload',array());return is_array($p)?$p:array();}
 public function getAttemptCount():int{return(int)$this->get('attempt_count',0);}
 public function getMaxAttempts():int{return(int)$this->get('max_attempts',3);}
 public function getTimeout():int{return(int)$this->get('timeout_seconds',300);}
 public function getIdempotencyKey():string{return(string)$this->get('idempotency_key');}
 public function isCancellationRequested():bool{return!empty($this->get('cancellation_requested',false));}
}