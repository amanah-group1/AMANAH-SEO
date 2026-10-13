<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

use Amanah\SEOEngine\AI\Provider\ProviderRegistry;

defined( 'ABSPATH' ) || exit;

final class AIModelDiscoveryService {
 private $registry; private $catalog;
 public function __construct(ProviderRegistry $registry,?AIModelCatalogInterface $catalog=null){$this->registry=$registry;$this->catalog=$catalog;}
 public function discover(string $provider,array $credentials): array {
  $adapter=$this->registry->get($provider); if(null===$adapter)throw new \RuntimeException('provider_not_found');
  $validation=$adapter->validateCredentials($credentials); if(empty($validation['valid']))throw new \RuntimeException($validation['category']??'authentication_error');
  $models=$adapter->discoverModels($credentials);
  if(null!==$this->catalog)$this->catalog->syncProvider($provider,$adapter->getProviderName(),$adapter->getCapabilities(),$models);
  return $models;
 }
}
