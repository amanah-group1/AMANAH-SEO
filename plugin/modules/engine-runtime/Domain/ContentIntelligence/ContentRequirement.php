<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Domain\ContentIntelligence;
defined('ABSPATH') || exit;
final class ContentRequirement {
 private $id; private $keyword_id; private $finding_id; private $key; private $type; private $statement; private $priority; private $mandatory; private $rule; private $status; private $version;
 public function __construct(int $keyword_id,int $finding_id,string $key,string $type,string $statement,string $priority='MEDIUM',bool $mandatory=false,array $rule=[],string $status='DRAFT',int $version=1,?int $id=null){$this->keyword_id=$keyword_id;$this->finding_id=$finding_id;$this->key=$key;$this->type=$type;$this->statement=$statement;$this->priority=$priority;$this->mandatory=$mandatory;$this->rule=$rule;$this->status=$status;$this->version=$version;$this->id=$id;}
 public function getId():?int{return $this->id;} public function getKeywordId():int{return $this->keyword_id;} public function getFindingId():int{return $this->finding_id;} public function getKey():string{return $this->key;} public function getType():string{return $this->type;} public function getStatement():string{return $this->statement;} public function getPriority():string{return $this->priority;} public function isMandatory():bool{return $this->mandatory;} public function getVerificationRule():array{return $this->rule;} public function getStatus():string{return $this->status;} public function getVersion():int{return $this->version;}
}