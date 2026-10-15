<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\BackgroundJob;
use Amanah\SEOEngine\Application\Job\JobRecord;
use Amanah\SEOEngine\Application\Port\QueueAdapterInterface;
use Amanah\SEOEngine\Application\Port\SchedulerInterface;
defined( 'ABSPATH' ) || exit;
final class WordPressQueueAdapter implements QueueAdapterInterface {
 private $scheduler;
 public function __construct(SchedulerInterface $scheduler){$this->scheduler=$scheduler;}
 public function enqueue(JobRecord $job): bool{return $this->scheduler->schedule('amanah_seo_engine_run_jobs',(int)$job->get('scheduled_at',time()),array());}
 public function cancel(string $jobId): bool{return true;}
}