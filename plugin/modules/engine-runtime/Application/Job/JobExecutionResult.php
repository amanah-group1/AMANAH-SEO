<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
final class JobExecutionResult {
 private $data;
 public function __construct( array $data=array() ) { $this->data=$data; }
 public function all(): array { return $this->data; }
 public function isPartial(): bool { return true===(bool)($this->data['partial']??false); }
 public function getProcessed(): int { return (int)($this->data['processed']??0); }
 public function getTotal(): int { return (int)($this->data['total']??0); }
 public function getFailures(): array { $v=$this->data['failures']??array(); return is_array($v)?$v:array(); }
 public function isSuccessful(): bool { return !isset($this->data['success']) || true===$this->data['success']; }
}