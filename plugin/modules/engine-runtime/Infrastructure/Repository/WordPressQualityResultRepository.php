<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\QualityResultRepositoryInterface;
use Amanah\SEOEngine\Domain\Quality\QualityResult;

defined( 'ABSPATH' ) || exit;

final class WordPressQualityResultRepository extends WordPressRepository implements QualityResultRepositoryInterface {

 public function __construct( \wpdb $wpdb ) {
  parent::__construct(
   $wpdb,
   $wpdb->prefix . 'amanah_seo_quality_results'
  );
 }

 public function save( QualityResult $result ): QualityResult {
  $data = array(
   'content_id'           => $result->getContentId(),
   'content_version'      => $result->getContentVersion(),
   'deterministic_result' => wp_json_encode( $result->getDeterministic() ),
   'semantic_result'      => wp_json_encode( $result->getSemantic() ),
   'final_result'         => wp_json_encode( $result->getFinal() ),
   'score'                => $result->getScore(),
   'status'               => $result->getStatus(),
   'created_at'           => current_time( 'mysql', true ),
  );

  $formats = array(
   '%d',
   '%d',
   '%s',
   '%s',
   '%s',
   '%f',
   '%s',
   '%s',
  );

  if ( null !== $result->getId() ) {
   $this->wpdb->update(
    $this->table,
    $data,
    array( 'id' => $result->getId() ),
    $formats,
    array( '%d' )
   );

   return $result;
  }

  $inserted = $this->wpdb->insert(
   $this->table,
   $data,
   $formats
  );

  if ( false === $inserted ) {
   throw new \RuntimeException(
    'Quality result could not be stored.'
   );
  }

  return new QualityResult(
   $result->getContentId(),
   $result->getFinal(),
   $result->getDeterministic(),
   $result->getSemantic(),
   $result->getScore(),
   $result->getStatus(),
   (int) $this->wpdb->insert_id,
   $result->getContentVersion()
  );
 }

 public function findById( int $id ): ?QualityResult {
  $row = $this->findRawById( $id );

  return $this->hydrate( $row );
 }

 public function findLatestByContentId( int $content_id ): ?QualityResult {
  $row = $this->wpdb->get_row(
   $this->wpdb->prepare(
    "SELECT * FROM {$this->table} WHERE content_id = %d ORDER BY id DESC LIMIT 1",
    $content_id
   ),
   ARRAY_A
  );

  return $this->hydrate(
   is_array( $row ) ? $row : null
  );
 }

 private function hydrate( ?array $row ): ?QualityResult {
  if ( null === $row ) {
   return null;
  }

  $deterministic = json_decode(
   (string) $row['deterministic_result'],
   true
  );

  $semantic = json_decode(
   (string) $row['semantic_result'],
   true
  );

  $final = json_decode(
   (string) $row['final_result'],
   true
  );

  return new QualityResult(
   (int) $row['content_id'],
   is_array( $final ) ? $final : array(),
   is_array( $deterministic ) ? $deterministic : array(),
   is_array( $semantic ) ? $semantic : array(),
   null !== $row['score'] ? (float) $row['score'] : null,
   (string) $row['status'],
   (int) $row['id'],
   isset( $row['content_version'] ) ? (int) $row['content_version'] : 1
  );
 }
}
