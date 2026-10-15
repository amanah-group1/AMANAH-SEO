<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\DTO;
use Amanah\SEOEngine\Domain\Content\Content;
final class ContentData {
 public static function fromDomain(Content $content):self{$x=new self();$x->id=$content->getId();$x->keyword_id=$content->getKeywordId();$x->blueprint_id=$content->getBlueprintId();$x->title=$content->getTitle();$x->body=$content->getBody();$x->excerpt=$content->getExcerpt();$x->metadata=$content->getMetadata();$x->status=$content->getStatus();$x->version=$content->getVersion();return $x;}
 public $id;public $keyword_id;public $blueprint_id;public $title;public $body;public $excerpt;public $metadata;public $status;public $version;
}
