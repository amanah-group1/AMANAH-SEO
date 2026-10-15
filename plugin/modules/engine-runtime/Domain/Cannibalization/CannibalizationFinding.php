<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Cannibalization;
defined( 'ABSPATH' ) || exit;
final class CannibalizationFinding {
	private $id;private $keyword_id;private $owner_a;private $owner_b;private $type;private $severity;private $method;private $data;private $status;
	public function __construct(int $keyword_id,int $owner_a,int $owner_b,string $type,string $severity,string $method,array $data=array(),string $status='open',?int $id=null){if($keyword_id<1||$owner_a<1||$owner_b<1||$owner_a===$owner_b||''===$type||''===$severity||''===$method){throw new \InvalidArgumentException('Cannibalization finding is invalid.');}$this->id=$id;$this->keyword_id=$keyword_id;$this->owner_a=$owner_a;$this->owner_b=$owner_b;$this->type=$type;$this->severity=$severity;$this->method=$method;$this->data=$data;$this->status=$status;}
	public function getId():?int{return $this->id;}public function getKeywordId():int{return $this->keyword_id;}public function getOwnerUrlAId():int{return $this->owner_a;}public function getOwnerUrlBId():int{return $this->owner_b;}public function getConflictType():string{return $this->type;}public function getSeverity():string{return $this->severity;}public function getDetectionMethod():string{return $this->method;}public function getFindingData():array{return $this->data;}public function getStatus():string{return $this->status;}
}