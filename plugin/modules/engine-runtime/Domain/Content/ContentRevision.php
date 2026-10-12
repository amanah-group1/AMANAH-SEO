<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\Content;

defined( 'ABSPATH' ) || exit;

final class ContentRevision {
	private $id;
	private $content_id;
	private $wordpress_post_id;
	private $revision_number;
	private $source;
	private $title;
	private $body;
	private $excerpt;
	private $content_hash;
	private $wordpress_modified_at;
	private $created_at;

	public function __construct(
		int $content_id,
		int $wordpress_post_id,
		int $revision_number,
		string $source,
		string $title,
		string $body,
		string $excerpt,
		string $content_hash,
		string $wordpress_modified_at = '',
		?int $id = null,
		string $created_at = ''
	) {
		if ( $content_id < 1 || $wordpress_post_id < 1 || $revision_number < 1 || '' === trim( $body ) || '' === trim( $content_hash ) ) {
			throw new \InvalidArgumentException( 'Content revision data is incomplete.' );
		}

		$this->id = $id;
		$this->content_id = $content_id;
		$this->wordpress_post_id = $wordpress_post_id;
		$this->revision_number = $revision_number;
		$this->source = '' !== trim( $source ) ? $source : 'wordpress';
		$this->title = $title;
		$this->body = $body;
		$this->excerpt = $excerpt;
		$this->content_hash = $content_hash;
		$this->wordpress_modified_at = $wordpress_modified_at;
		$this->created_at = $created_at;
	}

	public function getId(): ?int { return $this->id; }
	public function getContentId(): int { return $this->content_id; }
	public function getWordPressPostId(): int { return $this->wordpress_post_id; }
	public function getRevisionNumber(): int { return $this->revision_number; }
	public function getSource(): string { return $this->source; }
	public function getTitle(): string { return $this->title; }
	public function getBody(): string { return $this->body; }
	public function getExcerpt(): string { return $this->excerpt; }
	public function getContentHash(): string { return $this->content_hash; }
	public function getWordPressModifiedAt(): string { return $this->wordpress_modified_at; }
	public function getCreatedAt(): string { return $this->created_at; }
}
