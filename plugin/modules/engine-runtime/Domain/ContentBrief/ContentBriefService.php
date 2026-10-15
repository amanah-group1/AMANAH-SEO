<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\ContentBrief;

use Amanah\SEOEngine\Domain\ContentPlan\ContentPlan;
use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategy;

defined( 'ABSPATH' ) || exit;

final class ContentBriefService {
	public function validate(
		ContentBrief $brief,
		ContentPlan $plan,
		ContentStrategy $strategy
	): void {
		if ( $brief->getKeywordId() !== $plan->getKeywordId() ) {
			throw new \DomainException( 'Content Brief keyword relationship is invalid.' );
		}

		if ( null !== $brief->getStrategyId() && $brief->getStrategyId() !== $strategy->getId() ) {
			throw new \DomainException( 'Content Brief strategy relationship is invalid.' );
		}

		if ( null !== $plan->getStrategyId() && $plan->getStrategyId() !== $strategy->getId() ) {
			throw new \DomainException( 'Content Plan and SEO Strategy relationship is invalid.' );
		}

		if ( $brief->getVersion() < 1 || array() === $brief->getData() ) {
			throw new \DomainException( 'Content Brief must contain versioned data.' );
		}
	}

	public function buildData( ContentPlan $plan, ContentStrategy $strategy ): array {
		$strategy_data = $strategy->getData();
		$plan_data     = $plan->getData();

		$keyword = isset( $strategy_data['keyword'] ) && is_array( $strategy_data['keyword'] )
			? $strategy_data['keyword']
			: array();

		$support_keywords = isset( $strategy_data['support_keywords'] ) && is_array( $strategy_data['support_keywords'] )
			? array_values( $strategy_data['support_keywords'] )
			: array();

		$owner_url = isset( $strategy_data['owner_url'] ) && is_array( $strategy_data['owner_url'] )
			? $strategy_data['owner_url']
			: null;

		$search_intent = isset( $strategy_data['search_intent'] ) && is_array( $strategy_data['search_intent'] )
			? $strategy_data['search_intent']
			: null;

		$intent_coverage = isset( $strategy_data['intent_coverage'] ) && is_array( $strategy_data['intent_coverage'] )
			? $strategy_data['intent_coverage']
			: array();

		$cluster = isset( $strategy_data['cluster'] ) && is_array( $strategy_data['cluster'] )
			? $strategy_data['cluster']
			: null;

		$content_requirement = isset( $plan_data['content_requirement'] ) ? (string) $plan_data['content_requirement'] : 'new_content';
		$content_role = isset( $plan_data['content_role'] ) ? (string) $plan_data['content_role'] : 'supporting';
		$article_type = isset( $plan_data['article_type'] ) ? (string) $plan_data['article_type'] : 'supporting';
		$target_url = isset( $plan_data['target_url'] ) ? (string) $plan_data['target_url'] : '';
		$issues = isset( $strategy_data['issues'] ) && is_array( $strategy_data['issues'] ) ? array_values( $strategy_data['issues'] ) : array();

		$primary_phrase = isset( $keyword['phrase'] ) ? trim( (string) $keyword['phrase'] ) : '';
		$outline_builder = new ArticleOutlineBuilder();
		$outline_result = $outline_builder->build( $primary_phrase, $article_type, $intent_coverage, $support_keywords );
		$article_outline = $outline_result['article_outline'];
		$support_keyword_mapping = $outline_result['support_keyword_mapping'];

		return array(
			'brief_version' => 1,
			'source' => 'core',
			'keyword' => $keyword,
			'support_keywords' => $support_keywords,
			'target_url' => $target_url,
			'content_requirement' => $content_requirement,
			'content_role' => $content_role,
			'article_type' => $article_type,
			'search_intent' => $search_intent,
			'intent_coverage' => 'pillar' === $article_type ? $intent_coverage : array(),
			'cluster' => $cluster,
			'content_objective' => 'pillar' === $article_type
				? 'Create comprehensive pillar content that addresses all covered search intents and all assigned support keywords while maintaining one coherent topic.'
				: 'Create content that addresses the target keyword and follows the current SEO strategy.',
			'content_direction' => 'pillar' === $article_type
				? 'Cover every intent listed in intent_coverage. Integrate every supplied support keyword naturally into relevant sections; never list keywords unnaturally or stuff them.'
				: 'Use the keyword, intent, cluster, owner URL, and content requirement as the governing inputs for the next production stage.',
			'strategy_id' => $strategy->getId(),
			'content_plan_id' => $plan->getId(),
			'issues' => $issues,
			'editable_outline_version' => 1,
			'article_outline' => $article_outline,
			'support_keyword_mapping' => $support_keyword_mapping,
			'outline_status' => 'draft',
		);
	}
}
