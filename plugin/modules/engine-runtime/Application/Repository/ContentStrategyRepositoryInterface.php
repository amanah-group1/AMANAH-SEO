<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategy;

defined( 'ABSPATH' ) || exit;

interface ContentStrategyRepositoryInterface {
	public function findById( int $id ): ?ContentStrategy;
	public function findByKeywordId( int $keyword_id ): ?ContentStrategy;
	public function findAll( int $limit = 100 ): array;
	public function save( ContentStrategy $strategy ): ContentStrategy;
}
