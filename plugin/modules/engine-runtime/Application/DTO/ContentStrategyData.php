<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\DTO;

use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategy;

defined( 'ABSPATH' ) || exit;

final class ContentStrategyData {
	public $id;
	public $keyword_id;
	public $data;
	public $version;
	public $status;

	public function __construct( ?int $id, int $keyword_id, array $data, int $version, string $status ) {
		$this->id = $id;
		$this->keyword_id = $keyword_id;
		$this->data = $data;
		$this->version = $version;
		$this->status = $status;
	}

	public static function fromDomain( ContentStrategy $strategy ): self {
		return new self(
			$strategy->getId(),
			$strategy->getKeywordId(),
			$strategy->getData(),
			$strategy->getVersion(),
			$strategy->getStatus()
		);
	}

	public function toArray(): array {
		return array(
			'id' => $this->id,
			'keyword_id' => $this->keyword_id,
			'data' => $this->data,
			'version' => $this->version,
			'status' => $this->status,
		);
	}
}
