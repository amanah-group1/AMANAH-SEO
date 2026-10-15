<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\SearchIntentData;
use Amanah\SEOEngine\Application\Repository\SearchIntentRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class ListSearchIntents {
	private $repository;

	public function __construct( SearchIntentRepositoryInterface $repository ) {
		$this->repository = $repository;
	}

	public function execute( int $limit = 100 ): array {
		$items = array();
		foreach ( $this->repository->findAll( $limit ) as $intent ) {
			$items[] = SearchIntentData::fromDomain( $intent );
		}
		return $items;
	}
}
