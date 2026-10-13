<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

final class AICostCalculator {

 public function calculate( array $usage ): array {
  if ( isset( $usage['estimated_cost'] ) && is_numeric( $usage['estimated_cost'] ) ) {
   return array(
    'estimated_cost' => (float) $usage['estimated_cost'],
    'currency' => isset( $usage['currency'] ) ? (string) $usage['currency'] : null,
    'pricing_source' => isset( $usage['pricing_source'] ) ? (string) $usage['pricing_source'] : 'provider_reported',
    'cost_status' => 'reported',
   );
  }

  $inputTokens = $usage['input_tokens'] ?? null;
  $outputTokens = $usage['output_tokens'] ?? null;
  $inputRate = $usage['input_cost_per_1k'] ?? null;
  $outputRate = $usage['output_cost_per_1k'] ?? null;
  $currency = isset( $usage['currency'] ) ? trim( (string) $usage['currency'] ) : '';

  if (
   is_numeric( $inputTokens ) &&
   is_numeric( $outputTokens ) &&
   is_numeric( $inputRate ) &&
   is_numeric( $outputRate ) &&
   (float) $inputTokens >= 0 &&
   (float) $outputTokens >= 0 &&
   (float) $inputRate >= 0 &&
   (float) $outputRate >= 0 &&
   '' !== $currency
  ) {
   $cost = (
    ( (float) $inputTokens / 1000 ) * (float) $inputRate
   ) + (
    ( (float) $outputTokens / 1000 ) * (float) $outputRate
   );

   return array(
    'estimated_cost' => round( $cost, 8 ),
    'currency' => $currency,
    'pricing_source' => isset( $usage['pricing_source'] ) ? (string) $usage['pricing_source'] : 'supplied',
    'cost_status' => 'calculated',
   );
  }

  return array(
   'estimated_cost' => null,
   'currency' => '' !== $currency ? $currency : null,
   'pricing_source' => null,
   'cost_status' => 'unknown',
  );
 }
}
