<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Command;

defined( 'ABSPATH' ) || exit;

final class CreateSearchIntentCommand {
	private $slug;
	private $name;
	private $description;

	public function __construct( string $slug, string $name, string $description = '' ) {
		$this->slug = $slug;
		$this->name = $name;
		$this->description = $description;
	}
	public function getSlug(): string { return $this->slug; }
	public function getName(): string { return $this->name; }
	public function getDescription(): string { return $this->description; }
}
