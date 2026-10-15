<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\ContentStrategyData;

defined( 'ABSPATH' ) || exit;

interface StrategyGeneratorInterface {
	public function execute( int $keyword_id ): ContentStrategyData;
}
