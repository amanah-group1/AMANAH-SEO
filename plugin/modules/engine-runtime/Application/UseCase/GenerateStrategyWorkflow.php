<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\ContentStrategyData;

defined( 'ABSPATH' ) || exit;

/**
 * Orchestrates the locked Strategy -> URL Mapping -> Content Plan branch.
 *
 * Existing pages stop at the strategy stage until the Optimization workflow
 * is available. New pages continue automatically into Content Plan generation.
 */
final class GenerateStrategyWorkflow {

	private $generate_strategy;
	private $generate_content_plan;

	public function __construct(
		StrategyGeneratorInterface $generate_strategy,
		ContentPlanGeneratorInterface $generate_content_plan
	) {
		$this->generate_strategy     = $generate_strategy;
		$this->generate_content_plan = $generate_content_plan;
	}

	public function execute( int $keyword_id ): ContentStrategyData {
		$strategy = $this->generate_strategy->execute( $keyword_id );
		$owner_url = isset( $strategy->data['owner_url'] ) && is_array( $strategy->data['owner_url'] )
			? $strategy->data['owner_url']
			: array();

		$is_existing_page = null !== ( $owner_url['wordpress_post_id'] ?? null )
			&& (int) $owner_url['wordpress_post_id'] > 0;

		if ( ! $is_existing_page ) {
			$this->generate_content_plan->execute( $keyword_id );
		}

		return $strategy;
	}
}
