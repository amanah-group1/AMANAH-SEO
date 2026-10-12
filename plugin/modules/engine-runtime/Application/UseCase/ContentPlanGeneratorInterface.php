<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\ContentPlanData;

defined( 'ABSPATH' ) || exit;

interface ContentPlanGeneratorInterface {
	public function execute( int $keyword_id ): ContentPlanData;
}
