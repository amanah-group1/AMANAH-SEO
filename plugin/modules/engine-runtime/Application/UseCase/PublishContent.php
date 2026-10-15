<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Port\ContentPublisherInterface;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\QualityResultRepositoryInterface;
use Amanah\SEOEngine\Domain\Quality\PrePublishValidator;

defined( 'ABSPATH' ) || exit;

final class PublishContent {

 private $contents;
 private $quality_results;
 private $publisher;
 private $validator;
 private $sync_wordpress_content;

 public function __construct(
  ContentRepositoryInterface $contents,
  QualityResultRepositoryInterface $quality_results,
  ContentPublisherInterface $publisher,
  PrePublishValidator $validator,
  SyncWordPressContent $sync_wordpress_content
 ) {
  $this->contents = $contents;
  $this->quality_results = $quality_results;
  $this->publisher = $publisher;
  $this->validator = $validator;
  $this->sync_wordpress_content = $sync_wordpress_content;
 }

 public function execute( int $content_id ): int {
  $content = $this->contents->findById( $content_id );
  if ( null === $content ) {
   throw new \RuntimeException( 'Content not found.' );
  }

  if ( null !== $content->getWordPressPostId() ) {
   $this->sync_wordpress_content->execute( $content_id );
   $content = $this->contents->findById( $content_id );
  }

  if ( 'approved' !== $content->getStatus() ) {
   throw new \DomainException( 'Content must be manually approved before publishing.' );
  }

  $quality = $this->quality_results->findLatestByContentId( $content_id );
  if ( null === $quality ) {
   throw new \DomainException( 'Content quality evaluation is required before publishing.' );
  }

  if ( $quality->getContentVersion() !== $content->getVersion() ) {
   throw new \DomainException( 'Content changed after quality evaluation. Re-evaluation is required before publishing.' );
  }

  $this->validator->validate( $quality );

  $post_id = $this->publisher->publish(
   array(
    'post_id' => $content->getWordPressPostId(),
    'title' => $content->getTitle(),
    'content' => $content->getBody(),
    'excerpt' => $content->getExcerpt(),
    'post_status' => 'publish',
    'post_type' => 'post',
   )
  );

  $content->changeStatus( 'published' );
  $this->contents->save(
   new \Amanah\SEOEngine\Domain\Content\Content(
    $content->getBody(),
    $content->getVersion(),
    'published',
    $content->getKeywordId(),
    $content->getBlueprintId(),
    $content->getId(),
    $content->getTitle(),
    $content->getExcerpt(),
    $content->getMetadata(),
    $post_id
   )
  );

  return $post_id;
 }
}
