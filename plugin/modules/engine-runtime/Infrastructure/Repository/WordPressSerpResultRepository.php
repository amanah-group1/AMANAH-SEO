<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\Repository;
use Amanah\SEOEngine\Application\Repository\SerpResultRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentScanning\SerpResult;
defined('ABSPATH') || exit;
final class WordPressSerpResultRepository extends WordPressRepository implements SerpResultRepositoryInterface {
 public function __construct(\wpdb $wpdb){parent::__construct($wpdb,$wpdb->prefix.'amanah_seo_serp_results');}
 public function deleteByScanId(int $scan_id):void{$this->wpdb->delete($this->table,array('scan_id'=>$scan_id),array('%d'));}
 public function save(SerpResult $r):SerpResult{$data=array('scan_id'=>$r->getScanId(),'position'=>$r->getPosition(),'page_number'=>$r->getPageNumber(),'title'=>$r->getTitle(),'url'=>$r->getUrl(),'domain'=>$r->getDomain(),'snippet'=>$r->getSnippet(),'result_type'=>$r->getResultType(),'fetch_status'=>$r->getFetchStatus(),'http_status'=>$r->getHttpStatus(),'error_message'=>$r->getErrorMessage(),'page_data'=>wp_json_encode($r->getPageData()),'created_at'=>current_time('mysql',true),'updated_at'=>current_time('mysql',true));$f=array('%d','%d','%d','%s','%s','%s','%s','%s','%s','%s','%d','%s','%s','%s','%s');$ok=$this->wpdb->insert($this->table,$data,$f);if(false===$ok)throw new \RuntimeException('SERP result could not be stored.');return new SerpResult($r->getScanId(),$r->getPosition(),$r->getPageNumber(),$r->getTitle(),$r->getUrl(),$r->getDomain(),$r->getSnippet(),$r->getResultType(),$r->getFetchStatus(),$r->getHttpStatus(),$r->getErrorMessage(),$r->getPageData(),(int)$this->wpdb->insert_id);}
 public function findByScanId(int $scan_id):array{$rows=$this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE scan_id=%d ORDER BY position ASC",$scan_id),ARRAY_A);$out=array();foreach($rows as $r){$d=json_decode((string)$r['page_data'],true);$out[]=new SerpResult((int)$r['scan_id'],(int)$r['position'],(int)$r['page_number'],(string)$r['title'],(string)$r['url'],(string)$r['domain'],(string)$r['snippet'],(string)$r['result_type'],(string)$r['fetch_status'],(int)$r['http_status'],(string)$r['error_message'],is_array($d)?$d:array(),(int)$r['id']);}return $out;}
}