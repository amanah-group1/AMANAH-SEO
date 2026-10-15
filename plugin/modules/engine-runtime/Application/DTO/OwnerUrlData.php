<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\DTO;

use Amanah\SEOEngine\Domain\OwnerUrl\OwnerUrl;

defined( 'ABSPATH' ) || exit;

final class OwnerUrlData {

	private $id;

	private $url;

	private $normalized_url;

	private $wordpress_post_id;

	private $post_type;

	private $status;

	public function __construct(
		?int $id,
		string $url,
		string $normalized_url,
		?int $wordpress_post_id,
		?string $post_type,
		string $status
	) {
		$this->id                = $id;
		$this->url               = $url;
		$this->normalized_url    = $normalized_url;
		$this->wordpress_post_id = $wordpress_post_id;
		$this->post_type         = $post_type;
		$this->status             = $status;
	}

	public static function fromDomain( OwnerUrl $owner_url ): self {
		return new self(
			$owner_url->getId(),
			$owner_url->getUrl(),
			$owner_url->getNormalizedUrl(),
			$owner_url->getWordPressPostId(),
			$owner_url->getPostType(),
			$owner_url->getStatus()
		);
	}

	public function getId(): ?int { return $this->id; }
	public function getUrl(): string { return $this->url; }
	public function getNormalizedUrl(): string { return $this->normalized_url; }
	public function getWordPressPostId(): ?int { return $this->wordpress_post_id; }
	public function getPostType(): ?string { return $this->post_type; }
	public function getStatus(): string { return $this->status; }
}
