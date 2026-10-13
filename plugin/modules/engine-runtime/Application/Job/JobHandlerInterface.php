<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Job;

defined( 'ABSPATH' ) || exit;

interface JobHandlerInterface {
	public function getJobType(): string;
	public function handle( JobRecord $job ): array;
}
