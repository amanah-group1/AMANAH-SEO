<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Error;

defined( 'ABSPATH' ) || exit;

final class ApplicationError {

	private $code;
	private $message;

	public function __construct( string $code, string $message ) {
		$this->code    = $code;
		$this->message = $message;
	}

	public function getCode(): string { return $this->code; }
	public function getMessage(): string { return $this->message; }

	public function toArray(): array {
		return array(
			'code'    => $this->code,
			'message' => $this->message,
		);
	}
}
