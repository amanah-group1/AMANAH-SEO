<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Tests\Unit;
use Amanah\SEOEngine\Application\Job\AICostProtection;
use Amanah\SEOEngine\Application\Job\BatchChunker;
use Amanah\SEOEngine\Application\Job\ConcurrencyPolicy;
use Amanah\SEOEngine\Application\Job\JobContract;
use Amanah\SEOEngine\Application\Job\JobExecutionException;
use Amanah\SEOEngine\Application\Job\JobHandlerInterface;
use Amanah\SEOEngine\Application\Job\JobHandlerRegistry;
use Amanah\SEOEngine\Application\Job\JobManager;
use Amanah\SEOEngine\Application\Job\JobRecord;
use Amanah\SEOEngine\Application\Job\JobRepositoryInterface;
use Amanah\SEOEngine\Application\Job\RetryPolicy;
use Amanah\SEOEngine\Application\Port\QueueAdapterInterface;
use PHPUnit\Framework\TestCase;
final class Phase8BackgroundJobsTest extends TestCase {
 private function repo($existing=null){return new class($existing) implements JobRepositoryInterface{private $existing;public function __construct($e){$this->existing=$e;}public function create(array $job):JobRecord{return new JobRecord($job);}public function findByJobId(string $id):?JobRecord{return null;}public function findByIdempotencyKey(string $key):?JobRecord{return $this->existing;}public function findNextDue(int $t):?JobRecord{return null;}public function countProcessing(string $type='',string $provider=''):int{return 0;}public function claim(string $id,string $w,int $l):bool{return false;}public function complete(string $id,array $r=array()):void{}public function fail(string $id,string $c,string $m,array $d=array()):void{}public function scheduleRetry(string $id,int $t,string $c,string $m):void{}public function cancel(string $id):bool{return false;}public function requestCancellation(string $id):bool{return false;}public function updateProgress(string $id,int $p,int $t):void{}public function recoverStale(int $t):int{return 0;}};}
 public function test_retry_policy_is_bounded_and_transient_only():void{$p=new RetryPolicy(10,25);$this->assertTrue($p->shouldRetry('timeout',1,3));$this->assertFalse($p->shouldRetry('authentication_error',1,3));$this->assertFalse($p->shouldRetry('timeout',3,3));$this->assertSame(20,$p->nextDelay(2));$this->assertSame(25,$p->nextDelay(5));}
 public function test_contract_and_priority_lifecycle_are_stable():void{$this->assertSame(array('created','queued','processing','completed','failed','retry_scheduled','cancelled','stale','blocked'),JobContract::states());$this->assertSame('high',JobContract::HIGH);}
 public function test_payload_secret_and_idempotency_protection():void{$existing=new JobRecord(array('job_id'=>'existing','job_type'=>'test','status'=>JobContract::QUEUED));$queue=new class implements QueueAdapterInterface{public function enqueue(JobRecord $j):bool{return true;}public function cancel(string $id):bool{return true;}};$m=new JobManager($this->repo($existing),$queue);$this->assertSame('existing',$m->enqueue('test',array(),'idem')->getJobId());$this->expectException(\InvalidArgumentException::class);$m->enqueue('test',array('api_key'=>'secret'),'new');}
 public function test_batch_chunking_is_bounded_and_repeatable():void{$chunks=BatchChunker::split(range(1,105),50);$this->assertCount(3,$chunks);$this->assertCount(50,$chunks[0]);$this->assertSame(3,BatchChunker::count(range(1,105),50));}
 public function test_concurrency_policy_blocks_global_type_and_provider_limits():void{$p=new ConcurrencyPolicy(2,array('ai'=>1),array('openai'=>1));$this->assertTrue($p->canStart(1,0,0,'ai','openai'));$this->assertFalse($p->canStart(1,1,0,'ai','openai'));$this->assertFalse($p->canStart(2,0,0,'other',''));$this->assertFalse($p->canStart(0,0,1,'ai','openai'));}
 public function test_ai_cost_protection_blocks_over_limit_and_unknown_when_required():void{$g=new AICostProtection();$g->assertAllowed(array('provider'=>'openai','estimated_cost'=>1.0,'max_cost'=>2.0));$this->expectException(JobExecutionException::class);$g->assertAllowed(array('provider'=>'openai','estimated_cost'=>3.0,'max_cost'=>2.0));}
 public function test_ai_cost_protection_can_require_known_cost():void{$g=new AICostProtection();$this->expectException(JobExecutionException::class);$g->assertAllowed(array('ai_job'=>true,'require_known_cost'=>true));}
 public function test_handler_registry_remains_application_boundary():void{$r=new JobHandlerRegistry();$h=new class implements JobHandlerInterface{public function getJobType():string{return'test_job';}public function handle(JobRecord $j):array{return array('success'=>true);}};$r->register($h);$this->assertSame($h,$r->get('test_job'));$this->assertNull($r->get('missing'));}
 public function test_execution_exception_carries_structured_retry_category():void{$e=new JobExecutionException('rate_limit_error','temporary limit',true);$this->assertSame('rate_limit_error',$e->getCategory());$this->assertTrue($e->isRetryable());}
 public function test_queue_adapter_is_replaceable_by_contract():void{$q=new class implements QueueAdapterInterface{public function enqueue(JobRecord $j):bool{return JobContract::QUEUED===$j->getStatus();}public function cancel(string $id):bool{return true;}};$this->assertTrue($q->enqueue(new JobRecord(array('status'=>JobContract::QUEUED))));}
}