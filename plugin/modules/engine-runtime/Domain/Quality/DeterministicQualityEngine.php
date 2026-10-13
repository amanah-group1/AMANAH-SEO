<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\Quality;

use Amanah\SEOEngine\Domain\Content\Content;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;
use Amanah\SEOEngine\Application\Content\ContentGenerationRules;

defined( 'ABSPATH' ) || exit;

final class DeterministicQualityEngine {

 public function evaluate( Content $content, ?string $keyword = null, ?PageBlueprint $blueprint = null ): QualityResult {
  $checks = array();

  $checks[] = $this->check( 'content_body', '' !== trim( $content->getBody() ), true, 'Content body is required.' );
  $checks[] = $this->check( 'title', null !== $content->getTitle() && '' !== trim( (string) $content->getTitle() ), true, 'Content title is required.' );
  $checks[] = $this->check( 'excerpt', null !== $content->getExcerpt() && '' !== trim( (string) $content->getExcerpt() ), false, 'Content excerpt is missing.' );
  $checks[] = $this->check( 'metadata', is_array( $content->getMetadata() ), true, 'Content metadata must be an array.' );
  
  $metadata = $content->getMetadata();
  if ( is_array( $metadata ) && isset( $metadata['generation_policy'] ) ) {
   $policy = ContentGenerationRules::validatePayload(
    array(
     'title' => $content->getTitle(),
     'body' => $content->getBody(),
     'excerpt' => $content->getExcerpt(),
     'metadata' => $metadata,
    ),
    null !== $blueprint ? $blueprint->getData() : array()
   );
   $checks[] = $this->check(
    'content_generation_policy',
    ! empty( $policy['valid'] ),
    true,
    'Content does not satisfy the mandatory aturan generate konten policy: ' . implode( ', ', $policy['errors'] ?? array() )
   );
   foreach ( (array) ( $policy['warnings'] ?? array() ) as $warning ) {
    $checks[] = $this->check( 'content_policy_warning:' . $warning, false, false, $warning );
   }
  }

  if ( null !== $keyword && '' !== trim( $keyword ) ) {
   $checks[] = $this->check(
    'keyword_presence',
    false !== stripos( $content->getBody(), trim( $keyword ) ),
    true,
    'Required keyword is not present in the content body.'
   );
  }

  if ( null !== $blueprint ) {
   $blueprint_data = $blueprint->getData();
   $outline = isset( $blueprint_data['article_outline'] ) && is_array( $blueprint_data['article_outline'] ) ? $blueprint_data['article_outline'] : array();
   if ( ! empty( $outline ) ) {
    $matches = array();
    preg_match_all( '/<h([1-3])\b[^>]*>(.*?)<\/h[1-3]>/is', $content->getBody(), $matches, PREG_SET_ORDER );
    $headings = array();
    foreach ( $matches as $match ) {
     $headings[] = array( 'level' => (int) $match[1], 'title' => trim( wp_strip_all_tags( html_entity_decode( (string) $match[2] ) ) ) );
    }
    $h1_count = count( array_filter( $headings, static function ( $item ) { return 1 === $item['level']; } ) );
    $checks[] = $this->check( 'outline_h1', 1 === $h1_count, true, 'Generated content must contain exactly one H1.' );
    $cursor = 0;
    foreach ( $outline as $section ) {
     $level = (int) ( $section['level'] ?? 0 );
     $title = trim( (string) ( $section['title'] ?? '' ) );
     if ( $level < 1 || $level > 3 || '' === $title ) { continue; }
     $found = false;
     for ( $i = $cursor; $i < count( $headings ); $i++ ) {
      if ( $headings[ $i ]['level'] === $level && 0 === strcasecmp( $headings[ $i ]['title'], $title ) ) {
       $cursor = $i + 1; $found = true; break;
      }
     }
     $checks[] = $this->check( 'outline_section:' . $title, $found, true, 'Required outline section is missing or out of order: ' . $title );
    }
   }
   $support = isset( $blueprint_data['support_keywords'] ) && is_array( $blueprint_data['support_keywords'] ) ? $blueprint_data['support_keywords'] : array();
   $body_text = wp_strip_all_tags( $content->getBody() );
   foreach ( $support as $support_keyword ) {
    $phrase = is_array( $support_keyword ) ? trim( (string) ( $support_keyword['phrase'] ?? $support_keyword['keyword'] ?? '' ) ) : trim( (string) $support_keyword );
    if ( '' !== $phrase ) {
     $checks[] = $this->check( 'support_keyword:' . $phrase, false !== stripos( $body_text, $phrase ), true, 'Required Potential Support Keyword is missing: ' . $phrase );
    }
   }
   $checks[] = $this->check(
    'blueprint_relationship',
    $content->getBlueprintId() === $blueprint->getId(),
    true,
    'Content blueprint relationship is invalid.'
   );

   $required_sections = isset( $blueprint->getData()['required_sections'] ) && is_array( $blueprint->getData()['required_sections'] )
    ? $blueprint->getData()['required_sections']
    : array();

   foreach ( $required_sections as $section ) {
    $section = trim( (string) $section );
    if ( '' === $section ) {
     continue;
    }
    $checks[] = $this->check(
     'required_section:' . $section,
     false !== stripos( $content->getBody(), $section ),
     true,
     'Required section is missing: ' . $section
    );
   }
  }

  $failed = 0;
  $warnings = 0;
  foreach ( $checks as $check ) {
   if ( 'failed' === $check['status'] ) {
    $failed++;
   } elseif ( 'warning' === $check['status'] ) {
    $warnings++;
   }
  }

  $status = $failed > 0 ? 'failed' : ( $warnings > 0 ? 'warning' : 'passed' );
  $score = count( $checks ) > 0 ? round( ( count( $checks ) - $failed ) / count( $checks ) * 100, 2 ) : 0.0;

  return new QualityResult(
   $content->getId() ?: 0,
   $checks,
   $checks,
   array(),
   $score,
   $status,
   null,
   $content->getVersion()
  );
 }

 private function check( string $name, bool $passed, bool $required, string $message ): array {
  return array(
   'check' => $name,
   'status' => $passed ? 'passed' : ( $required ? 'failed' : 'warning' ),
   'required' => $required,
   'reason' => $passed ? 'Check passed.' : $message,
   'recommended_action' => $passed ? null : $message,
   'source' => 'deterministic',
  );
 }
}
