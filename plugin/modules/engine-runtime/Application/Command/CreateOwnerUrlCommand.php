<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Command;

defined( 'ABSPATH' ) || exit;

final class CreateOwnerUrlCommand {

	private $url;

	private $status;

	public function __construct( string $url, string $status = 'active' ) {
		$this->url    = $url;
		$this->status = $status;
	}

	public function getUrl(): string {
		return $this->url;
	}

	public function getStatus(): string {
		return $this->status;
	}
}
