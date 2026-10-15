<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\SearchIntentData;
use Amanah\SEOEngine\Application\Repository\SearchIntentRepositoryInterface;
use Amanah\SEOEngine\Domain\SearchIntent\SearchIntent;
use Amanah\SEOEngine\Domain\SearchIntent\SearchIntentService;

defined( 'ABSPATH' ) || exit;

final class ResolveSearchIntent {
	private $repository;
	private $service;

	public function __construct( SearchIntentRepositoryInterface $repository, SearchIntentService $service ) {
		$this->repository = $repository;
		$this->service = $service;
	}

	public function execute( string $slug, string $name, string $description = '' ): SearchIntentData {
		$slug = sanitize_key( $slug );
		$name = trim( $name );

		if ( '' === $slug || '' === $name ) {
			throw new \InvalidArgumentException( 'Search intent identity is required.' );
		}

		$existing = $this->repository->findBySlug( $slug );

		if ( null !== $existing ) {
			return SearchIntentData::fromDomain( $existing );
		}

		$intent = new SearchIntent( $slug, $name, $description );
		$this->service->validate( $intent );

		return SearchIntentData::fromDomain( $this->repository->save( $intent ) );
	}
}
