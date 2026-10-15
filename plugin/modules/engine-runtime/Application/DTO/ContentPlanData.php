<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\DTO;

use Amanah\SEOEngine\Domain\ContentPlan\ContentPlan;

defined( 'ABSPATH' ) || exit;

final class ContentPlanData {

	private $id;
	private $keyword_id;
	private $strategy_id;
	private $data;
	private $version;
	private $status;

	private function __construct( ContentPlan $plan ) {
		$this->id         = $plan->getId();
		$this->keyword_id = $plan->getKeywordId();
		$this->strategy_id = $plan->getStrategyId();
		$this->data       = $plan->getData();
		$this->version    = $plan->getVersion();
		$this->status     = $plan->getStatus();
	}

	public static function fromDomain( ContentPlan $plan ): self {
		return new self( $plan );
	}

	public function toArray(): array {
		return array(
			'id' => $this->id,
			'keyword_id' => $this->keyword_id,
			'strategy_id' => $this->strategy_id,
			'data' => $this->data,
			'version' => $this->version,
			'status' => $this->status,
		);
	}
}
