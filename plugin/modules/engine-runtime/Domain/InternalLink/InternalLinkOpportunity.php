<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\InternalLink;
defined( 'ABSPATH' ) || exit;
final class InternalLinkOpportunity {
	private $id;private $source_url_id;private $target_url_id;private $anchor;private $relevance;private $confidence;private $method;private $status;
	public function __construct(int $source_url_id,int $target_url_id,string $method,string $status='suggested',?int $id=null,?string $anchor=null,?float $relevance=null,?float $confidence=null){if($source_url_id<1||$target_url_id<1||$source_url_id===$target_url_id||''===trim($method)){throw new \InvalidArgumentException('Internal link opportunity is invalid.');}$this->id=$id;$this->source_url_id=$source_url_id;$this->target_url_id=$target_url_id;$this->method=$method;$this->status=$status;$this->anchor=$anchor;$this->relevance=$relevance;$this->confidence=$confidence;}
	public function getId():?int{return $this->id;}public function getSourceUrlId():int{return $this->source_url_id;}public function getTargetUrlId():int{return $this->target_url_id;}public function getAnchorSuggestion():?string{return $this->anchor;}public function getRelevance():?float{return $this->relevance;}public function getConfidence():?float{return $this->confidence;}public function getDetectionMethod():string{return $this->method;}public function getStatus():string{return $this->status;}
}