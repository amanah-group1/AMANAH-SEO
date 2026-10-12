<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Job;

defined( 'ABSPATH' ) || exit;

final class JobContract {
	public const CREATED = 'created';
	public const QUEUED = 'queued';
	public const PROCESSING = 'processing';
	public const COMPLETED = 'completed';
	public const FAILED = 'failed';
	public const RETRY_SCHEDULED = 'retry_scheduled';
	public const CANCELLED = 'cancelled';
	public const STALE = 'stale';
	public const BLOCKED = 'blocked';

	public const HIGH = 'high';
	public const NORMAL = 'normal';
	public const LOW = 'low';

	public static function states(): array {
		return array( self::CREATED, self::QUEUED, self::PROCESSING, self::COMPLETED, self::FAILED, self::RETRY_SCHEDULED, self::CANCELLED, self::STALE, self::BLOCKED );
	}
}
