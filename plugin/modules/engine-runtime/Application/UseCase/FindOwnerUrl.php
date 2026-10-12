<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\OwnerUrlData;
use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;

defined( 'ABSPATH' ) || exit;

final class FindOwnerUrl {

	private $repository;

	public function __construct( OwnerUrlRepositoryInterface $repository ) {
		$this->repository = $repository;
	}

	public function execute( int $id ): ?OwnerUrlData {
		$owner_url = $this->repository->findById( $id );

		return null !== $owner_url ? OwnerUrlData::fromDomain( $owner_url ) : null;
	}
}
