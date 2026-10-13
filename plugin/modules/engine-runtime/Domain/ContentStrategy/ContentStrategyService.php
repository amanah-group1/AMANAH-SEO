<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\ContentStrategy;

use Amanah\SEOEngine\Domain\Keyword\Keyword;

defined( 'ABSPATH' ) || exit;

final class ContentStrategyService {

	public function validate( ContentStrategy $strategy, Keyword $keyword ): void {
		if ( null !== $keyword->getId() && $strategy->getKeywordId() !== $keyword->getId() ) {
			throw new \DomainException( 'Strategy keyword relationship is invalid.' );
		}
	}

	public function buildData(
		Keyword $keyword,
		?array $search_intent,
		?array $cluster,
		?array $owner_url,
		array $relationships,
		array $intent_coverage = array(),
		array $support_keywords = array()
	): array {
		$issues = array();

		if ( 'support' === $keyword->getArticleType() ) {
			if ( null === $search_intent ) {
				$issues[] = 'missing_search_intent';
			}

			if ( null === $cluster ) {
				$issues[] = 'missing_cluster';
			}
		}

		if ( null === $owner_url ) {
			$issues[] = 'missing_owner_url';
		}

		$content_requirement = 'new_content';

		if ( null !== $owner_url && ! empty( $owner_url['wordpress_post_id'] ) ) {
			$content_requirement = 'optimization';
		}

		return array(
			'strategy_version' => 1,
			'source' => 'core',
			'keyword' => array(
				'id' => $keyword->getId(),
				'phrase' => $keyword->getPhrase(),
				'type' => $keyword->getKeywordType(),
				'article_type' => $keyword->getArticleType(),
				'pillar_keyword_id' => $keyword->getPillarKeywordId(),
			),
			'support_keywords' => 'pillar' === $keyword->getArticleType() ? array_values( $support_keywords ) : array(),
			'search_intent' => $search_intent,
			'intent_coverage' => 'pillar' === $keyword->getArticleType() ? $intent_coverage : array(),
			'cluster' => $cluster,
			'owner_url' => $owner_url,
			'content_requirement' => $content_requirement,
			'relationships' => $relationships,
			'issues' => $issues,
		);
	}
}
