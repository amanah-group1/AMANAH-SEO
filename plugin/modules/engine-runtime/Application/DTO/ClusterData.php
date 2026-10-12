<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\DTO;

use Amanah\SEOEngine\Domain\Cluster\Cluster;

defined( 'ABSPATH' ) || exit;

final class ClusterData {
	public $id;
	public $name;
	public $slug;
	public $description;
	public $status;

	public function __construct( ?int $id, string $name, string $slug, string $description, string $status ) {
		$this->id = $id;
		$this->name = $name;
		$this->slug = $slug;
		$this->description = $description;
		$this->status = $status;
	}

	public static function fromDomain( Cluster $cluster ): self {
		return new self( $cluster->getId(), $cluster->getName(), $cluster->getSlug(), $cluster->getDescription(), $cluster->getStatus() );
	}
}
