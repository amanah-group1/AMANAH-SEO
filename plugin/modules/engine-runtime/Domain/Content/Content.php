<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Content;
defined( 'ABSPATH' ) || exit;
final class Content {
	private $id;private $keyword_id;private $blueprint_id;private $wordpress_post_id;private $title;private $body;private $excerpt;private $metadata;private $status;private $version;
	public function __construct(string $body,int $version=1,string $status='draft',?int $keyword_id=null,?int $blueprint_id=null,?int $id=null,?string $title=null,?string $excerpt=null,array $metadata=array(),?int $wordpress_post_id=null){if(''===trim($body)||$version<1){throw new \InvalidArgumentException('Content body and version are required.');}$this->id=$id;$this->keyword_id=$keyword_id;$this->blueprint_id=$blueprint_id;$this->wordpress_post_id=$wordpress_post_id;$this->title=$title;$this->body=$body;$this->excerpt=$excerpt;$this->metadata=$metadata;$this->status=$status;$this->version=$version;}
	public function getId():?int{return $this->id;}public function getKeywordId():?int{return $this->keyword_id;}public function getBlueprintId():?int{return $this->blueprint_id;}public function getWordPressPostId():?int{return $this->wordpress_post_id;}public function getTitle():?string{return $this->title;}public function getBody():string{return $this->body;}public function getExcerpt():?string{return $this->excerpt;}public function getMetadata():array{return $this->metadata;}public function getStatus():string{return $this->status;}public function getVersion():int{return $this->version;}
	public function changeStatus(string $status):void{$this->status=$status;}public function publish():void{if(!in_array($this->status,array('validated','approved','published'),true)){throw new \DomainException('Content cannot be published before validation.');}$this->status='published';}
}