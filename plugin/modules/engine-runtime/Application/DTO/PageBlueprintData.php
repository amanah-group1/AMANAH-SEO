<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\DTO;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;
defined( 'ABSPATH' ) || exit;
final class PageBlueprintData { private $id; private $keyword_id; private $content_brief_id; private $data; private $version; private $status; private function __construct(PageBlueprint $b){$this->id=$b->getId();$this->keyword_id=$b->getKeywordId();$this->content_brief_id=$b->getContentBriefId();$this->data=$b->getData();$this->version=$b->getVersion();$this->status=$b->getStatus();} public static function fromDomain(PageBlueprint $b):self{return new self($b);} public function toArray():array{return array('id'=>$this->id,'keyword_id'=>$this->keyword_id,'content_brief_id'=>$this->content_brief_id,'data'=>$this->data,'version'=>$this->version,'status'=>$this->status);}}
