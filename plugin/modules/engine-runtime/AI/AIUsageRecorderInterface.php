<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

interface AIUsageRecorderInterface {
 public function record( int $task_id, ?int $provider_id, string $provider, string $model, array $usage ): bool;
}
