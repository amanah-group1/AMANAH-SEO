<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

final class AIExecutionResult {
 private $status; private $response; private $error_category; private $attempts; private $provider;
 public function __construct( string $status, ?AIResponse $response = null, ?string $error_category = null, int $attempts = 0, ?string $provider = null ) { $this->status=$status; $this->response=$response; $this->error_category=$error_category; $this->attempts=$attempts; $this->provider=$provider; }
 public function getStatus(): string { return $this->status; } public function getResponse(): ?AIResponse { return $this->response; } public function getErrorCategory(): ?string { return $this->error_category; } public function getAttempts(): int { return $this->attempts; } public function getProvider(): ?string { return $this->provider; } public function isSuccessful(): bool { return 'completed' === $this->status; }
}
