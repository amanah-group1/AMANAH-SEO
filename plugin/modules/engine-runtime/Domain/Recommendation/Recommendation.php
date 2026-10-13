<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Domain\Recommendation;

defined( 'ABSPATH' ) || exit;

final class Recommendation {

	private $canonical_key;
	private $issue_id;
	private $priority;
	private $recommendation;
	private $target_url;
	private $status;
	private $execution_mode;
	private $id;
	private $accepted_at;
	private $completed_at;
	private $dismissed_at;
	private $verified_at;

	public function __construct(
		string $canonical_key,
		int $issue_id,
		string $priority,
		string $recommendation,
		string $target_url,
		string $status,
		string $execution_mode,
		?int $id = null,
		?string $accepted_at = null,
		?string $completed_at = null,
		?string $dismissed_at = null,
		?string $verified_at = null
	) {
		$this->canonical_key = $canonical_key;
		$this->issue_id = $issue_id;
		$this->priority = $priority;
		$this->recommendation = $recommendation;
		$this->target_url = $target_url;
		$this->status = $status;
		$this->execution_mode = $execution_mode;
		$this->id = $id;
		$this->accepted_at = $accepted_at;
		$this->completed_at = $completed_at;
		$this->dismissed_at = $dismissed_at;
		$this->verified_at = $verified_at;
	}

	public function getId(): ?int { return $this->id; }
	public function getCanonicalKey(): string { return $this->canonical_key; }
	public function getIssueId(): int { return $this->issue_id; }
	public function getPriority(): string { return $this->priority; }
	public function getRecommendation(): string { return $this->recommendation; }
	public function getTargetUrl(): string { return $this->target_url; }
	public function getStatus(): string { return $this->status; }
	public function getExecutionMode(): string { return $this->execution_mode; }
	public function getAcceptedAt(): ?string { return $this->accepted_at; }
	public function getCompletedAt(): ?string { return $this->completed_at; }
	public function getDismissedAt(): ?string { return $this->dismissed_at; }
	public function getVerifiedAt(): ?string { return $this->verified_at; }
}
