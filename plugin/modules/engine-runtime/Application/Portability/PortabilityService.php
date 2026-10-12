<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Portability;

use Amanah\SEOEngine\Application\Job\JobContract;
use Amanah\SEOEngine\Application\Job\JobManager;

defined( 'ABSPATH' ) || exit;

final class PortabilityService {

 private $repository;
 private $sanitizer;
 private $validator;
 private $backupRepository;
 private $compatibilityGate;
 private $jobManager;

 public function __construct(
  PortabilityRepositoryInterface $repository,
  ?SecretSanitizer $sanitizer = null,
  ?PortabilityImportValidator $validator = null,
  ?PortabilityBackupRepositoryInterface $backupRepository = null,
  ?PortabilityCompatibilityGate $compatibilityGate = null,
  ?JobManager $jobManager = null
 ) {
  $this->repository = $repository;
  $this->sanitizer = $sanitizer ?: new SecretSanitizer();
  $this->validator = $validator ?: new PortabilityImportValidator();
  $this->backupRepository = $backupRepository;
  $this->compatibilityGate = $compatibilityGate ?: new PortabilityCompatibilityGate();
  $this->jobManager = $jobManager;
 }

 public function export( array $scopes ): array {
  $this->validateScopes( $scopes );
  $records = $this->sanitizer->sanitize( $this->repository->export( $scopes ) );
  $document = array(
   'format_version' => PortabilityFormat::VERSION,
   'product' => PortabilityFormat::PRODUCT,
   'type' => PortabilityFormat::TYPE,
   'plugin_version' => defined( 'AMANAH_SEO_ENGINE_VERSION' ) ? AMANAH_SEO_ENGINE_VERSION : 'unknown',
   'exported_at' => gmdate( 'c' ),
   'data_scope' => array_values( $scopes ),
   'schema_version' => PortabilityCompatibilityGate::MAX_SCHEMA_VERSION,
   'records' => $records,
  );

  if ( strlen( (string) wp_json_encode( $document ) ) > PortabilityImportValidator::MAX_DOCUMENT_BYTES ) {
   throw new \RuntimeException( 'Export exceeds the maximum portability document size.' );
  }

  return $document;
 }

 public function createBackup( array $scopes, array $metadata = array() ): array {
  $document = $this->export( $scopes );
  $backupId = substr( hash( 'sha256', wp_json_encode( $document ) . '|' . microtime( true ) ), 0, 32 );
  if ( null === $this->backupRepository ) {
   throw new \RuntimeException( 'Backup storage is not configured.' );
  }
  $metadata = array_merge(
   array(
    'schema_version' => isset( $document['schema_version'] ) ? (int) $document['schema_version'] : 0,
    'plugin_version' => isset( $document['plugin_version'] ) ? (string) $document['plugin_version'] : '',
    'data_scope' => $document['data_scope'],
   ),
   $metadata
  );
  return $this->backupRepository->create( $backupId, $document, $metadata );
 }

 public function previewBackupRestore( string $backupId, string $mode = 'merge', array $resolutions = array() ): array {
  $backup = $this->getBackup( $backupId );
  $this->compatibilityGate->validate( $backup['document'] );
  return $this->previewImport( $backup['document'], $mode, $resolutions );
 }

 public function restoreBackup( string $backupId, string $mode = 'merge', array $resolutions = array() ): array {
  $backup = $this->getBackup( $backupId );
  $document = $backup['document'];
  $this->compatibilityGate->validate( $document );
  $preview = $this->previewImport( $document, $mode, $resolutions );
  $result = $this->import( $document, $mode, $preview['confirmation_token'], $resolutions );
  $this->backupRepository->markStatus( $backupId, 'restored', array( 'last_restore_at' => current_time( 'mysql', true ), 'last_operation_id' => isset( $result['operation_id'] ) ? $result['operation_id'] : '' ) );
  $result['backup_id'] = $backupId;
  return $result;
 }

 public function resumeRestore( string $backupId, string $mode = 'merge', array $resolutions = array() ): array {
  return $this->restoreBackup( $backupId, $mode, $resolutions );
 }

 public function enqueueRestore( string $backupId, string $mode = 'merge', array $resolutions = array() ): array {
  if ( null === $this->jobManager ) {
   throw new \RuntimeException( 'Background job manager is not configured.' );
  }
  $this->getBackup( $backupId );
  $idempotencyKey = 'portability-restore:' . $backupId . ':' . hash( 'sha256', $mode . '|' . wp_json_encode( $resolutions ) );
  $job = $this->jobManager->enqueue( 'portability_restore', array( 'backup_id' => $backupId, 'mode' => $mode, 'resolutions' => $resolutions ), $idempotencyKey, JobContract::NORMAL, 0, 900, 3 );
  return array( 'status' => 'queued', 'job_id' => $job->getJobId(), 'backup_id' => $backupId );
 }

 public function previewImport(
  array $document,
  string $mode = 'merge',
  array $resolutions = array()
 ): array {
  $this->validator->validate( $document );
  $this->compatibilityGate->validate( $document );
  $this->validator->validateMode( $mode );
  $this->validator->validateResolutions( $resolutions );
  $preview = $this->repository->previewImport( $document, $resolutions );
  $preview['confirmation_token'] = $this->validator->confirmationToken( $document, $mode, $resolutions );
  $preview['requires_confirmation'] = true;
  $preview['mode'] = $mode;
  $preview['resolutions'] = $resolutions;

  return $preview;
 }

 public function import(
  array $document,
  string $mode = 'merge',
  ?string $confirmation = null,
  array $resolutions = array()
 ): array {
  $this->validator->validate( $document );
  $this->compatibilityGate->validate( $document );
  $this->validator->validateConfirmation( $document, $mode, $confirmation, $resolutions );

  return $this->repository->import( $document, $mode, $resolutions );
 }

 private function getBackup( string $backupId ): array {
  if ( '' === trim( $backupId ) || null === $this->backupRepository ) {
   throw new \InvalidArgumentException( 'A valid backup ID is required.' );
  }
  $backup = $this->backupRepository->find( $backupId );
  if ( null === $backup || empty( $backup['document'] ) || ! is_array( $backup['document'] ) ) {
   throw new \RuntimeException( 'Backup was not found or is invalid.' );
  }
  return $backup;
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
}
