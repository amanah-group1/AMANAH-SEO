<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\AI\AIExecutionService;
use Amanah\SEOEngine\AI\AIRequest;
use Amanah\SEOEngine\Application\DTO\ContentData;
use Amanah\SEOEngine\Application\Content\ContentGenerationRules;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Domain\Content\Content;
use Amanah\SEOEngine\Domain\Content\ContentValidationService;
use Amanah\SEOEngine\Infrastructure\AI\SecretStore;
use Amanah\SEOEngine\Application\Port\ContentPublisherInterface;
use Amanah\SEOEngine\Infrastructure\Configuration\Configuration;

defined( 'ABSPATH' ) || exit;

final class GenerateContent {

 private $blueprints;
 private $contents;
 private $validation;
 private $ai;
 private $secrets;
 private $configuration;
 private $publisher;

 public function __construct(
  PageBlueprintRepositoryInterface $blueprints,
  ContentRepositoryInterface $contents,
  ContentValidationService $validation,
  AIExecutionService $ai,
  SecretStore $secrets,
  Configuration $configuration,
  ContentPublisherInterface $publisher
 ) {
  $this->blueprints = $blueprints;
  $this->contents = $contents;
  $this->validation = $validation;
  $this->ai = $ai;
  $this->secrets = $secrets;
  $this->configuration = $configuration;
  $this->publisher = $publisher;
 }

 public function execute( int $keyword_id ): ContentData {
  $blueprint = $this->blueprints->findByKeywordId( $keyword_id );

  if ( null === $blueprint ) {
   throw new \RuntimeException( 'Page Blueprint not found. Create Page Blueprint before generating Content.' );
  }

  $provider = sanitize_key( (string) get_option( 'amanah_seo_engine_active_ai_provider', '' ) );
  if ( '' === $provider ) {
   $provider = trim( (string) $this->configuration->get( 'ai_provider', '' ) );
  }

  $model = sanitize_text_field( (string) get_option( 'amanah_seo_engine_active_ai_model', '' ) );
  if ( '' === $model ) {
   $model = trim( (string) $this->configuration->get( 'ai_model', '' ) );
  }

  if ( 'amanah_seo_engine' === $provider ) {
   $provider = 'gemini';
   if ( '' === $model || 'amanah-default-v1' === $model ) {
    $model = 'gemini-2.5-flash';
   }
  }

  if ( '' === $provider ) {
   throw new \RuntimeException( 'AI provider is not configured.' );
  }

  if ( '' === $model ) {
   throw new \RuntimeException( 'AI model is not configured.' );
  }

  $secret = $this->secrets->get( $provider );
  if ( null === $secret || '' === $secret ) {
   throw new \RuntimeException( 'AI credentials are not configured for the selected provider.' );
  }

  $credentials = array( 'api_key' => $secret );
  $d = $blueprint->getData();

  if ( ! isset( $d['requirement_baseline'] ) || ! is_array( $d['requirement_baseline'] ) ) {
   throw new \DomainException( 'Page Blueprint requirement baseline is missing. Regenerate the Page Blueprint before content generation.' );
  }

  if ( empty( $d['article_outline'] ) || ! is_array( $d['article_outline'] ) || 'approved_for_generation' !== (string) ( $d['outline_status'] ?? '' ) ) {
   throw new \DomainException( 'Editable Article Outline must be saved and approved before content generation.' );
  }

  $support_mapping = isset( $d['support_keyword_mapping'] ) && is_array( $d['support_keyword_mapping'] ) ? $d['support_keyword_mapping'] : array();
  $required_support = isset( $d['support_keywords'] ) && is_array( $d['support_keywords'] ) ? $d['support_keywords'] : array();

  if ( count( $required_support ) !== count( $support_mapping ) ) {
   throw new \DomainException( 'Every Potential Support Keyword must be mapped to the editable outline before content generation.' );
  }

  $prompt = ContentGenerationRules::systemPrompt() . "\n\nAPPROVED EDITABLE ARTICLE OUTLINE IS THE GOVERNING STRUCTURE. Preserve the supplied H1/H2/H3 order and section meaning. Do not invent, replace, merge, or reorder sections. Every mapped support keyword belongs to its mapped section and must be covered naturally. Do not treat a support keyword as an exact-phrase requirement unless an applicable mandatory requirement explicitly requires phrase presence. Do not change search intent, Owner URL, cluster, keyword, or blueprint semantics. Generate only from supplied/verified context and explicitly mark missing research as needs_validation.";

  $request = new AIRequest(
   'content_generation',
   $model,
   $prompt,
   array( 'page_blueprint' => $d ),
   true,
   array( 'title', 'body', 'excerpt', 'metadata' )
  );

  $result = $this->ai->executeTask( $request, $provider, $credentials, array( 'text_generation' ) );

  if ( ! $result->isSuccessful() || null === $result->getResponse() ) {
   throw new \RuntimeException( 'Content generation failed: ' . ( $result->getErrorCategory() ?: 'ai_unavailable' ) );
  }

  $decoded = json_decode( $result->getResponse()->getContent(), true );

  if ( ! is_array( $decoded ) || empty( $decoded['body'] ) ) {
   throw new \RuntimeException( 'Generated content response is invalid.' );
  }

  $policy_validation = ContentGenerationRules::validatePayload( $decoded, $d );

  if ( empty( $policy_validation['valid'] ) ) {
   throw new \RuntimeException( 'Generated content violates mandatory content-generation rules: ' . implode( ', ', $policy_validation['errors'] ) );
  }

  if ( ! isset( $decoded['metadata'] ) || ! is_array( $decoded['metadata'] ) ) {
   $decoded['metadata'] = array();
  }

  $decoded['metadata']['policy_validation'] = $policy_validation;
  $existing = $this->contents->findLatestByBlueprintId( (int) $blueprint->getId() );
  $version = null !== $existing ? $existing->getVersion() + 1 : 1;
  $metadata = isset( $decoded['metadata'] ) && is_array( $decoded['metadata'] ) ? $decoded['metadata'] : array();

  $content = new Content(
   (string) $decoded['body'],
   $version,
   'generated',
   $keyword_id,
   $blueprint->getId(),
   null,
   isset( $decoded['title'] ) ? (string) $decoded['title'] : null,
   isset( $decoded['excerpt'] ) ? (string) $decoded['excerpt'] : null,
   $metadata
  );

  $this->validation->validate( $content, $blueprint );
  $saved = $this->contents->save( $content );

  $post_data = array(
   'title' => $saved->getTitle(),
   'content' => $saved->getBody(),
   'excerpt' => $saved->getExcerpt(),
   'post_type' => 'post',
  );

  if ( null !== $existing && null !== $existing->getWordPressPostId() ) {
   $post_data['post_id'] = $existing->getWordPressPostId();
   $post_id = $this->publisher->updateDraft( $post_data );
  } else {
   $post_id = $this->publisher->createDraft( $post_data );
  }

  $linked = new Content(
   $saved->getBody(),
   $saved->getVersion(),
   $saved->getStatus(),
   $saved->getKeywordId(),
   $saved->getBlueprintId(),
   $saved->getId(),
   $saved->getTitle(),
   $saved->getExcerpt(),
   $saved->getMetadata(),
   $post_id
  );

  return ContentData::fromDomain( $this->contents->save( $linked ) );
 }
}
