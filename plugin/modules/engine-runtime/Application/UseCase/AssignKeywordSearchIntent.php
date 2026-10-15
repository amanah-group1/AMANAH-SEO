<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\KeywordData;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\SearchIntentRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class AssignKeywordSearchIntent {
	private $keyword_repository;
	private $search_intent_repository;

	public function __construct(
		KeywordRepositoryInterface $keyword_repository,
		SearchIntentRepositoryInterface $search_intent_repository
	) {
		$this->keyword_repository = $keyword_repository;
		$this->search_intent_repository = $search_intent_repository;
	}

	public function execute( int $keyword_id, int $search_intent_id ): KeywordData {
		$keyword = $this->keyword_repository->findById( $keyword_id );

		if ( null === $keyword ) {
			throw new \RuntimeException( 'Keyword not found.' );
		}

		$intent = $this->search_intent_repository->findById( $search_intent_id );

		if ( null === $intent ) {
			throw new \RuntimeException( 'Search intent not found.' );
		}

		$keyword->assignSearchIntent( $intent->getId() );

		return KeywordData::fromDomain( $this->keyword_repository->save( $keyword ) );
	}
}
