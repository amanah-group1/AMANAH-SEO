<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\AI\AIProviderIdentityResolverInterface;

defined( 'ABSPATH' ) || exit;

final class WordPressAIProviderIdentityResolver implements AIProviderIdentityResolverInterface {

 private $wpdb;

 public function __construct( \wpdb $wpdb ) {
  $this->wpdb = $wpdb;
 }

 public function resolveId( string $provider_key ): ?int {
  $table = $this->wpdb->prefix . 'amanah_seo_ai_providers';
  $id = $this->wpdb->get_var(
   $this->wpdb->prepare(
    "SELECT id FROM {$table} WHERE provider_key = %s LIMIT 1",
    $provider_key
   )
  );

  return null === $id ? null : (int) $id;
 }
}
