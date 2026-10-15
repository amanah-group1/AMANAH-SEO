<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\DTO;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
defined( 'ABSPATH' ) || exit;
final class ContentBriefData {
 private $id; private $keyword_id; private $strategy_id; private $data; private $version; private $status;
 private function __construct( ContentBrief $brief ) {$this->id=$brief->getId();$this->keyword_id=$brief->getKeywordId();$this->strategy_id=$brief->getStrategyId();$this->data=$brief->getData();$this->version=$brief->getVersion();$this->status=$brief->getStatus();}
 public static function fromDomain( ContentBrief $brief ): self {return new self($brief);}
 public function toArray(): array {return array('id'=>$this->id,'keyword_id'=>$this->keyword_id,'strategy_id'=>$this->strategy_id,'data'=>$this->data,'version'=>$this->version,'status'=>$this->status);}
}
