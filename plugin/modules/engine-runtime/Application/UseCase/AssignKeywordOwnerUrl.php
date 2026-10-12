<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Command\AssignKeywordOwnerUrlCommand;
use Amanah\SEOEngine\Application\DTO\KeywordData;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;
use Amanah\SEOEngine\Domain\OwnerUrl\OwnerUrlService;

defined( 'ABSPATH' ) || exit;

final class AssignKeywordOwnerUrl {

	private $keyword_repository;

	private $owner_url_repository;

	private $owner_url_service;

	public function __construct(
		KeywordRepositoryInterface $keyword_repository,
		OwnerUrlRepositoryInterface $owner_url_repository,
		OwnerUrlService $owner_url_service
	) {
		$this->keyword_repository  = $keyword_repository;
		$this->owner_url_repository = $owner_url_repository;
		$this->owner_url_service    = $owner_url_service;
	}

	public function execute( AssignKeywordOwnerUrlCommand $command ): KeywordData {
		$keyword = $this->keyword_repository->findById( $command->getKeywordId() );

		if ( null === $keyword ) {
			throw new \RuntimeException( 'Keyword not found.' );
		}

		$owner_url = $this->owner_url_repository->findById( $command->getOwnerUrlId() );

		if ( null === $owner_url ) {
			throw new \RuntimeException( 'Owner URL not found.' );
		}

		$this->owner_url_service->assign( $keyword, $owner_url );

		return KeywordData::fromDomain(
			$this->keyword_repository->save( $keyword )
		);
	}
}
