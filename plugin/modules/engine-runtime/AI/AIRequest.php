<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\AI;

defined( 'ABSPATH' ) || exit;

final class AIRequest {

 private $task_type;
 private $model;
 private $prompt;
 private $context;
 private $structured;
 private $required_keys;

 public function __construct(
  string $task_type,
  string $model,
  string $prompt,
  array $context = array(),
  bool $structured = false,
  array $required_keys = array()
 ) {
  $this->task_type = $task_type;
  $this->model = $model;
  $this->prompt = $prompt;
  $this->context = $context;
  $this->structured = $structured;
  $this->required_keys = $required_keys;
 }

 public function getTaskType(): string { return $this->task_type; }
 public function getModel(): string { return $this->model; }
 public function getPrompt(): string { return $this->prompt; }
 public function getContext(): array { return $this->context; }
 public function isStructured(): bool { return $this->structured; }
 public function getRequiredKeys(): array { return $this->required_keys; }
}
