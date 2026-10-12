<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\DTO;

use Amanah\SEOEngine\Domain\SearchIntent\SearchIntent;

defined( 'ABSPATH' ) || exit;

final class SearchIntentData {
	private $id;
	private $slug;
	private $name;
	private $description;

	public function __construct( ?int $id, string $slug, string $name, string $description ) {
		$this->id = $id;
		$this->slug = $slug;
		$this->name = $name;
		$this->description = $description;
	}

	public static function fromDomain( SearchIntent $intent ): self {
		return new self( $intent->getId(), $intent->getSlug(), $intent->getName(), $intent->getDescription() );
	}

	public function toArray(): array {
		return array( 'id' => $this->id, 'slug' => $this->slug, 'name' => $this->name, 'description' => $this->description );
	}
}
