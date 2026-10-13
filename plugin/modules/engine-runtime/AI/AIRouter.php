<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

use Amanah\SEOEngine\AI\Provider\ProviderRegistry;

defined( 'ABSPATH' ) || exit;

final class AIRouter {
 private $registry;
 public function __construct( ProviderRegistry $registry ) { $this->registry=$registry; }
 public function candidates( string $provider, array $fallback=array() ): array {
  $result=array();
  foreach(array_merge(array($provider),$fallback) as $candidate){$candidate=(string)$candidate;if(''!==$candidate&&!in_array($candidate,$result,true))$result[]=$candidate;}
  return $result;
 }
 public function select( string $provider, string $model, array $required_capabilities, array $fallback=array() ): array {
  foreach($this->candidates($provider,$fallback) as $candidate){
   $adapter=$this->registry->get($candidate);
   if(null===$adapter)continue;
   if(!array_diff($required_capabilities,$adapter->getCapabilities()))return array('provider'=>$adapter,'provider_key'=>$candidate,'model'=>$model);
  }
  throw new \RuntimeException('capability_error');
 }
}
