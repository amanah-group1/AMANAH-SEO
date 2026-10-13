<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\Keyword;
defined( 'ABSPATH' ) || exit;
final class Keyword {
	private $id; private $phrase; private $normalized_phrase; private $search_intent_id; private $keyword_type; private $article_type; private $pillar_keyword_id; private $cluster_id; private $owner_url_id; private $status;
	public function __construct(string $phrase,string $keyword_type='primary',string $status='draft',?int $id=null,?int $search_intent_id=null,?int $cluster_id=null,?int $owner_url_id=null,string $article_type='pillar',?int $pillar_keyword_id=null){
		$phrase=trim($phrase); if(''===$phrase){throw new \InvalidArgumentException('Keyword phrase cannot be empty.');}
		if(!in_array($keyword_type,array('primary','secondary','supporting'),true)){throw new \InvalidArgumentException('Invalid keyword type.');}
		if(!in_array($article_type,array('pillar','support'),true)){throw new \InvalidArgumentException('Invalid article type.');}
		if('pillar'===$article_type && null!==$pillar_keyword_id){throw new \InvalidArgumentException('Pillar keyword cannot have a parent pillar.');}
		if('support'===$article_type && null===$pillar_keyword_id){throw new \InvalidArgumentException('Support keyword requires a pillar keyword.');}
		$this->id=$id;$this->phrase=$phrase;$this->normalized_phrase=self::normalize($phrase);$this->keyword_type=$keyword_type;$this->article_type=$article_type;$this->pillar_keyword_id=$pillar_keyword_id;$this->status=$status;$this->search_intent_id=$search_intent_id;$this->cluster_id=$cluster_id;$this->owner_url_id=$owner_url_id;
	}
	public static function normalize(string $phrase):string{$phrase=trim((string)preg_replace('/\s+/u',' ',$phrase));return function_exists('mb_strtolower')?mb_strtolower($phrase,'UTF-8'):strtolower($phrase);}
	public function getId():?int{return $this->id;} public function getPhrase():string{return $this->phrase;} public function getNormalizedPhrase():string{return $this->normalized_phrase;} public function getSearchIntentId():?int{return $this->search_intent_id;} public function getKeywordType():string{return $this->keyword_type;} public function getArticleType():string{return $this->article_type;} public function getPillarKeywordId():?int{return $this->pillar_keyword_id;} public function getClusterId():?int{return $this->cluster_id;} public function getOwnerUrlId():?int{return $this->owner_url_id;} public function getStatus():string{return $this->status;}
	public function assignSearchIntent(int $id):void{$this->search_intent_id=$id;} public function assignCluster(?int $id):void{$this->cluster_id=$id;} public function assignOwnerUrl(?int $id):void{$this->owner_url_id=$id;} public function changeStatus(string $status):void{$this->status=$status;}
}