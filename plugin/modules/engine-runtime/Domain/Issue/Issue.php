<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\Issue;

defined( 'ABSPATH' ) || exit;

final class Issue {

	private $canonical_key;
	private $source;
	private $issue_type;
	private $severity;
	private $status;
	private $url;
	private $message;
	private $evidence;
	private $id;
	private $first_seen_at;
	private $last_seen_at;
	private $resolved_at;
	private $dismissed_at;

	public function __construct(
		string $canonical_key,
		string $source,
		string $issue_type,
		string $severity,
		string $status,
		string $url,
		string $message,
		string $evidence,
		?int $id = null,
		?string $first_seen_at = null,
		?string $last_seen_at = null,
		?string $resolved_at = null,
		?string $dismissed_at = null
	) {
		$this->canonical_key = $canonical_key;
		$this->source = $source;
		$this->issue_type = $issue_type;
		$this->severity = $severity;
		$this->status = $status;
		$this->url = $url;
		$this->message = $message;
		$this->evidence = $evidence;
		$this->id = $id;
		$this->first_seen_at = $first_seen_at;
		$this->last_seen_at = $last_seen_at;
		$this->resolved_at = $resolved_at;
		$this->dismissed_at = $dismissed_at;
	}

	public function getId(): ?int { return $this->id; }
	public function getCanonicalKey(): string { return $this->canonical_key; }
	public function getSource(): string { return $this->source; }
	public function getIssueType(): string { return $this->issue_type; }
	public function getSeverity(): string { return $this->severity; }
	public function getStatus(): string { return $this->status; }
	public function getUrl(): string { return $this->url; }
	public function getMessage(): string { return $this->message; }
	public function getEvidence(): string { return $this->evidence; }
	public function getFirstSeenAt(): ?string { return $this->first_seen_at; }
	public function getLastSeenAt(): ?string { return $this->last_seen_at; }
	public function getResolvedAt(): ?string { return $this->resolved_at; }
	public function getDismissedAt(): ?string { return $this->dismissed_at; }
}
