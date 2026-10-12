<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Quality;
defined( 'ABSPATH' ) || exit;
final class QualityResult {
	private $id;private $content_id;private $content_version;private $deterministic;private $semantic;private $final;private $score;private $status;
	public function __construct(int $content_id,array $final,array $deterministic=array(),array $semantic=array(),?float $score=null,string $status='pending',?int $id=null,int $content_version=1){if($content_id<1||$content_version<1||!in_array($status,array('pending','passed','warning','failed'),true)){throw new \InvalidArgumentException('Invalid quality result.');}$this->id=$id;$this->content_id=$content_id;$this->content_version=$content_version;$this->deterministic=$deterministic;$this->semantic=$semantic;$this->final=$final;$this->score=$score;$this->status=$status;}
	public function getId():?int{return $this->id;}public function getContentId():int{return $this->content_id;}public function getContentVersion():int{return $this->content_version;}public function getDeterministic():array{return $this->deterministic;}public function getSemantic():array{return $this->semantic;}public function getFinal():array{return $this->final;}public function getScore():?float{return $this->score;}public function getStatus():string{return $this->status;}
}
