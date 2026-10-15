<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Platform\Update;

defined( 'ABSPATH' ) || exit;

interface UpdateExecutorInterface {

	public function install( string $package_path ): bool;
}
