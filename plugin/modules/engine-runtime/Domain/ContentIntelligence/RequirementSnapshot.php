<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Domain\ContentIntelligence;
defined('ABSPATH') || exit;
final class RequirementSnapshot {
 private $id; private $requirement_id; private $version; private $data;
 public function __construct(int $requirement_id,int $version,array $data=[],?int $id=null){$this->requirement_id=$requirement_id;$this->version=$version;$this->data=$data;$this->id=$id;}
 public function getId():?int{return $this->id;} public function getRequirementId():int{return $this->requirement_id;} public function getVersion():int{return $this->version;} public function getData():array{return $this->data;}
}