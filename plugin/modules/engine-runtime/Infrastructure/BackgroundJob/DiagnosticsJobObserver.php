<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\BackgroundJob;
use Amanah\SEOEngine\Application\Job\JobObserverInterface;
use Amanah\SEOEngine\Application\Job\JobRecord;
use Amanah\SEOEngine\Infrastructure\Diagnostics\Diagnostics;
defined( 'ABSPATH' ) || exit;
final class DiagnosticsJobObserver implements JobObserverInterface {
 private $diagnostics;
 public function __construct(Diagnostics $diagnostics){$this->diagnostics=$diagnostics;}
 public function started(JobRecord $job):void{$this->diagnostics->info('job_started','Background job started',array('job_id'=>$job->getJobId(),'job_type'=>$job->getType()));}
 public function completed(JobRecord $job,array $result):void{$this->diagnostics->info('job_completed','Background job completed',array('job_id'=>$job->getJobId(),'job_type'=>$job->getType(),'partial'=>!empty($result['partial'])));}
 public function failed(JobRecord $job,string $category):void{$this->diagnostics->error('job_failed','Background job failed',array('job_id'=>$job->getJobId(),'job_type'=>$job->getType(),'category'=>$category));}
 public function retryScheduled(JobRecord $job,string $category):void{$this->diagnostics->warning('job_retry_scheduled','Background job retry scheduled',array('job_id'=>$job->getJobId(),'category'=>$category));}
 public function blocked(JobRecord $job,string $reason):void{$this->diagnostics->warning('job_blocked','Background job blocked',array('job_id'=>$job->getJobId(),'reason'=>$reason));}
 public function cancelled(JobRecord $job):void{$this->diagnostics->info('job_cancelled','Background job cancelled',array('job_id'=>$job->getJobId()));}
}