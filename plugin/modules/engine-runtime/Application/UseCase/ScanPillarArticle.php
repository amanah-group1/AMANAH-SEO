<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;
use Amanah\SEOEngine\Infrastructure\PillarArticle\PillarArticleScanner;

defined( 'ABSPATH' ) || exit;

final class ScanPillarArticle {

	private $keyword_repository;
	private $owner_url_repository;
	private $scanner;

	public function __construct(
		KeywordRepositoryInterface $keyword_repository,
		OwnerUrlRepositoryInterface $owner_url_repository,
		PillarArticleScanner $scanner
	) {
		$this->keyword_repository  = $keyword_repository;
		$this->owner_url_repository = $owner_url_repository;
		$this->scanner              = $scanner;
	}

	public function execute( int $pillar_keyword_id, string $support_keyword = '' ): array {
		$pillar = $this->keyword_repository->findById( $pillar_keyword_id );

		if ( null === $pillar || 'pillar' !== $pillar->getArticleType() ) {
			throw new \RuntimeException( 'Pillar keyword tidak ditemukan atau bukan artikel Pilar.' );
		}

		$owner_url_id = $pillar->getOwnerUrlId();

		if ( null === $owner_url_id ) {
			throw new \RuntimeException( 'Artikel Pilar belum memiliki Owner URL.' );
		}

		$owner_url = $this->owner_url_repository->findById( $owner_url_id );

		if ( null === $owner_url ) {
			throw new \RuntimeException( 'Owner URL artikel Pilar tidak ditemukan.' );
		}

		$result = $this->scanner->scan( $owner_url->getUrl(), trim( $support_keyword ) );
		$result['pillar_keyword_id'] = $pillar_keyword_id;
		$result['pillar_keyword']     = $pillar->getPhrase();
		$result['owner_url_id']       = $owner_url_id;

		return $result;
	}
}
