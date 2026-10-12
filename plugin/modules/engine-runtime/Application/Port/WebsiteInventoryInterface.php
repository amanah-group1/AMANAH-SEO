<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Port;

defined( 'ABSPATH' ) || exit;

interface WebsiteInventoryInterface {

	public function collect(): array;
}
