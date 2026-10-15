<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Service;

use Amanah\SEOEngine\Application\Repository\ContentRequirementRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\RequirementSnapshotRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;

defined( 'ABSPATH' ) || exit;

/**
 * Canonical Page Blueprint assembler.
 *
 * The saved editable Content Brief outline and support-keyword mapping are
 * the governing structure for every Blueprint generation path. Requirement
 * snapshots are materialized into the Blueprint baseline at the same boundary.
 */
final class PageBlueprintBuilder {

	private $requirement_repository;
	private $snapshot_repository;

	public function __construct(
		ContentRequirementRepositoryInterface $requirement_repository,
		RequirementSnapshotRepositoryInterface $snapshot_repository
	) {
		$this->requirement_repository = $requirement_repository;
		$this->snapshot_repository    = $snapshot_repository;
	}

	public function build( ContentBrief $brief, int $keyword_id ): array {
		$d = $brief->getData();

		$keyword = isset( $d['keyword'] ) && is_array( $d['keyword'] ) ? $d['keyword'] : array();
		$support_keywords = isset( $d['support_keywords'] ) && is_array( $d['support_keywords'] )
			? array_values( $d['support_keywords'] )
			: array();
		$outline = isset( $d['article_outline'] ) && is_array( $d['article_outline'] ) ? $d['article_outline'] : array();
		$mapping = isset( $d['support_keyword_mapping'] ) && is_array( $d['support_keyword_mapping'] ) ? $d['support_keyword_mapping'] : array();

		if ( empty( $outline ) ) {
			throw new \DomainException( 'Editable Article Outline is required before Page Blueprint generation.' );
		}

		$article_type = isset( $d['article_type'] ) ? (string) $d['article_type'] : 'supporting';
		$target = isset( $d['target_url'] ) ? (string) $d['target_url'] : '';
		$intent_coverage = isset( $d['intent_coverage'] ) && is_array( $d['intent_coverage'] )
			? $d['intent_coverage']
			: array();

		$mapping_by_section = array();
		foreach ( $mapping as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$section_id = sanitize_key( (string) ( $item['section_id'] ?? '' ) );
			if ( '' !== $section_id ) {
				$mapping_by_section[ $section_id ][] = $item;
			}
		}

		$sections = array();
		foreach ( $outline as $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}
			$level = max( 1, min( 3, (int) ( $section['level'] ?? 2 ) ) );
			$section_id = sanitize_key( (string) ( $section['id'] ?? '' ) );
			$sections[] = array(
				'id' => $section_id,
				'level' => $level,
				'type' => 'h' . $level,
				'title' => trim( (string) ( $section['title'] ?? '' ) ),
				'intent_slugs' => array(),
				'support_keywords' => $mapping_by_section[ $section_id ] ?? array(),
			);
		}

		$requirement_baseline = $this->buildRequirementBaseline( $keyword_id );
		$requirements = array();
		$first_section_id = ! empty( $outline ) ? sanitize_key( (string) ( $outline[0]['id'] ?? '' ) ) : '';
		foreach ( $requirement_baseline as $item ) {
			$requirements[] = array(
				'requirement_id' => $item['requirement_id'],
				'snapshot_id' => $item['snapshot_id'],
				'section_id' => $first_section_id,
				'mandatory' => $item['mandatory'],
			);
		}

		return array(
			'blueprint_version' => 2,
			'source' => 'editable_content_brief',
			'keyword' => $keyword,
			'support_keywords' => $support_keywords,
			'support_keyword_mapping' => $mapping,
			'requirements' => $requirements,
			'article_type' => $article_type,
			'target_url' => $target,
			'content_requirement' => isset( $d['content_requirement'] ) ? (string) $d['content_requirement'] : 'new_content',
			'content_role' => isset( $d['content_role'] ) ? (string) $d['content_role'] : 'supporting',
			'search_intent' => isset( $d['search_intent'] ) && is_array( $d['search_intent'] ) ? $d['search_intent'] : null,
			'intent_coverage' => 'pillar' === $article_type ? $intent_coverage : array(),
			'cluster' => isset( $d['cluster'] ) && is_array( $d['cluster'] ) ? $d['cluster'] : null,
			'content_objective' => isset( $d['content_objective'] ) ? (string) $d['content_objective'] : '',
			'content_direction' => isset( $d['content_direction'] ) ? (string) $d['content_direction'] : '',
			'content_brief_id' => $brief->getId(),
			'article_outline' => $outline,
			'sections' => $sections,
			'outline_status' => isset( $d['outline_status'] ) ? (string) $d['outline_status'] : 'draft',
			'requirement_baseline' => $requirement_baseline,
			'seo_requirements' => array(
				'owner_url' => $target,
				'primary_keyword' => $keyword,
				'support_keywords' => $support_keywords,
				'support_keyword_mapping' => $mapping,
				'requirements' => $requirements,
				'intent_coverage' => 'pillar' === $article_type ? $intent_coverage : array(),
			),
		);
	}

	private function buildRequirementBaseline( int $keyword_id ): array {
		$requirements = $this->requirement_repository->findByKeywordId( $keyword_id, 'ACTIVE' );
		$baseline = array();

		foreach ( $requirements as $requirement ) {
			if ( null === $requirement->getId() ) {
				throw new \RuntimeException( 'Active Content Requirement must have a persisted ID before Blueprint generation.' );
			}

			$snapshot = $this->snapshot_repository->findByRequirementVersion(
				$requirement->getId(),
				$requirement->getVersion()
			);

			if ( null === $snapshot ) {
				throw new \RuntimeException(
					'Requirement Snapshot is missing for active Content Requirement: ' . $requirement->getKey()
				);
			}

			$baseline[] = array(
				'requirement_id' => $requirement->getId(),
				'requirement_key' => $requirement->getKey(),
				'requirement_type' => $requirement->getType(),
				'statement' => $requirement->getStatement(),
				'priority' => $requirement->getPriority(),
				'mandatory' => $requirement->isMandatory(),
				'verification_rule' => $requirement->getVerificationRule(),
				'status' => $requirement->getStatus(),
				'version' => $requirement->getVersion(),
				'finding_id' => $requirement->getFindingId(),
				'snapshot_id' => $snapshot->getId(),
				'snapshot' => array(
					'requirement_id' => $snapshot->getRequirementId(),
					'version' => $snapshot->getVersion(),
					'data' => $snapshot->getData(),
				),
			);
		}

		return $baseline;
	}
}
