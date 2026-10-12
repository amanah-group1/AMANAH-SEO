<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Port;

defined( 'ABSPATH' ) || exit;

interface ContentPublisherInterface {

	public function createDraft( array $data ): int;

	public function updateDraft( array $data ): int;

	public function publish( array $data ): int;
}
