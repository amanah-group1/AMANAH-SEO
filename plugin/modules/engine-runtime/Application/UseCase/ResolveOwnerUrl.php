<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Command\CreateOwnerUrlCommand;
use Amanah\SEOEngine\Application\DTO\OwnerUrlData;
use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;
use Amanah\SEOEngine\Domain\OwnerUrl\OwnerUrl;

defined( 'ABSPATH' ) || exit;

final class ResolveOwnerUrl {

	private $repository;

	public function __construct( OwnerUrlRepositoryInterface $repository ) {
		$this->repository = $repository;
	}

	public function execute( string $url ): OwnerUrlData {
		$normalized_url = OwnerUrl::normalize( $url );
		$existing = $this->repository->findByNormalizedUrl( $normalized_url );

		if ( null !== $existing ) {
			return OwnerUrlData::fromDomain( $existing );
		}

		$owner_url = new OwnerUrl( $url );
		return OwnerUrlData::fromDomain( $this->repository->save( $owner_url ) );
	}
}
