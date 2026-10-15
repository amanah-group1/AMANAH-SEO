<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\ContentBrief;
defined( 'ABSPATH' ) || exit;
final class ContentBrief {
	private $id;private $keyword_id;private $strategy_id;private $data;private $version;private $status;
	public function __construct(int $keyword_id,array $data,int $version=1,string $status='draft',?int $strategy_id=null,?int $id=null){if($keyword_id<1||$version<1){throw new \InvalidArgumentException('Content Brief identity is invalid.');}$this->id=$id;$this->keyword_id=$keyword_id;$this->strategy_id=$strategy_id;$this->data=$data;$this->version=$version;$this->status=$status;}
	public function getId():?int{return $this->id;}public function getKeywordId():int{return $this->keyword_id;}public function getStrategyId():?int{return $this->strategy_id;}public function getData():array{return $this->data;}public function getVersion():int{return $this->version;}public function getStatus():string{return $this->status;}
}