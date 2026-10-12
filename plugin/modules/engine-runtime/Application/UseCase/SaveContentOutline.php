<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\ContentBriefData;
use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Service\PageBlueprintBuilder;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;

defined( 'ABSPATH' ) || exit;

final class SaveContentOutline {

	private $briefs;
	private $blueprints;
	private $builder;

	public function __construct(
		ContentBriefRepositoryInterface $briefs,
		PageBlueprintRepositoryInterface $blueprints,
		PageBlueprintBuilder $builder
	) {
		$this->briefs              = $briefs;
		$this->blueprints           = $blueprints;
		$this->builder              = $builder;
	}

	public function execute( int $keyword_id, array $outline, array $support_mapping ): ContentBriefData {
		$brief = $this->briefs->findByKeywordId( $keyword_id );

		if ( null === $brief ) {
			throw new \RuntimeException( 'Content Brief not found. Generate Content Brief before editing the outline.' );
		}

		$normalized = $this->normalizeOutline( $outline );
		$this->validateOutline( $normalized );

		$available = array();
		foreach ( (array) ( $brief->getData()['support_keywords'] ?? array() ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$id = $this->absInt( $item['id'] ?? 0 );
			$phrase = trim( (string) ( $item['phrase'] ?? $item['keyword'] ?? '' ) );
			if ( $id > 0 && '' !== $phrase ) {
				$available[ $id ] = array( 'id' => $id, 'phrase' => $phrase );
			}
		}

		$section_ids = array();
		foreach ( $normalized as $section ) {
			$section_ids[ $section['id'] ] = true;
		}

		$mapping = array();
		foreach ( $available as $id => $item ) {
			$section_id = $this->sanitizeKey( (string) ( $support_mapping[ $id ] ?? '' ) );
			if ( '' === $section_id || ! isset( $section_ids[ $section_id ] ) ) {
				throw new \DomainException( 'Every Potential Support Keyword must be mapped to an outline section before content generation.' );
			}
			$mapping[] = array(
				'keyword_id' => $id,
				'phrase' => $item['phrase'],
				'section_id' => $section_id,
				'required' => true,
			);
		}

		$data = $brief->getData();
		$data['editable_outline_version'] = 1;
		$data['article_outline'] = $normalized;
		$data['support_keyword_mapping'] = $mapping;
		$data['outline_status'] = 'approved_for_generation';
		$data['outline_updated_at'] = current_time( 'mysql', true );

		$saved_brief = $this->briefs->save(
			new ContentBrief(
				$keyword_id,
				$data,
				$brief->getVersion() + 1,
				'draft',
				$brief->getStrategyId()
			)
		);

		$blueprint = $this->blueprints->findByKeywordId( $keyword_id );
		$blueprint_version = null !== $blueprint ? $blueprint->getVersion() + 1 : 1;
		$blueprint_data = $this->builder->build( $saved_brief, $keyword_id );

		$this->blueprints->save(
			new PageBlueprint(
				$keyword_id,
				$blueprint_data,
				$blueprint_version,
				'draft',
				$saved_brief->getId()
			)
		);

		return ContentBriefData::fromDomain( $saved_brief );
	}

	private function normalizeOutline( array $outline ): array {
		$result = array();
		$seen = array();

		foreach ( array_slice( $outline, 0, 50 ) as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$title = $this->sanitizeText( (string) ( $item['title'] ?? '' ) );
			if ( '' === $title ) {
				continue;
			}

			$level = $this->absInt( $item['level'] ?? 2 );
			$level = max( 1, min( 3, $level ) );

			$id = $this->sanitizeKey( (string) ( $item['id'] ?? '' ) );
			if ( '' === $id || isset( $seen[ $id ] ) ) {
				$id = 'section_' . ( $index + 1 );
			}

			while ( isset( $seen[ $id ] ) ) {
				$id .= '_x';
			}

			$seen[ $id ] = true;
			$result[] = array(
				'id' => $id,
				'level' => $level,
				'type' => 'h' . $level,
				'title' => $title,
			);
		}

		return $result;
	}

	private function validateOutline( array $outline ): void {
		if ( empty( $outline ) ) {
			throw new \DomainException( 'Article Outline cannot be empty.' );
		}

		$h1 = array_values(
			array_filter(
				$outline,
				static function ( $item ) {
					return 1 === (int) $item['level'];
				}
			)
		);

		if ( 1 !== count( $h1 ) ) {
			throw new \DomainException( 'Article Outline must contain exactly one H1.' );
		}

		if ( 1 !== (int) $outline[0]['level'] ) {
			throw new \DomainException( 'The H1 must be the first outline item.' );
		}
	}

	private function absInt( $value ): int {
		return is_numeric( $value ) ? max( 0, (int) $value ) : 0;
	}

	private function sanitizeText( string $value ): string {
		if ( function_exists( 'sanitize_text_field' ) ) {
			return (string) sanitize_text_field( $value );
		}

		return trim( preg_replace( '/[\r\n\t]+/', ' ', strip_tags( $value ) ) );
	}

	private function sanitizeKey( string $value ): string {
		if ( function_exists( 'sanitize_key' ) ) {
			return (string) sanitize_key( $value );
		}

		$value = strtolower( trim( $value ) );
		$value = preg_replace( '/[^a-z0-9_\-]/', '', $value );

		return is_string( $value ) ? $value : '';
	}
}
