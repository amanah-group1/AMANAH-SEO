<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\BackgroundJob;
use Amanah\SEOEngine\Application\Job\JobContract;
use Amanah\SEOEngine\Application\Job\JobRetentionPolicy;
defined( 'ABSPATH' ) || exit;
final class JobCleanup {
 private $wpdb; private $policy;
 public function __construct(\wpdb $wpdb,?JobRetentionPolicy $policy=null){$this->wpdb=$wpdb;$this->policy=$policy?:new JobRetentionPolicy();}
 public function cleanup(int $now=0): int {
  $now=$now>0?$now:time();$count=0;$table=$this->wpdb->prefix.'amanah_seo_jobs';
  foreach(array(JobContract::COMPLETED,JobContract::FAILED,JobContract::CANCELLED) as $status){$cut=gmdate('Y-m-d H:i:s',$this->policy->cutoff($status,$now));$count+=(int)$this->wpdb->query($this->wpdb->prepare("DELETE FROM {$table} WHERE status=%s AND completed_at IS NOT NULL AND completed_at<%s",$status,$cut));}
  return $count;
 }
}