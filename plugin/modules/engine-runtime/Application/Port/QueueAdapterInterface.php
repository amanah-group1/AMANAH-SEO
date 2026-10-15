<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Port;
use Amanah\SEOEngine\Application\Job\JobRecord;
defined( 'ABSPATH' ) || exit;
interface QueueAdapterInterface {
 public function enqueue(JobRecord $job): bool;
 public function cancel(string $jobId): bool;
}