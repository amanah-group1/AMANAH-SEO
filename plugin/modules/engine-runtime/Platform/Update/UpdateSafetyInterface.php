<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Platform\Update;

defined( 'ABSPATH' ) || exit;

interface UpdateSafetyInterface {

	public function createBackup(): bool;

	public function restoreBackup(): bool;
}
