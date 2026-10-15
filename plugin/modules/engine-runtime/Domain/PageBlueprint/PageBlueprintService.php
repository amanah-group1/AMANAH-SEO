<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\PageBlueprint;

use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;

defined( 'ABSPATH' ) || exit;

final class PageBlueprintService {

	public function validate( PageBlueprint $blueprint, ContentBrief $brief ): void {
		if ( $blueprint->getKeywordId() !== $brief->getKeywordId() ) {
			throw new \DomainException( 'Page Blueprint keyword relationship is invalid.' );
		}

		if ( null !== $blueprint->getContentBriefId() && $blueprint->getContentBriefId() !== $brief->getId() ) {
			throw new \DomainException( 'Page Blueprint Content Brief relationship is invalid.' );
		}

		if ( $blueprint->getVersion() < 1 || array() === $blueprint->getData() ) {
			throw new \DomainException( 'Page Blueprint must contain valid structure.' );
		}

		$data = $blueprint->getData();

		if ( ! isset( $data['blueprint_version'] ) ) {
			throw new \DomainException( 'Page Blueprint version data is missing.' );
		}

		if ( ! isset( $data['sections'] ) || ! is_array( $data['sections'] ) || empty( $data['sections'] ) ) {
			throw new \DomainException( 'Page Blueprint must contain at least one section.' );
		}

		if ( 'pillar' === ( $data['article_type'] ?? '' ) ) {
			$coverage = isset( $data['intent_coverage'] ) && is_array( $data['intent_coverage'] )
				? $data['intent_coverage']
				: array();

			if ( empty( $coverage ) ) {
				throw new \DomainException( 'Pillar Page Blueprint must cover at least one search intent.' );
			}
		}
	}
}
