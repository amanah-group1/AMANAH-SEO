<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\DTO;
defined( 'ABSPATH' ) || exit;
final class KeywordData {
	private $id; private $phrase; private $normalized_phrase; private $keyword_type; private $article_type; private $pillar_keyword_id; private $status; private $search_intent_id; private $cluster_id; private $owner_url_id;
	public function __construct(?int $id,string $phrase,string $normalized_phrase,string $keyword_type,string $article_type,?int $pillar_keyword_id,string $status,?int $search_intent_id,?int $cluster_id,?int $owner_url_id){$this->id=$id;$this->phrase=$phrase;$this->normalized_phrase=$normalized_phrase;$this->keyword_type=$keyword_type;$this->article_type=$article_type;$this->pillar_keyword_id=$pillar_keyword_id;$this->status=$status;$this->search_intent_id=$search_intent_id;$this->cluster_id=$cluster_id;$this->owner_url_id=$owner_url_id;}
	public static function fromDomain(\Amanah\SEOEngine\Domain\Keyword\Keyword $keyword):self{return new self($keyword->getId(),$keyword->getPhrase(),$keyword->getNormalizedPhrase(),$keyword->getKeywordType(),$keyword->getArticleType(),$keyword->getPillarKeywordId(),$keyword->getStatus(),$keyword->getSearchIntentId(),$keyword->getClusterId(),$keyword->getOwnerUrlId());}
	public function toArray():array{return array('id'=>$this->id,'phrase'=>$this->phrase,'normalized_phrase'=>$this->normalized_phrase,'keyword_type'=>$this->keyword_type,'article_type'=>$this->article_type,'pillar_keyword_id'=>$this->pillar_keyword_id,'status'=>$this->status,'search_intent_id'=>$this->search_intent_id,'cluster_id'=>$this->cluster_id,'owner_url_id'=>$this->owner_url_id);}
}