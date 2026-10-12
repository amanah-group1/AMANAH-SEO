<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Command\CreateOwnerUrlCommand;
use Amanah\SEOEngine\Application\DTO\OwnerUrlData;
use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;
use Amanah\SEOEngine\Domain\OwnerUrl\OwnerUrl;

defined( 'ABSPATH' ) || exit;

final class CreateOwnerUrl {

	private $repository;

	public function __construct( OwnerUrlRepositoryInterface $repository ) {
		$this->repository = $repository;
	}

	public function execute( CreateOwnerUrlCommand $command ): OwnerUrlData {
		$owner_url = new OwnerUrl(
			$command->getUrl(),
			$command->getStatus()
		);

		$existing = $this->repository->findByNormalizedUrl( $owner_url->getNormalizedUrl() );

		if ( null !== $existing ) {
			return OwnerUrlData::fromDomain( $existing );
		}

		return OwnerUrlData::fromDomain( $this->repository->save( $owner_url ) );
	}
}
