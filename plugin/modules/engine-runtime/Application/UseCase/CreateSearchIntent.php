<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Command\CreateSearchIntentCommand;
use Amanah\SEOEngine\Application\DTO\SearchIntentData;
use Amanah\SEOEngine\Application\Repository\SearchIntentRepositoryInterface;
use Amanah\SEOEngine\Domain\SearchIntent\SearchIntent;
use Amanah\SEOEngine\Domain\SearchIntent\SearchIntentService;

defined( 'ABSPATH' ) || exit;

final class CreateSearchIntent {
	private $repository;
	private $service;

	public function __construct( SearchIntentRepositoryInterface $repository, SearchIntentService $service ) {
		$this->repository = $repository;
		$this->service = $service;
	}

	public function execute( CreateSearchIntentCommand $command ): SearchIntentData {
		$intent = new SearchIntent( $command->getSlug(), $command->getName(), $command->getDescription() );
		$this->service->validate( $intent );
		return SearchIntentData::fromDomain( $this->repository->save( $intent ) );
	}
}
