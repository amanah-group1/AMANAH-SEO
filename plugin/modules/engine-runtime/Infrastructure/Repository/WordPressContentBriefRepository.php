<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\Repository;
use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
defined( 'ABSPATH' ) || exit;
final class WordPressContentBriefRepository extends WordPressRepository implements ContentBriefRepositoryInterface {
 public function __construct( \wpdb $wpdb ){parent::__construct($wpdb,$wpdb->prefix.'amanah_seo_content_briefs');}
 public function save(ContentBrief $brief): ContentBrief {
  $data=array('keyword_id'=>$brief->getKeywordId(),'strategy_id'=>$brief->getStrategyId(),'brief_data'=>wp_json_encode($brief->getData()),'version'=>$brief->getVersion(),'status'=>$brief->getStatus(),'created_at'=>current_time('mysql',true),'updated_at'=>current_time('mysql',true));
  $formats=array('%d','%d','%s','%d','%s','%s','%s');
  if(null!==$brief->getId()){ $updated=$this->wpdb->update($this->table,$data,array('id'=>$brief->getId()),$formats,array('%d')); if(false===$updated) throw new \RuntimeException('Content Brief could not be stored.'); return $brief; }
  $inserted=$this->wpdb->insert($this->table,$data,$formats); if(false===$inserted) throw new \RuntimeException('Content Brief could not be stored.');
  return new ContentBrief($brief->getKeywordId(),$brief->getData(),$brief->getVersion(),$brief->getStatus(),$brief->getStrategyId(),(int)$this->wpdb->insert_id);
 }
 public function findById(int $id):?ContentBrief { $row=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d LIMIT 1",$id),ARRAY_A); return $this->hydrate(is_array($row)?$row:null); }
 public function findByKeywordId(int $keyword_id):?ContentBrief { $row=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE keyword_id = %d ORDER BY version DESC, id DESC LIMIT 1",$keyword_id),ARRAY_A); return $this->hydrate(is_array($row)?$row:null); }
 public function findAll(int $limit=100):array { $limit=max(1,min(100,$limit)); $rows=$this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d",$limit),ARRAY_A); $items=array(); foreach($rows as $row){$brief=$this->hydrate(is_array($row)?$row:null);if(null!==$brief)$items[]=$brief;} return $items; }
 private function hydrate(?array $row):?ContentBrief { if(null===$row)return null; $data=json_decode((string)$row['brief_data'],true); return new ContentBrief((int)$row['keyword_id'],is_array($data)?$data:array(),(int)$row['version'],(string)$row['status'],isset($row['strategy_id'])?(int)$row['strategy_id']:null,(int)$row['id']);}
}
