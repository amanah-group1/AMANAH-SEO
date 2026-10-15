<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

use Amanah\SEOEngine\Infrastructure\Database\WordPressDatabase;

defined( 'ABSPATH' ) || exit;

final class AIUsageRecorder implements AIUsageRecorderInterface {

 private $database;
 private $costCalculator;

 public function __construct( WordPressDatabase $database, ?AICostCalculator $costCalculator = null ) {
  $this->database = $database;
  $this->costCalculator = $costCalculator ?: new AICostCalculator();
 }

 public function record( int $task_id, ?int $provider_id, string $provider, string $model, array $usage ): bool {
  $cost = $this->costCalculator->calculate( $usage );

  $data = array(
   'ai_task_id' => $task_id,
   'provider_id' => $provider_id,
   'model_identifier' => $model,
   'input_tokens' => $usage['input_tokens'] ?? null,
   'output_tokens' => $usage['output_tokens'] ?? null,
   'total_tokens' => $usage['total_tokens'] ?? null,
   'usage_status' => $usage['usage_status'] ?? 'unknown',
   'estimated_cost' => $cost['estimated_cost'],
   'cost_status' => $cost['cost_status'],
   'currency' => $cost['currency'],
   'pricing_source' => $cost['pricing_source'],
   'created_at' => current_time( 'mysql', true ),
  );

  return $this->database->insert( $this->database->getPrefix() . 'amanah_seo_ai_usage', $data );
 }
}
