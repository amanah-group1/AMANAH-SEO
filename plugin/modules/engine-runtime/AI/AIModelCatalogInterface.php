<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

interface AIModelCatalogInterface {
 public function syncProvider(string $providerKey,string $providerName,array $capabilities,array $models): void;
 public function getModels(string $providerKey): array;
}
