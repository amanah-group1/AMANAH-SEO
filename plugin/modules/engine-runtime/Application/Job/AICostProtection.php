<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
final class AICostProtection {
 public function assertAllowed( array $payload ): void {
  if ( empty($payload['ai_job']) && empty($payload['provider']) && empty($payload['model']) ) return;
  $max=isset($payload['max_cost'])?(float)$payload['max_cost']:null;
  $cost=isset($payload['estimated_cost'])?(float)$payload['estimated_cost']:null;
  if ( null!==$max && $max<0 ) throw new JobExecutionException('cost_limit','Invalid maximum AI cost.',false);
  if ( null!==$cost && null!==$max && $cost>$max ) throw new JobExecutionException('cost_limit','Estimated AI cost exceeds job limit.',false);
  if ( true===(bool)($payload['require_known_cost']??false) && null===$cost ) throw new JobExecutionException('cost_unknown','AI cost is unknown and this job requires a known cost.',false);
 }
}