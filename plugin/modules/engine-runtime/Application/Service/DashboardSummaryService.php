<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Service;

use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentPlanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\QualityResultRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class DashboardSummaryService {

	private $keywords;
	private $owner_urls;
	private $strategies;
	private $plans;
	private $briefs;
	private $blueprints;
	private $content;
	private $quality;

	public function __construct(
		KeywordRepositoryInterface $keywords,
		OwnerUrlRepositoryInterface $owner_urls,
		ContentStrategyRepositoryInterface $strategies,
		ContentPlanRepositoryInterface $plans,
		ContentBriefRepositoryInterface $briefs,
		PageBlueprintRepositoryInterface $blueprints,
		ContentRepositoryInterface $content,
		QualityResultRepositoryInterface $quality
	) {
		$this->keywords   = $keywords;
		$this->owner_urls = $owner_urls;
		$this->strategies  = $strategies;
		$this->plans       = $plans;
		$this->briefs      = $briefs;
		$this->blueprints  = $blueprints;
		$this->content     = $content;
		$this->quality     = $quality;
	}

	public function build(): array {
		$keywords = $this->keywords->findAll( 100 );

		$summary = array(
			'keywords' => array(
				'total'      => count( $keywords ),
				'mapped'     => 0,
				'unassigned' => 0,
			),
			'strategy' => array(
				'total'   => count( $keywords ),
				'covered' => 0,
				'missing' => 0,
			),
			'content_pipeline' => array(
				'plan'      => 0,
				'brief'     => 0,
				'blueprint' => 0,
				'content'   => 0,
			),
			'quality' => array(
				'checked'  => 0,
				'passed'   => 0,
				'warning'  => 0,
				'failed'   => 0,
			),
			'priority_findings' => array(),
		);

		foreach ( $keywords as $keyword ) {
			$keyword_id = (int) $keyword->getId();

			if ( null !== $keyword->getOwnerUrlId() ) {
				++$summary['keywords']['mapped'];
			} else {
				++$summary['keywords']['unassigned'];
				$summary['priority_findings'][] = $this->finding(
					'UNASSIGNED_KEYWORD',
					'warning',
					$keyword_id,
					$keyword->getPhrase(),
					'Keyword belum memiliki Owner URL.',
					'Review URL Mapping.'
				);
			}

			$strategy = $this->strategies->findByKeywordId( $keyword_id );
			if ( null !== $strategy ) {
				++$summary['strategy']['covered'];
			} else {
				++$summary['strategy']['missing'];
				$summary['priority_findings'][] = $this->finding(
					'MISSING_STRATEGY',
					'warning',
					$keyword_id,
					$keyword->getPhrase(),
					'Keyword belum memiliki SEO Strategy.',
					'Review Strategy.'
				);
			}

			$plan = $this->plans->findByKeywordId( $keyword_id );
			if ( null !== $plan ) {
				++$summary['content_pipeline']['plan'];
			} else {
				$summary['priority_findings'][] = $this->finding(
					'MISSING_CONTENT_PLAN',
					'warning',
					$keyword_id,
					$keyword->getPhrase(),
					'Keyword belum memiliki Content Plan.',
					'Review Content Plan.'
				);
			}

			$brief = $this->briefs->findByKeywordId( $keyword_id );
			if ( null !== $brief ) {
				++$summary['content_pipeline']['brief'];
			} else {
				$summary['priority_findings'][] = $this->finding(
					'MISSING_CONTENT_BRIEF',
					'info',
					$keyword_id,
					$keyword->getPhrase(),
					'Keyword belum memiliki Content Brief.',
					'Review Content Plan dan buat Content Brief.'
				);
			}

			$blueprint = $this->blueprints->findByKeywordId( $keyword_id );
			if ( null !== $blueprint ) {
				++$summary['content_pipeline']['blueprint'];
			} else {
				$summary['priority_findings'][] = $this->finding(
					'MISSING_PAGE_BLUEPRINT',
					'info',
					$keyword_id,
					$keyword->getPhrase(),
					'Keyword belum memiliki Page Blueprint.',
					'Review Content Brief dan buat Page Blueprint.'
				);
			}

			if ( null !== $blueprint ) {
				$content = $this->content->findLatestByBlueprintId( (int) $blueprint->getId() );
				if ( null !== $content ) {
					++$summary['content_pipeline']['content'];
					$quality = $this->quality->findLatestByContentId( (int) $content->getId() );
					if ( null !== $quality ) {
						++$summary['quality']['checked'];
						$status = $quality->getStatus();
						if ( 'passed' === $status ) {
							++$summary['quality']['passed'];
						} elseif ( 'warning' === $status ) {
							++$summary['quality']['warning'];
						} elseif ( 'failed' === $status ) {
							++$summary['quality']['failed'];
							$summary['priority_findings'][] = $this->finding(
								'QUALITY_FAILED',
								'critical',
								$keyword_id,
								$keyword->getPhrase(),
								'Konten terakhir memiliki hasil Quality Evaluation failed.',
								'Review Quality Evaluation.'
							);
						}
					}
				}
			}
		}

		return $summary;
	}

	private function finding( string $type, string $severity, int $entity_id, string $entity, string $message, string $action ): array {
		return array(
			'type'       => $type,
			'severity'   => $severity,
			'entity_id'  => $entity_id,
			'entity'     => $entity,
			'message'    => $message,
			'action'     => $action,
		);
	}
}
