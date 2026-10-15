<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Portability;

use Amanah\SEOEngine\Application\Portability\PortabilityFormat;
use Amanah\SEOEngine\Application\Portability\PortabilityRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class WordPressPortabilityRepository implements PortabilityRepositoryInterface {

 private $wpdb;

 private const TABLES = array(
  PortabilityFormat::SCOPE_SEO => array(
   'search_intents',
   'clusters',
   'owner_urls',
   'keywords',
   'content_strategies',
   'content_briefs',
   'page_blueprints',
   'content',
   'quality_results',
   'internal_link_opportunities',
   'cannibalization',
   'ai_providers',
   'ai_models',
   'ai_tasks',
   'ai_usage',
  ),
  PortabilityFormat::SCOPE_DIAGNOSTICS => array( 'diagnostics' ),
  PortabilityFormat::SCOPE_JOBS => array( 'jobs' ),
 );

 private const IMPORT_ORDER = array(
  'configuration',
  'search_intents',
  'clusters',
  'owner_urls',
  'keywords',
  'content_strategies',
  'content_briefs',
  'page_blueprints',
  'content',
  'quality_results',
  'internal_link_opportunities',
  'cannibalization',
  'ai_providers',
  'ai_models',
  'ai_tasks',
  'ai_usage',
  'diagnostics',
  'jobs',
 );

 private const REFERENCE_FIELDS = array(
  'search_intent_id' => 'search_intents',
  'cluster_id' => 'clusters',
  'owner_url_id' => 'owner_urls',
  'keyword_id' => 'keywords',
  'strategy_id' => 'content_strategies',
  'content_brief_id' => 'content_briefs',
  'blueprint_id' => 'page_blueprints',
  'content_id' => 'content',
  'source_url_id' => 'owner_urls',
  'target_url_id' => 'owner_urls',
  'owner_url_a_id' => 'owner_urls',
  'owner_url_b_id' => 'owner_urls',
  'provider_id' => 'ai_providers',
  'ai_task_id' => 'ai_tasks',
 );

 public function __construct( \wpdb $wpdb ) {
  $this->wpdb = $wpdb;
 }

 public function export( array $scopes ): array {
  $result = array();

  foreach ( $scopes as $scope ) {
   if ( PortabilityFormat::SCOPE_CONFIGURATION === $scope ) {
    $result['configuration'] = $this->configuration();
    continue;
   }

   foreach ( self::TABLES[ $scope ] as $suffix ) {
    $table = $this->table( $suffix );
    $rows = $this->wpdb->get_results( "SELECT * FROM {$table}", ARRAY_A );
    $result[ $suffix ] = is_array( $rows ) ? $rows : array();
   }
  }

  return $result;
 }

 public function previewImport( array $document, array $resolutions = array() ): array {
  $summary = array(
   'new_records' => 0,
   'updated_records' => 0,
   'conflicts' => 0,
   'rejected_records' => 0,
   'skipped_records' => 0,
   'manual_resolution_required' => 0,
  );

  foreach ( $this->orderedRows( $document['records'] ) as $item ) {
   $suffix = $item['table'];
   $row = $item['row'];

   if ( 'configuration' === $suffix ) {
    if ( is_array( $row ) ) {
     $summary['updated_records']++;
    } else {
     $summary['rejected_records']++;
    }
    continue;
   }

   if ( ! is_array( $row ) || empty( $row ) ) {
    $summary['rejected_records']++;
    continue;
   }

   $existing = $this->findExisting( $suffix, $row );
   if ( null === $existing ) {
    $summary['new_records']++;
    continue;
   }

   $summary['conflicts']++;
   $resolution = $this->resolutionFor( $suffix, $row, $resolutions );

   if ( 'use_imported' === $resolution ) {
    $summary['updated_records']++;
   } elseif ( 'resolve_manually' === $resolution ) {
    $summary['manual_resolution_required']++;
   } else {
    $summary['skipped_records']++;
   }
  }

  return $summary;
 }

 public function import(
  array $document,
  string $mode = 'merge',
  array $resolutions = array()
 ): array {
  $preview = $this->previewImport( $document, $resolutions );
  $totalRecords = count( $this->orderedRows( $document['records'] ) );
  $operationId = substr( hash( 'sha256', wp_json_encode( $document ) . '|' . microtime( true ) ), 0, 32 );
  $this->startOperation( $operationId, 'import', $document['data_scope'], $preview );

  $imported = 0;
  $rejected = $preview['rejected_records'];
  $skipped = $preview['skipped_records'];
  $mapping = array();
  $started = false;

  $this->wpdb->query( 'START TRANSACTION' );
  $started = true;

  try {
   if ( 'replace' === $mode ) {
    $this->clearScope( $document['data_scope'] );
   }

   foreach ( $this->orderedRows( $document['records'] ) as $item ) {
    $suffix = $item['table'];
    $row = $item['row'];

    if ( 'configuration' === $suffix ) {
     if ( is_array( $row ) ) {
      $this->importConfiguration( $row );
      $imported++;
     } else {
      $rejected++;
     }
     continue;
    }

    if ( ! is_array( $row ) || empty( $row ) || ! $this->isAllowedTable( $suffix ) ) {
     $rejected++;
     continue;
    }

    $sourceId = isset( $row['id'] ) ? (int) $row['id'] : 0;
    $clean = $this->remapReferences( $this->sanitizeRow( $row ), $mapping );
    $existing = $this->findExisting( $suffix, $clean );
    $resolution = $this->resolutionFor( $suffix, $row, $resolutions );

    if ( 'update_existing' === $mode && null === $existing ) {
     $skipped++;
     continue;
    }

    if ( $existing !== null && 'replace' !== $mode ) {
     if ( 'skip' === $resolution || 'keep_local' === $resolution ) {
      $skipped++;
      if ( $sourceId > 0 ) {
       $mapping[ $suffix ][ $sourceId ] = (int) $existing['id'];
      }
      continue;
     }

     if ( 'resolve_manually' === $resolution ) {
      $rejected++;
      continue;
     }

     if ( 'create_only' === $mode ) {
      $skipped++;
      if ( $sourceId > 0 ) {
       $mapping[ $suffix ][ $sourceId ] = (int) $existing['id'];
      }
      continue;
     }

     if ( 'update_existing' !== $mode && 'merge' !== $mode && 'use_imported' !== $resolution ) {
      $skipped++;
      continue;
     }
    }

    if ( $existing !== null && 'replace' !== $mode ) {
     $localId = (int) $existing['id'];
     unset( $clean['id'] );
     $updated = $this->wpdb->update(
      $this->table( $suffix ),
      $clean,
      array( 'id' => $localId ),
      $this->formats( $clean ),
      array( '%d' )
     );

     if ( false === $updated ) {
      throw new \RuntimeException( 'Import database operation failed.' );
     }
    } else {
     $localId = $this->insertRow( $suffix, $clean );
    }

    if ( $sourceId > 0 ) {
     $mapping[ $suffix ][ $sourceId ] = $localId;
    }

    $imported++;
   }

   $integrity = $this->verifyImportedState( $document['records'], $mapping );
   if ( ! $integrity['valid'] ) {
    throw new \RuntimeException( 'Post-import integrity verification failed.' );
   }

   $this->wpdb->query( 'COMMIT' );
   $started = false;

   $result = array(
    'status' => 'completed',
    'imported_records' => $imported,
    'total_records' => $totalRecords,
    'rejected_records' => $rejected,
    'skipped_records' => $skipped,
    'preview' => $preview,
    'source_to_local_mapping' => $mapping,
    'integrity' => $integrity,
    'operation_id' => $operationId,
   );
   $this->completeOperation( $operationId, $result );

   return $result;
  } catch ( \Throwable $e ) {
   if ( $started ) {
    $this->wpdb->query( 'ROLLBACK' );
   }

   $failure = array(
    'status' => 'failed',
    'imported_records' => $imported,
    'rejected_records' => $rejected + 1,
    'skipped_records' => $skipped,
    'operation_id' => $operationId,
   );
   $this->completeOperation( $operationId, $failure );

   throw new \RuntimeException( 'Import failed safely.', 0, $e );
  }
 }

 private function orderedRows( array $records ): array {
  $items = array();

  foreach ( self::IMPORT_ORDER as $suffix ) {
   if ( ! isset( $records[ $suffix ] ) ) {
    continue;
   }

   $rows = $records[ $suffix ];
   if ( 'configuration' === $suffix ) {
    $items[] = array( 'table' => $suffix, 'row' => $rows );
    continue;
   }

   if ( ! is_array( $rows ) ) {
    $items[] = array( 'table' => $suffix, 'row' => $rows );
    continue;
   }

   foreach ( $rows as $row ) {
    $items[] = array( 'table' => $suffix, 'row' => $row );
   }
  }

  return $items;
 }

 private function findExisting( string $suffix, array $row ): ?array {
  $table = $this->table( $suffix );
  $where = $this->naturalKey( $suffix, $row );

  if ( empty( $where ) && isset( $row['id'] ) && (int) $row['id'] > 0 ) {
   $where = array( 'id' => (int) $row['id'] );
  }

  if ( empty( $where ) ) {
   return null;
  }

  $clauses = array();
  $values = array();

  foreach ( $where as $field => $value ) {
   $clauses[] = $field . ' = %s';
   $values[] = $value;
  }

  $sql = "SELECT * FROM {$table} WHERE " . implode( ' AND ', $clauses ) . ' LIMIT 1';
  $prepared = $this->wpdb->prepare( $sql, $values );
  $result = $this->wpdb->get_row( $prepared, ARRAY_A );

  return is_array( $result ) ? $result : null;
 }

 private function naturalKey( string $suffix, array $row ): array {
  $keys = array(
   'search_intents' => array( 'slug' ),
   'clusters' => array( 'slug' ),
   'owner_urls' => array( 'normalized_url' ),
   'keywords' => array( 'normalized_keyword' ),
   'ai_providers' => array( 'provider_key' ),
   'ai_models' => array( 'provider_id', 'model_identifier' ),
   'ai_tasks' => array( 'task_identifier' ),
   'internal_link_opportunities' => array( 'source_url_id', 'target_url_id' ),
   'cannibalization' => array( 'keyword_id', 'owner_url_a_id', 'owner_url_b_id' ),
  );

  if ( ! isset( $keys[ $suffix ] ) ) {
   return array();
  }

  $result = array();

  foreach ( $keys[ $suffix ] as $field ) {
   if ( array_key_exists( $field, $row ) && '' !== (string) $row[ $field ] ) {
    $result[ $field ] = $row[ $field ];
   }
  }

  return count( $result ) === count( $keys[ $suffix ] ) ? $result : array();
 }

 private function resolutionFor( string $suffix, array $row, array $resolutions ): string {
  $sourceId = isset( $row['id'] ) ? (int) $row['id'] : 0;
  $key = $suffix . ':' . $sourceId;

  return isset( $resolutions[ $key ] ) ? (string) $resolutions[ $key ] : 'use_imported';
 }

 private function remapReferences( array $row, array $mapping ): array {
  foreach ( self::REFERENCE_FIELDS as $field => $target ) {
   if ( ! isset( $row[ $field ] ) || (int) $row[ $field ] <= 0 ) {
    continue;
   }

   $sourceId = (int) $row[ $field ];
   if ( isset( $mapping[ $target ][ $sourceId ] ) ) {
    $row[ $field ] = $mapping[ $target ][ $sourceId ];
   }
  }

  return $row;
 }

 private function insertRow( string $suffix, array $row ): int {
  $table = $this->table( $suffix );

  if ( isset( $row['id'] ) ) {
   unset( $row['id'] );
  }

  $result = $this->wpdb->insert( $table, $row, $this->formats( $row ) );
  if ( false === $result || empty( $this->wpdb->insert_id ) ) {
   throw new \RuntimeException( 'Import database operation failed.' );
  }

  return (int) $this->wpdb->insert_id;
 }

 private function formats( array $row ): array {
  return array_fill( 0, count( $row ), '%s' );
 }

 private function clearScope( array $scopes ): void {
  $tables = array();

  foreach ( $scopes as $scope ) {
   if ( PortabilityFormat::SCOPE_CONFIGURATION === $scope ) {
    delete_option( 'amanah_seo_engine_configuration' );
    continue;
   }

   foreach ( self::TABLES[ $scope ] as $suffix ) {
    $tables[ $suffix ] = true;
   }
  }

  $ordered = array_keys( $tables );
  $ordered = array_reverse( $ordered );

  foreach ( $ordered as $suffix ) {
   $this->wpdb->query( 'DELETE FROM ' . $this->table( $suffix ) );
  }
 }

 private function verifyImportedState( array $records, array $mapping ): array {
  $checked = 0;
  $missing = 0;

  foreach ( $records as $suffix => $rows ) {
   if ( 'configuration' === $suffix || ! is_array( $rows ) ) {
    continue;
   }

   foreach ( $rows as $row ) {
    if ( ! is_array( $row ) ) {
     continue;
    }

    if ( isset( $row['id'] ) && (int) $row['id'] > 0 ) {
     $checked++;
     if ( ! isset( $mapping[ $suffix ][ (int) $row['id'] ] ) ) {
      $missing++;
     }
    }

    foreach ( self::REFERENCE_FIELDS as $field => $target ) {
     if ( isset( $row[ $field ] ) && (int) $row[ $field ] > 0 ) {
      if ( ! isset( $mapping[ $target ][ (int) $row[ $field ] ] ) ) {
       $missing++;
      }
     }
    }
   }
  }

  return array(
   'valid' => 0 === $missing,
   'checked' => $checked,
   'missing_mappings' => $missing,
  );
 }

 private function startOperation(
  string $operationId,
  string $type,
  array $scope,
  array $summary
 ): void {
  $this->wpdb->insert(
   $this->wpdb->prefix . 'amanah_seo_portability_operations',
   array(
    'operation_id' => $operationId,
    'operation_type' => $type,
    'status' => 'running',
    'scope' => wp_json_encode( $scope ),
    'summary' => wp_json_encode( $summary ),
    'created_at' => current_time( 'mysql', true ),
   ),
   array( '%s', '%s', '%s', '%s', '%s', '%s' )
  );
 }

 private function completeOperation( string $operationId, array $result ): void {
  $this->wpdb->update(
   $this->wpdb->prefix . 'amanah_seo_portability_operations',
   array(
    'status' => isset( $result['status'] ) ? $result['status'] : 'completed',
    'summary' => wp_json_encode( $result ),
    'completed_at' => current_time( 'mysql', true ),
   ),
   array( 'operation_id' => $operationId ),
   array( '%s', '%s', '%s' ),
   array( '%s' )
  );
 }

 private function configuration(): array {
  $config = get_option( 'amanah_seo_engine_configuration', array() );
  if ( ! is_array( $config ) ) {
   return array();
  }

  foreach (
   array(
    'api_key',
    'apikey',
    'authorization',
    'password',
    'secret',
    'token',
    'credential',
    'license_key',
    'private_key',
   ) as $key
  ) {
   unset( $config[ $key ] );
  }

  return $config;
 }

 private function importConfiguration( array $config ): void {
  $allowed = array(
   'configuration_version',
   'ai_enabled',
   'debug_logging',
   'automatic_publishing',
   'background_processing',
   'delete_data_on_uninstall',
  );
  $clean = array();

  foreach ( $allowed as $key ) {
   if ( array_key_exists( $key, $config ) ) {
    $clean[ $key ] = $config[ $key ];
   }
  }

  if ( ! empty( $clean ) ) {
   update_option( 'amanah_seo_engine_configuration', $clean, false );
  }
 }

 private function sanitizeRow( array $row ): array {
  foreach (
   array(
    'api_key',
    'password',
    'secret',
    'token',
    'authorization',
    'credential',
    'license_key',
    'private_key',
   ) as $key
  ) {
   unset( $row[ $key ] );
  }

  return $row;
 }

 private function isAllowedTable( string $suffix ): bool {
  foreach ( self::TABLES as $tables ) {
   if ( in_array( $suffix, $tables, true ) ) {
    return true;
   }
  }

  return false;
 }

 private function table( string $suffix ): string {
  return $this->wpdb->prefix . 'amanah_seo_' . $suffix;
 }
}
