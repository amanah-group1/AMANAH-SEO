<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRevisionRepositoryInterface;
use Amanah\SEOEngine\Domain\Content\Content;
use Amanah\SEOEngine\Domain\Content\ContentRevision;

defined( 'ABSPATH' ) || exit;

final class UpdateContent {
 private $contents;
 private $revisions;

 public function __construct(
  ContentRepositoryInterface $contents,
  ContentRevisionRepositoryInterface $revisions
 ) {
  $this->contents = $contents;
  $this->revisions = $revisions;
 }

 public function execute( int $content_id, string $title, string $body, string $excerpt = '' ): Content {
  $content = $this->contents->findById( $content_id );
  if ( null === $content ) {
   throw new \RuntimeException( 'Content not found.' );
  }
  if ( '' === trim( $body ) ) {
   throw new \InvalidArgumentException( 'Content body is required.' );
  }

  $post_id = $content->getWordPressPostId();
  if ( null === $post_id ) {
   throw new \DomainException( 'Content must be linked to a WordPress post before it can be revised.' );
  }

  $updated = wp_update_post(
   array(
    'ID'           => $post_id,
    'post_title'   => $title,
    'post_content' => $body,
    'post_excerpt' => $excerpt,
    'post_status'  => 'draft',
   ),
   true
  );
  if ( is_wp_error( $updated ) ) {
   throw new \RuntimeException( $updated->get_error_message() );
  }

  $wordpress_modified_at = '';
  $post = get_post( $post_id );
  if ( $post instanceof \WP_Post ) {
   $wordpress_modified_at = (string) $post->post_modified_gmt;
  }

  $revision_number = $content->getVersion() + 1;
  $revision = new ContentRevision(
   $content->getId() ?: $content_id,
   $post_id,
   $revision_number,
   'amanah_editor',
   $title,
   $body,
   $excerpt,
   hash( 'sha256', $title . "\n" . $body . "\n" . $excerpt ),
   $wordpress_modified_at
  );
  $this->revisions->save( $revision );

  $updated_content = new Content(
   $body,
   $revision_number,
   'draft',
   $content->getKeywordId(),
   $content->getBlueprintId(),
   $content->getId(),
   $title,
   $excerpt,
   $content->getMetadata(),
   $post_id
  );

  return $this->contents->save( $updated_content );
 }
}
