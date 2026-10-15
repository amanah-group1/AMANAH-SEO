<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\KeywordData;
use Amanah\SEOEngine\Application\Query\GetKeywordQuery;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class GetKeyword {

	private $repository;

	public function __construct( KeywordRepositoryInterface $repository ) {
		$this->repository = $repository;
	}

	public function execute( GetKeywordQuery $query ): ?KeywordData {
		$keyword = $this->repository->findById( $query->getKeywordId() );

		return null === $keyword ? null : KeywordData::fromDomain( $keyword );
	}
}
