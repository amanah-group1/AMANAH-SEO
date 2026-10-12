<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
final class BatchChunker {
 public static function split( array $items, int $chunkSize=50 ): array {
  $chunkSize=max(1,min(1000,$chunkSize)); return array_values(array_chunk($items,$chunkSize));
 }
 public static function count( array $items, int $chunkSize=50 ): int { return count(self::split($items,$chunkSize)); }
}