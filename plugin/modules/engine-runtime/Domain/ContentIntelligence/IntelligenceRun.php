<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Domain\ContentIntelligence;
defined('ABSPATH') || exit;
final class IntelligenceRun {
 private $id; private $keyword_id; private $source_type; private $source_reference; private $status; private $version;
 public function __construct(int $keyword_id,string $source_type='FULL',?string $source_reference=null,string $status='RUNNING',int $version=1,?int $id=null){$this->keyword_id=$keyword_id;$this->source_type=$source_type;$this->source_reference=$source_reference;$this->status=$status;$this->version=$version;$this->id=$id;}
 public function getId():?int{return $this->id;} public function getKeywordId():int{return $this->keyword_id;} public function getSourceType():string{return $this->source_type;} public function getSourceReference():?string{return $this->source_reference;} public function getStatus():string{return $this->status;} public function getVersion():int{return $this->version;}
}