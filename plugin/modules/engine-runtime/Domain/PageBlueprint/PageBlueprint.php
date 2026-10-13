<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\PageBlueprint;
defined( 'ABSPATH' ) || exit;
final class PageBlueprint {
	private $id;private $keyword_id;private $content_brief_id;private $data;private $version;private $status;
	public function __construct(int $keyword_id,array $data,int $version=1,string $status='draft',?int $content_brief_id=null,?int $id=null){if($keyword_id<1||$version<1){throw new \InvalidArgumentException('Page Blueprint identity is invalid.');}$this->id=$id;$this->keyword_id=$keyword_id;$this->content_brief_id=$content_brief_id;$this->data=$data;$this->version=$version;$this->status=$status;}
	public function getId():?int{return $this->id;}public function getKeywordId():int{return $this->keyword_id;}public function getContentBriefId():?int{return $this->content_brief_id;}public function getData():array{return $this->data;}public function getVersion():int{return $this->version;}public function getStatus():string{return $this->status;}
}