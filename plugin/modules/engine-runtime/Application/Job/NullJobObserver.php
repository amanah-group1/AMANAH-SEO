<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
final class NullJobObserver implements JobObserverInterface {
 public function started(JobRecord $job):void{}
 public function completed(JobRecord $job,array $result):void{}
 public function failed(JobRecord $job,string $category):void{}
 public function retryScheduled(JobRecord $job,string $category):void{}
 public function blocked(JobRecord $job,string $reason):void{}
 public function cancelled(JobRecord $job):void{}
}