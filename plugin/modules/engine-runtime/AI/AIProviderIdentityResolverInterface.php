<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

interface AIProviderIdentityResolverInterface {

 public function resolveId( string $provider_key ): ?int;
}
