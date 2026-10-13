<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\KeywordData;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class ListKeywords {

	private $repository;

	public function __construct( KeywordRepositoryInterface $repository ) {
		$this->repository = $repository;
	}

	public function execute( int $limit = 100 ): array {
		$items = array();

		foreach ( $this->repository->findAll( $limit ) as $keyword ) {
			$items[] = KeywordData::fromDomain( $keyword );
		}

		return $items;
	}
}
