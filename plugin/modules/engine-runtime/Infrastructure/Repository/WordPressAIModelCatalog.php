<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\AI\AIModelCatalogInterface;

defined( 'ABSPATH' ) || exit;

final class WordPressAIModelCatalog implements AIModelCatalogInterface {

 private $wpdb;

 public function __construct( \wpdb $wpdb ) {
  $this->wpdb = $wpdb;
 }

 public function syncProvider(
  string $providerKey,
  string $providerName,
  array $capabilities,
  array $models
 ): void {
  $providersTable = $this->wpdb->prefix . 'amanah_seo_ai_providers';
  $modelsTable = $this->wpdb->prefix . 'amanah_seo_ai_models';
  $now = current_time( 'mysql', true );

  $providerId = $this->wpdb->get_var(
   $this->wpdb->prepare(
    "SELECT id FROM {$providersTable} WHERE provider_key = %s LIMIT 1",
    $providerKey
   )
  );

  $providerData = array(
   'provider_key' => $providerKey,
   'provider_name' => $providerName,
   'capabilities' => wp_json_encode( array_values( $capabilities ) ),
   'status' => 'active',
   'updated_at' => $now,
  );

  if ( null === $providerId ) {
   $providerData['created_at'] = $now;
   $this->wpdb->insert( $providersTable, $providerData );
   $providerId = $this->wpdb->insert_id;
  } else {
   $this->wpdb->update(
    $providersTable,
    $providerData,
    array( 'id' => (int) $providerId )
   );
  }

  if ( ! $providerId ) {
   return;
  }

  $discoveredIds = array();

  foreach ( $models as $model ) {
   $identifier = trim( (string) ( $model['model_identifier'] ?? '' ) );
   if ( '' === $identifier ) {
    continue;
   }

   $displayName = trim( (string) ( $model['display_name'] ?? $identifier ) );
   $modelCapabilities = isset( $model['capabilities'] ) && is_array( $model['capabilities'] )
    ? array_values( $model['capabilities'] )
    : array_values( $capabilities );
   $metadata = isset( $model['metadata'] ) && is_array( $model['metadata'] )
    ? $model['metadata']
    : array();

   $existingId = $this->wpdb->get_var(
    $this->wpdb->prepare(
     "SELECT id FROM {$modelsTable} WHERE provider_id = %d AND model_identifier = %s LIMIT 1",
     (int) $providerId,
     $identifier
    )
   );

   $data = array(
    'provider_id' => (int) $providerId,
    'model_identifier' => $identifier,
    'display_name' => '' !== $displayName ? $displayName : $identifier,
    'capabilities' => wp_json_encode( $modelCapabilities ),
    'availability' => 'available',
    'metadata' => wp_json_encode( $metadata ),
    'last_discovered_at' => $now,
    'updated_at' => $now,
   );

   if ( null === $existingId ) {
    $data['created_at'] = $now;
    $this->wpdb->insert( $modelsTable, $data );
    $existingId = $this->wpdb->insert_id;
   } else {
    $this->wpdb->update( $modelsTable, $data, array( 'id' => (int) $existingId ) );
   }

   if ( $existingId ) {
    $discoveredIds[] = (int) $existingId;
   }
  }

  if ( empty( $discoveredIds ) ) {
   $this->wpdb->query(
    "UPDATE {$modelsTable} SET availability = 'unavailable' WHERE provider_id = " . (int) $providerId
   );
   return;
  }

  $ids = implode( ',', array_map( 'intval', $discoveredIds ) );
  $this->wpdb->query(
   "UPDATE {$modelsTable} SET availability = 'unavailable' WHERE provider_id = " . (int) $providerId . " AND id NOT IN ({$ids})"
  );
 }
 public function getModels(string $providerKey): array {
  $providersTable = $this->wpdb->prefix . 'amanah_seo_ai_providers';
  $modelsTable = $this->wpdb->prefix . 'amanah_seo_ai_models';
  $providerId = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT id FROM {$providersTable} WHERE provider_key = %s LIMIT 1", $providerKey ) );
  if ( null === $providerId ) return array();
  return (array) $this->wpdb->get_results(
   $this->wpdb->prepare(
    "SELECT model_identifier, display_name, capabilities, availability FROM {$modelsTable} WHERE provider_id = %d AND availability = 'available' ORDER BY display_name ASC",
    (int) $providerId
   ),
   ARRAY_A
  );
 }
}
