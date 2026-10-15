<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
final class JobRetentionPolicy {
 private $completedDays; private $failedDays; private $cancelledDays;
 public function __construct(int $completedDays=30,int $failedDays=90,int $cancelledDays=30){$this->completedDays=max(1,$completedDays);$this->failedDays=max(1,$failedDays);$this->cancelledDays=max(1,$cancelledDays);}
 public function cutoff(string $status,int $now): int {
  $days=JobContract::FAILED===$status?$this->failedDays:($status===JobContract::CANCELLED?$this->cancelledDays:$this->completedDays);
  return $now-($days*86400);
 }
}