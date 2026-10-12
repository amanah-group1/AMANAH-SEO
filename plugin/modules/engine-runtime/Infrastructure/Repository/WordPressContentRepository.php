<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Infrastructure\Repository;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Domain\Content\Content;
defined( 'ABSPATH' ) || exit;
final class WordPressContentRepository extends WordPressRepository implements ContentRepositoryInterface {
 public function __construct( \wpdb $wpdb ){ parent::__construct( $wpdb, $wpdb->prefix . 'amanah_seo_content' ); }
 public function save( Content $content ): Content {
  $data=array(
   'keyword_id'=>$content->getKeywordId(),'blueprint_id'=>$content->getBlueprintId(),'wordpress_post_id'=>$content->getWordPressPostId(),
   'title'=>$content->getTitle(),'content'=>$content->getBody(),'excerpt'=>$content->getExcerpt(),
   'metadata'=>wp_json_encode($content->getMetadata()),'status'=>$content->getStatus(),'version'=>$content->getVersion(),
   'created_at'=>current_time('mysql',true),'updated_at'=>current_time('mysql',true)
  );
  $formats=array('%d','%d','%d','%s','%s','%s','%s','%s','%d','%s','%s');
  if(null!==$content->getId()){
   $this->wpdb->update($this->table,$data,array('id'=>$content->getId()),$formats,array('%d'));
   return $content;
  }
  $inserted=$this->wpdb->insert($this->table,$data,$formats);
  if(false===$inserted) throw new \RuntimeException('Content could not be stored.');
  return new Content($content->getBody(),$content->getVersion(),$content->getStatus(),$content->getKeywordId(),$content->getBlueprintId(),(int)$this->wpdb->insert_id,$content->getTitle(),$content->getExcerpt(),$content->getMetadata(),$content->getWordPressPostId());
 }
 public function findById(int $id):?Content{return $this->hydrate($this->findRawById($id));}
 public function findLatestByBlueprintId(int $blueprint_id):?Content{
  $row=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE blueprint_id=%d ORDER BY id DESC LIMIT 1",$blueprint_id),ARRAY_A);
  return $this->hydrate(is_array($row)?$row:null);
 }
 public function findAll(int $limit=100):array{
  $limit=max(1,min(500,$limit));$rows=$this->wpdb->get_results("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT {$limit}",ARRAY_A);
  $items=array();foreach($rows as $row){$item=$this->hydrate(is_array($row)?$row:null);if(null!==$item)$items[]=$item;}return $items;
 }
 private function hydrate(?array $row):?Content{
  if(null===$row)return null;
  $metadata=json_decode((string)($row['metadata']??''),true);
  return new Content((string)$row['content'],(int)$row['version'],(string)$row['status'],null!==$row['keyword_id']?(int)$row['keyword_id']:null,null!==$row['blueprint_id']?(int)$row['blueprint_id']:null,(int)$row['id'],null!==$row['title']?(string)$row['title']:null,null!==$row['excerpt']?(string)$row['excerpt']:null,is_array($metadata)?$metadata:array(),null!==$row['wordpress_post_id']?(int)$row['wordpress_post_id']:null);
 }
}
