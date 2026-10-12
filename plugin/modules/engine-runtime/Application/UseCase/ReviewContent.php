<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class ReviewContent {

 private $contents;

 public function __construct( ContentRepositoryInterface $contents ) {
  $this->contents = $contents;
 }

 public function approve( int $content_id ): void {
  $content = $this->getContent( $content_id );
  if ( 'ready_for_review' !== $content->getStatus() ) {
   throw new \DomainException( 'Content must pass quality evaluation before approval.' );
  }
  $content->changeStatus( 'approved' );
  $this->contents->save( $content );
 }

 public function requestRevision( int $content_id ): void {
  $content = $this->getContent( $content_id );
  if ( ! in_array( $content->getStatus(), array( 'ready_for_review', 'quality_warning', 'quality_failed' ), true ) ) {
   throw new \DomainException( 'Content is not available for revision request.' );
  }
  $content->changeStatus( 'revision_requested' );
  $this->contents->save( $content );
 }

 private function getContent( int $content_id ) {
  $content = $this->contents->findById( $content_id );
  if ( null === $content ) {
   throw new \RuntimeException( 'Content not found.' );
  }
  return $content;
 }
}
