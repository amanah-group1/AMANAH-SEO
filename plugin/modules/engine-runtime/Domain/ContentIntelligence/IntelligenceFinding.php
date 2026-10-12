<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Domain\ContentIntelligence;
defined('ABSPATH') || exit;
final class IntelligenceFinding {
 private $id; private $run_id; private $key; private $type; private $subject; private $description; private $evidence; private $source; private $materiality; private $confidence; private $status;
 public function __construct(int $run_id,string $key,string $type,string $subject,string $description,array $evidence=[],string $source='content_scan',string $materiality='MEDIUM',string $confidence='MEDIUM',string $status='ACTIVE',?int $id=null){$this->run_id=$run_id;$this->key=$key;$this->type=$type;$this->subject=$subject;$this->description=$description;$this->evidence=$evidence;$this->source=$source;$this->materiality=$materiality;$this->confidence=$confidence;$this->status=$status;$this->id=$id;}
 public function getId():?int{return $this->id;} public function getRunId():int{return $this->run_id;} public function getKey():string{return $this->key;} public function getType():string{return $this->type;} public function getSubject():string{return $this->subject;} public function getDescription():string{return $this->description;} public function getEvidence():array{return $this->evidence;} public function getSource():string{return $this->source;} public function getMateriality():string{return $this->materiality;} public function getConfidence():string{return $this->confidence;} public function getStatus():string{return $this->status;}
}