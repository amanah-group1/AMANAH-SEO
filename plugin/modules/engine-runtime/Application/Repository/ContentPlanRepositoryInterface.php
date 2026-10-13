<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\ContentPlan\ContentPlan;

defined( 'ABSPATH' ) || exit;

interface ContentPlanRepositoryInterface {
	public function findById( int $id ): ?ContentPlan;
	public function findByKeywordId( int $keyword_id ): ?ContentPlan;
	public function findAll( int $limit = 100 ): array;
	public function save( ContentPlan $plan ): ContentPlan;
}
