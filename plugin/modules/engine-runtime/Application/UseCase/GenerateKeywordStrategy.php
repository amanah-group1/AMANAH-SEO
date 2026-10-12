<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\ContentStrategyData;
use Amanah\SEOEngine\Application\Repository\ClusterRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\SearchIntentRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategy;
use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategyService;

defined( 'ABSPATH' ) || exit;

final class GenerateKeywordStrategy implements StrategyGeneratorInterface {

	private $keyword_repository;
	private $search_intent_repository;
	private $cluster_repository;
	private $owner_url_repository;
	private $strategy_repository;
	private $relationship_analyzer;
	private $strategy_service;

	public function __construct(
		KeywordRepositoryInterface $keyword_repository,
		SearchIntentRepositoryInterface $search_intent_repository,
		ClusterRepositoryInterface $cluster_repository,
		OwnerUrlRepositoryInterface $owner_url_repository,
		ContentStrategyRepositoryInterface $strategy_repository,
		AnalyzeKeywordRelationships $relationship_analyzer,
		ContentStrategyService $strategy_service
	) {
		$this->keyword_repository = $keyword_repository;
		$this->search_intent_repository = $search_intent_repository;
		$this->cluster_repository = $cluster_repository;
		$this->owner_url_repository = $owner_url_repository;
		$this->strategy_repository = $strategy_repository;
		$this->relationship_analyzer = $relationship_analyzer;
		$this->strategy_service = $strategy_service;
	}

	public function execute( int $keyword_id ): ContentStrategyData {
		$keyword = $this->keyword_repository->findById( $keyword_id );

		if ( null === $keyword ) {
			throw new \RuntimeException( 'Keyword not found.' );
		}

		$search_intent = null;
		if ( null !== $keyword->getSearchIntentId() ) {
			$intent = $this->search_intent_repository->findById( $keyword->getSearchIntentId() );
			if ( null !== $intent ) {
				$search_intent = array(
					'id' => $intent->getId(),
					'slug' => $intent->getSlug(),
					'name' => $intent->getName(),
				);
			}
		}

		$intent_coverage = array();
		if ( 'pillar' === $keyword->getArticleType() ) {
			foreach ( $this->search_intent_repository->findAll() as $intent ) {
				$intent_coverage[] = array(
					'id' => $intent->getId(),
					'slug' => $intent->getSlug(),
					'name' => $intent->getName(),
				);
			}
		}

		$support_keywords = array();
		if ( 'pillar' === $keyword->getArticleType() && null !== $keyword->getId() ) {
			foreach ( $this->keyword_repository->findAll( 100 ) as $support ) {
				if (
					'support' !== $support->getArticleType()
					|| (int) $support->getPillarKeywordId() !== (int) $keyword->getId()
				) {
					continue;
				}

				$support_keywords[] = array(
					'id' => $support->getId(),
					'phrase' => $support->getPhrase(),
					'type' => $support->getKeywordType(),
					'article_type' => $support->getArticleType(),
					'pillar_keyword_id' => $support->getPillarKeywordId(),
				);
			}
		}

		$cluster = null;
		if ( null !== $keyword->getClusterId() ) {
			$item = $this->cluster_repository->findById( $keyword->getClusterId() );
			if ( null !== $item ) {
				$cluster = array(
					'id' => $item->getId(),
					'slug' => $item->getSlug(),
					'name' => $item->getName(),
				);
			}
		}

		$owner_url = null;
		if ( null !== $keyword->getOwnerUrlId() ) {
			$item = $this->owner_url_repository->findById( $keyword->getOwnerUrlId() );
			if ( null !== $item ) {
				$owner_url = array(
					'id' => $item->getId(),
					'url' => $item->getUrl(),
					'wordpress_post_id' => $item->getWordPressPostId(),
					'post_type' => $item->getPostType(),
					'status' => $item->getStatus(),
				);
			}
		}

		if ( null === $owner_url ) {
			throw new \RuntimeException( 'Owner URL must be assigned before generating SEO Strategy.' );
		}

		if ( 'support' === $keyword->getArticleType() && null === $search_intent ) {
			throw new \RuntimeException( 'Search Intent must be assigned before generating SEO Strategy for Support.' );
		}

		if ( 'support' === $keyword->getArticleType() && null === $cluster ) {
			throw new \RuntimeException( 'Cluster must be assigned before generating SEO Strategy for Support.' );
		}

		$relationship_data = array();
		foreach ( $this->relationship_analyzer->execute( $keyword_id ) as $relationship ) {
			$relationship_data[] = $relationship->toArray();
		}

		$existing = $this->strategy_repository->findByKeywordId( $keyword_id );
		$version = null !== $existing ? $existing->getVersion() + 1 : 1;

		$data = $this->strategy_service->buildData(
			$keyword,
			$search_intent,
			$cluster,
			$owner_url,
			$relationship_data,
			$intent_coverage,
			$support_keywords
		);

		$strategy = new ContentStrategy(
			(int) $keyword->getId(),
			$data,
			$version,
			'draft'
		);

		$this->strategy_service->validate( $strategy, $keyword );

		return ContentStrategyData::fromDomain( $this->strategy_repository->save( $strategy ) );
	}
}
