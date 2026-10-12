<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\Repository;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Domain\Keyword\Keyword;
defined( 'ABSPATH' ) || exit;
final class WordPressKeywordRepository extends WordPressRepository implements KeywordRepositoryInterface {
	public function __construct(\wpdb $wpdb){parent::__construct($wpdb,$wpdb->prefix.'amanah_seo_keywords');}
	public function save(Keyword $keyword):Keyword{
		$data=array('keyword'=>$keyword->getPhrase(),'normalized_keyword'=>$keyword->getNormalizedPhrase(),'search_intent_id'=>$keyword->getSearchIntentId(),'keyword_type'=>$keyword->getKeywordType(),'article_type'=>$keyword->getArticleType(),'pillar_keyword_id'=>$keyword->getPillarKeywordId(),'cluster_id'=>$keyword->getClusterId(),'owner_url_id'=>$keyword->getOwnerUrlId(),'status'=>$keyword->getStatus(),'created_at'=>current_time('mysql',true),'updated_at'=>current_time('mysql',true));
		$formats=array('%s','%s','%d','%s','%s','%d','%d','%d','%s','%s','%s');
		if(null!==$keyword->getId()){ $updated=$this->wpdb->update($this->table,$data,array('id'=>$keyword->getId()),$formats,array('%d')); if(false===$updated)throw new \RuntimeException('Keyword could not be stored.'); return $keyword; }
		$inserted=$this->wpdb->insert($this->table,$data,$formats); if(false===$inserted)throw new \RuntimeException('Keyword could not be stored.');
		return new Keyword($keyword->getPhrase(),$keyword->getKeywordType(),$keyword->getStatus(),(int)$this->wpdb->insert_id,$keyword->getSearchIntentId(),$keyword->getClusterId(),$keyword->getOwnerUrlId(),$keyword->getArticleType(),$keyword->getPillarKeywordId());
	}
	public function findById(int $id):?Keyword{return $this->hydrate($this->findRawById($id));}
	public function findAll(int $limit=100):array{$limit=max(1,min(100,$limit));$rows=$this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d",$limit),ARRAY_A);$keywords=array();foreach($rows as $row){$keyword=$this->hydrate(is_array($row)?$row:null);if(null!==$keyword)$keywords[]=$keyword;}return $keywords;}
	private function hydrate(?array $row):?Keyword{if(null===$row)return null;return new Keyword((string)$row['keyword'],(string)$row['keyword_type'],(string)$row['status'],(int)$row['id'],null!==$row['search_intent_id']?(int)$row['search_intent_id']:null,null!==$row['cluster_id']?(int)$row['cluster_id']:null,null!==$row['owner_url_id']?(int)$row['owner_url_id']:null,isset($row['article_type'])?(string)$row['article_type']:'pillar',isset($row['pillar_keyword_id'])?(int)$row['pillar_keyword_id']:null);}
}