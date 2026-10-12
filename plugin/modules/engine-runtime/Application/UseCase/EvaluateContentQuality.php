<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\QualityResultRepositoryInterface;
use Amanah\SEOEngine\Domain\Quality\DeterministicQualityEngine;
use Amanah\SEOEngine\Domain\Content\Content;
use Amanah\SEOEngine\Application\UseCase\SyncWordPressContent;

defined( 'ABSPATH' ) || exit;

final class EvaluateContentQuality {

 private $contents;
 private $keywords;
 private $blueprints;
 private $results;
 private $engine;
 private $sync_wordpress_content;

 public function __construct(
  ContentRepositoryInterface $contents,
  KeywordRepositoryInterface $keywords,
  PageBlueprintRepositoryInterface $blueprints,
  QualityResultRepositoryInterface $results,
  DeterministicQualityEngine $engine,
  SyncWordPressContent $sync_wordpress_content
 ) {
  $this->contents = $contents;
  $this->keywords = $keywords;
  $this->blueprints = $blueprints;
  $this->results = $results;
  $this->engine = $engine;
  $this->sync_wordpress_content = $sync_wordpress_content;
 }

 public function execute( int $content_id ) {
  $content = $this->contents->findById( $content_id );
  if ( null === $content ) {
   throw new \RuntimeException( 'Content not found.' );
  }

  if ( null !== $content->getWordPressPostId() ) {
   $this->sync_wordpress_content->execute( $content_id );
   $content = $this->contents->findById( $content_id );
  }

  $keyword = null !== $content->getKeywordId() ? $this->keywords->findById( (int) $content->getKeywordId() ) : null;
  $blueprint = null !== $content->getBlueprintId() ? $this->blueprints->findById( (int) $content->getBlueprintId() ) : null;

  $result = $this->engine->evaluate(
   $content,
   null !== $keyword ? $keyword->getPhrase() : null,
   $blueprint
  );

  $saved = $this->results->save( $result );
  $content->changeStatus( 'passed' === $saved->getStatus() ? 'ready_for_review' : 'quality_' . $saved->getStatus() );
  $this->contents->save( $content );

  return $saved;
 }
}
