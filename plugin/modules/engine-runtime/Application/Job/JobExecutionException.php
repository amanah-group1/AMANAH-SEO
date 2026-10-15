<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Job;

defined( 'ABSPATH' ) || exit;

final class JobExecutionException extends \RuntimeException {
	private $category;
	private $retryable;

	public function __construct( string $category, string $message, bool $retryable = false ) {
		parent::__construct( $message );
		$this->category = $category;
		$this->retryable = $retryable;
	}

	public function getCategory(): string { return $this->category; }
	public function isRetryable(): bool { return $this->retryable; }
}
