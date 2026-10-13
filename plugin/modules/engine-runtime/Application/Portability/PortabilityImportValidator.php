<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Portability;

defined( 'ABSPATH' ) || exit;

final class PortabilityImportValidator {

 public const MAX_DOCUMENT_BYTES = 5242880;

 public function validate( array $document ): void {
  $required = array( 'format_version', 'product', 'type', 'data_scope', 'records' );

  foreach ( $required as $key ) {
   if ( ! array_key_exists( $key, $document ) ) {
    throw new \InvalidArgumentException( 'Invalid portability document.' );
   }
  }

  if (
   PortabilityFormat::VERSION !== (int) $document['format_version']
   || PortabilityFormat::PRODUCT !== $document['product']
   || PortabilityFormat::TYPE !== $document['type']
  ) {
   throw new \InvalidArgumentException( 'Unsupported portability document.' );
  }

  if ( ! is_array( $document['data_scope'] ) || ! is_array( $document['records'] ) ) {
   throw new \InvalidArgumentException( 'Invalid portability structure.' );
  }

  if ( strlen( (string) wp_json_encode( $document ) ) > self::MAX_DOCUMENT_BYTES ) {
   throw new \InvalidArgumentException( 'Portability document exceeds the maximum allowed size.' );
  }

  $this->validateScopes( $document['data_scope'] );
  $this->validateRecords( $document['records'], $document['data_scope'] );
  $this->validateRelationships( $document['records'] );
  $this->validateUrls( $document['records'] );
 }

 public function validateMode( string $mode ): void {
  if ( ! in_array( $mode, array( 'create_only', 'update_existing', 'replace', 'merge' ), true ) ) {
   throw new \InvalidArgumentException( 'Unsupported import mode.' );
  }
 }

 public function validateResolutions( array $resolutions ): void {
  foreach ( $resolutions as $key => $resolution ) {
   if ( ! is_string( $key ) || ! in_array(
    $resolution,
    array( 'skip', 'keep_local', 'use_imported', 'resolve_manually' ),
    true
   ) ) {
    throw new \InvalidArgumentException( 'Invalid portability conflict resolution.' );
   }
  }
 }

 public function confirmationToken(
  array $document,
  string $mode,
  array $resolutions = array()
 ): string {
  return hash(
   'sha256',
   (string) wp_json_encode( $document )
   . '|'
   . $mode
   . '|'
   . (string) wp_json_encode( $resolutions )
  );
 }

 public function validateConfirmation(
  array $document,
  string $mode,
  ?string $confirmation,
  array $resolutions = array()
 ): void {
  $this->validateMode( $mode );
  $this->validateResolutions( $resolutions );

  if ( ! hash_equals(
   $this->confirmationToken( $document, $mode, $resolutions ),
   (string) $confirmation
  ) ) {
   throw new \InvalidArgumentException( 'Explicit import confirmation is required.' );
  }
 }

 private function validateScopes( array $scopes ): void {
  if ( empty( $scopes ) ) {
   throw new \InvalidArgumentException( 'At least one export scope is required.' );
  }

  foreach ( $scopes as $scope ) {
   if ( ! in_array( $scope, PortabilityFormat::allowedScopes(), true ) ) {
    throw new \InvalidArgumentException( 'Unsupported export scope.' );
   }
  }
 }

 private function validateRecords( array $records, array $scopes ): void {
  $allowed = array(
   'configuration' => PortabilityFormat::SCOPE_CONFIGURATION,
   'diagnostics' => PortabilityFormat::SCOPE_DIAGNOSTICS,
   'jobs' => PortabilityFormat::SCOPE_JOBS,
   'search_intents' => PortabilityFormat::SCOPE_SEO,
   'clusters' => PortabilityFormat::SCOPE_SEO,
   'owner_urls' => PortabilityFormat::SCOPE_SEO,
   'keywords' => PortabilityFormat::SCOPE_SEO,
   'content_strategies' => PortabilityFormat::SCOPE_SEO,
   'content_briefs' => PortabilityFormat::SCOPE_SEO,
   'page_blueprints' => PortabilityFormat::SCOPE_SEO,
   'content' => PortabilityFormat::SCOPE_SEO,
   'quality_results' => PortabilityFormat::SCOPE_SEO,
   'internal_link_opportunities' => PortabilityFormat::SCOPE_SEO,
   'cannibalization' => PortabilityFormat::SCOPE_SEO,
   'ai_providers' => PortabilityFormat::SCOPE_SEO,
   'ai_models' => PortabilityFormat::SCOPE_SEO,
   'ai_tasks' => PortabilityFormat::SCOPE_SEO,
   'ai_usage' => PortabilityFormat::SCOPE_SEO,
  );

  foreach ( array_keys( $records ) as $table ) {
   if ( ! isset( $allowed[ $table ] ) || ! in_array( $allowed[ $table ], $scopes, true ) ) {
    throw new \InvalidArgumentException( 'Portability record is outside the declared scope.' );
   }
  }
 }

 private function validateRelationships( array $records ): void {
  $ids = array();

  foreach ( $records as $table => $rows ) {
   if ( 'configuration' === $table ) {
    continue;
   }

   if ( ! is_array( $rows ) ) {
    throw new \InvalidArgumentException( 'Invalid portability record collection.' );
   }

   $ids[ $table ] = array();

   foreach ( $rows as $row ) {
    if ( ! is_array( $row ) ) {
     throw new \InvalidArgumentException( 'Invalid portability record.' );
    }

    if ( isset( $row['id'] ) ) {
     $ids[ $table ][ (int) $row['id'] ] = true;
    }
   }
  }

  $references = array(
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

  foreach ( $records as $table => $rows ) {
   if ( 'configuration' === $table ) {
    continue;
   }

   foreach ( $rows as $row ) {
    foreach ( $references as $field => $target ) {
     if (
      isset( $row[ $field ] )
      && (int) $row[ $field ] > 0
      && ( ! isset( $records[ $target ] ) || ! isset( $ids[ $target ][ (int) $row[ $field ] ] ) )
     ) {
      throw new \InvalidArgumentException(
       'Portability relationship references a missing source identifier.'
      );
     }
    }
   }
  }
 }

 private function validateUrls( array $records ): void {
  if ( empty( $records['owner_urls'] ) ) {
   return;
  }

  foreach ( $records['owner_urls'] as $row ) {
   if ( ! isset( $row['url'] ) || '' === trim( (string) $row['url'] ) ) {
    throw new \InvalidArgumentException( 'Owner URL is required.' );
   }

   $scheme = parse_url( (string) $row['url'], PHP_URL_SCHEME );

   if ( ! in_array( strtolower( (string) $scheme ), array( 'http', 'https' ), true ) ) {
    throw new \InvalidArgumentException( 'Owner URL must use HTTP or HTTPS.' );
   }
  }
 }
}
