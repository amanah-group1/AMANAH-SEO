<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\ContentPlan;

use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategy;

defined( 'ABSPATH' ) || exit;

final class ContentPlanService {

	public function validate( ContentPlan $plan, ContentStrategy $strategy ): void {
		if ( $plan->getKeywordId() !== $strategy->getKeywordId() ) {
			throw new \DomainException( 'Content Plan keyword relationship is invalid.' );
		}

		if ( null !== $plan->getStrategyId() && $plan->getStrategyId() !== $strategy->getId() ) {
			throw new \DomainException( 'Content Plan strategy relationship is invalid.' );
		}
	}

	public function buildData( ContentStrategy $strategy ): array {
		$strategy_data = $strategy->getData();
		$keyword       = isset( $strategy_data['keyword'] ) && is_array( $strategy_data['keyword'] )
			? $strategy_data['keyword']
			: array();

		$owner_url = isset( $strategy_data['owner_url'] ) && is_array( $strategy_data['owner_url'] )
			? $strategy_data['owner_url']
			: null;

		$content_requirement = isset( $strategy_data['content_requirement'] )
			? (string) $strategy_data['content_requirement']
			: 'new_content';

		$intent_coverage = isset( $strategy_data['intent_coverage'] ) && is_array( $strategy_data['intent_coverage'] )
			? $strategy_data['intent_coverage']
			: array();

		$support_keywords = isset( $strategy_data['support_keywords'] ) && is_array( $strategy_data['support_keywords'] )
			? array_values( $strategy_data['support_keywords'] )
			: array();

		$content_type = 'article';
		$content_role = 'supporting';
		$article_type = isset( $keyword['article_type'] ) ? (string) $keyword['article_type'] : 'pillar';

		if ( 'pillar' === $article_type ) {
			$content_role = 'pillar';
		}

		return array(
			'plan_version' => 1,
			'source' => 'core',
			'keyword' => $keyword,
			'article_type' => $article_type,
			'intent_coverage' => 'pillar' === $article_type ? $intent_coverage : array(),
			'support_keywords' => 'pillar' === $article_type ? $support_keywords : array(),
			'support_coverage_count' => 'pillar' === $article_type ? count( $support_keywords ) : 0,
			'strategy_id' => $strategy->getId(),
			'target_url' => is_array( $owner_url ) && isset( $owner_url['url'] ) ? $owner_url['url'] : null,
			'content_type' => $content_type,
			'content_role' => $content_role,
			'content_requirement' => $content_requirement,
			'planning_status' => 'planned',
		);
	}
}
