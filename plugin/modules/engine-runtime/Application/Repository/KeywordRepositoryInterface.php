<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\Keyword\Keyword;

defined( 'ABSPATH' ) || exit;

interface KeywordRepositoryInterface {

	public function findById( int $id ): ?Keyword;

	public function findAll( int $limit = 100 ): array;

	public function save( Keyword $keyword ): Keyword;
}
