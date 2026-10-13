<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Job;
defined( 'ABSPATH' ) || exit;
final class ConcurrencyPolicy {
 private $global; private $byType; private $byProvider;
 public function __construct( int $global=2, array $byType=array(), array $byProvider=array() ) { $this->global=max(1,$global); $this->byType=$byType; $this->byProvider=$byProvider; }
 public function canStart( int $activeGlobal, int $activeType, int $activeProvider, string $type, string $provider='' ): bool {
  $typeLimit=isset($this->byType[$type])?(int)$this->byType[$type]:$this->global;
  $providerLimit=''!==$provider&&isset($this->byProvider[$provider])?(int)$this->byProvider[$provider]:$this->global;
  return $activeGlobal<$this->global && $activeType<$typeLimit && $activeProvider<$providerLimit;
 }
}