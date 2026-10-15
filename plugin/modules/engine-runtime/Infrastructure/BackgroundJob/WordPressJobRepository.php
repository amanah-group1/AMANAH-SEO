<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\BackgroundJob;
use Amanah\SEOEngine\Application\Job\JobContract;
use Amanah\SEOEngine\Application\Job\JobRecord;
use Amanah\SEOEngine\Application\Job\JobRepositoryInterface;
defined( 'ABSPATH' ) || exit;
final class WordPressJobRepository implements JobRepositoryInterface {
 private $wpdb; private $table;
 public function __construct(\wpdb $wpdb){$this->wpdb=$wpdb;$this->table=$wpdb->prefix.'amanah_seo_jobs';}
 public function create(array $job):JobRecord{
  $now=gmdate('Y-m-d H:i:s');$data=array('job_id'=>$job['job_id'],'job_type'=>$job['job_type'],'status'=>$job['status'],'priority'=>$job['priority'],'payload'=>wp_json_encode($job['payload']),'attempt_count'=>0,'max_attempts'=>$job['max_attempts'],'scheduled_at'=>gmdate('Y-m-d H:i:s',$job['scheduled_at']),'timeout_seconds'=>$job['timeout_seconds'],'idempotency_key'=>$job['idempotency_key'],'cancellation_requested'=>0,'created_at'=>$now,'updated_at'=>$now);
  $this->wpdb->insert($this->table,$data);return $this->findByJobId($job['job_id']);
 }
 public function findByJobId(string $jobId):?JobRecord{$row=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE job_id=%s LIMIT 1",$jobId),ARRAY_A);return$row?$this->hydrate($row):null;}
 public function findByIdempotencyKey(string $key):?JobRecord{$row=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE idempotency_key=%s LIMIT 1",$key),ARRAY_A);return$row?$this->hydrate($row):null;}
 public function findNextDue(int $timestamp):?JobRecord{$now=gmdate('Y-m-d H:i:s',$timestamp);$row=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE status IN (%s,%s) AND scheduled_at<=%s AND cancellation_requested=0 ORDER BY FIELD(priority,%s,%s,%s),scheduled_at ASC,id ASC LIMIT 1",JobContract::QUEUED,JobContract::RETRY_SCHEDULED,$now,JobContract::HIGH,JobContract::NORMAL,JobContract::LOW),ARRAY_A);return$row?$this->hydrate($row):null;}
 public function countProcessing(string $jobType='',string $provider=''):int{
  $where="status=%s";$args=array(JobContract::PROCESSING);
  if(''!==$jobType){$where.=" AND job_type=%s";$args[]=$jobType;}
  if(''!==$provider){$where.=" AND JSON_UNQUOTE(JSON_EXTRACT(payload,'$.provider'))=%s";$args[]=$provider;}
  return(int)$this->wpdb->get_var($this->wpdb->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$where}",...$args));
 }
 public function claim(string $jobId,string $workerId,int $leaseExpiresAt):bool{
  $now=gmdate('Y-m-d H:i:s');$lease=gmdate('Y-m-d H:i:s',$leaseExpiresAt);$result=$this->wpdb->query($this->wpdb->prepare("UPDATE {$this->table} SET status=%s,attempt_count=attempt_count+1,started_at=%s,worker_id=%s,lock_acquired_at=%s,lease_expires_at=%s,updated_at=%s WHERE job_id=%s AND status IN (%s,%s) AND cancellation_requested=0 AND (lease_expires_at IS NULL OR lease_expires_at<%s)",JobContract::PROCESSING,$now,$workerId,$now,$lease,$now,$jobId,JobContract::QUEUED,JobContract::RETRY_SCHEDULED,$now));return 1===(int)$result;
 }
 public function complete(string $jobId,array $result=array()):void{$now=gmdate('Y-m-d H:i:s');$this->wpdb->update($this->table,array('status'=>JobContract::COMPLETED,'result_data'=>wp_json_encode($result),'completed_at'=>$now,'updated_at'=>$now,'worker_id'=>null,'lock_acquired_at'=>null,'lease_expires_at'=>null),array('job_id'=>$jobId));}
 public function fail(string $jobId,string $category,string $message,array $details=array()):void{$now=gmdate('Y-m-d H:i:s');$this->wpdb->update($this->table,array('status'=>JobContract::FAILED,'result_data'=>wp_json_encode(array('failure_summary'=>$details)),'error_category'=>sanitize_key($category),'error_message'=>wp_strip_all_tags($message),'completed_at'=>$now,'updated_at'=>$now,'worker_id'=>null,'lock_acquired_at'=>null,'lease_expires_at'=>null),array('job_id'=>$jobId));}
 public function scheduleRetry(string $jobId,int $timestamp,string $category,string $message):void{$now=gmdate('Y-m-d H:i:s');$this->wpdb->update($this->table,array('status'=>JobContract::RETRY_SCHEDULED,'scheduled_at'=>gmdate('Y-m-d H:i:s',$timestamp),'error_category'=>sanitize_key($category),'error_message'=>wp_strip_all_tags($message),'updated_at'=>$now,'worker_id'=>null,'lock_acquired_at'=>null,'lease_expires_at'=>null),array('job_id'=>$jobId));}
 public function cancel(string $jobId):bool{$result=$this->wpdb->query($this->wpdb->prepare("UPDATE {$this->table} SET status=%s,cancellation_requested=1,updated_at=%s WHERE job_id=%s AND status IN (%s,%s,%s,%s)",JobContract::CANCELLED,gmdate('Y-m-d H:i:s'),$jobId,JobContract::CREATED,JobContract::QUEUED,JobContract::RETRY_SCHEDULED,JobContract::BLOCKED));return 1===(int)$result;}
 public function requestCancellation(string $jobId):bool{$result=$this->wpdb->query($this->wpdb->prepare("UPDATE {$this->table} SET cancellation_requested=1,updated_at=%s WHERE job_id=%s AND status=%s",gmdate('Y-m-d H:i:s'),$jobId,JobContract::PROCESSING));return 1===(int)$result;}
 public function updateProgress(string $jobId,int $processed,int $total):void{$total=max(0,$total);$processed=max(0,min($processed,$total));$percent=$total>0?round(($processed/$total)*100,2):0;$this->wpdb->update($this->table,array('progress_processed'=>$processed,'progress_total'=>$total,'progress_percent'=>$percent,'updated_at'=>gmdate('Y-m-d H:i:s')),array('job_id'=>$jobId));}
 public function recoverStale(int $timestamp):int{$now=gmdate('Y-m-d H:i:s',$timestamp);return(int)$this->wpdb->query($this->wpdb->prepare("UPDATE {$this->table} SET status=CASE WHEN attempt_count<max_attempts THEN %s ELSE %s END,scheduled_at=%s,worker_id=NULL,lock_acquired_at=NULL,lease_expires_at=NULL,updated_at=%s WHERE status=%s AND lease_expires_at IS NOT NULL AND lease_expires_at<%s",JobContract::QUEUED,JobContract::FAILED,$now,$now,JobContract::PROCESSING,$now));}
 private function hydrate(array $row):JobRecord{$row['payload']=json_decode((string)$row['payload'],true);$row['payload']=is_array($row['payload'])?$row['payload']:array();$row['cancellation_requested']=(int)$row['cancellation_requested'];return new JobRecord($row);}
}