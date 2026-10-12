<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\Repository;
use Amanah\SEOEngine\Application\Repository\ContentScanRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentScanning\ContentScan;
defined('ABSPATH') || exit;
final class WordPressContentScanRepository extends WordPressRepository implements ContentScanRepositoryInterface {
 public function __construct(\wpdb $wpdb){parent::__construct($wpdb,$wpdb->prefix.'amanah_seo_content_scans');}
 public function findById(int $id):?ContentScan{$r=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE id=%d LIMIT 1",$id),ARRAY_A);return $this->hydrate($r);}
 public function findLatestByKeywordId(int $keyword_id):?ContentScan{$r=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE keyword_id=%d ORDER BY version DESC,id DESC LIMIT 1",$keyword_id),ARRAY_A);return $this->hydrate($r);}
 public function save(ContentScan $s):ContentScan{$now=current_time('mysql',true);$data=array('keyword_id'=>$s->getKeywordId(),'strategy_id'=>$s->getStrategyId(),'query'=>$s->getQuery(),'status'=>$s->getStatus(),'scan_data'=>wp_json_encode($s->getData()),'version'=>$s->getVersion(),'updated_at'=>$now);$formats=array('%d','%d','%s','%s','%s','%d','%s');if(null!==$s->getId()){$ok=$this->wpdb->update($this->table,$data,array('id'=>$s->getId()),$formats,array('%d'));if(false===$ok)throw new \RuntimeException('Content Scan could not be stored.');return $s;}$data['created_at']=$now;$formats[]='%s';$ok=$this->wpdb->insert($this->table,$data,$formats);if(false===$ok)throw new \RuntimeException('Content Scan could not be stored.');return new ContentScan($s->getKeywordId(),$s->getStrategyId(),$s->getQuery(),$s->getData(),$s->getStatus(),$s->getVersion(),(int)$this->wpdb->insert_id);}
 private function hydrate($r):?ContentScan{if(!is_array($r))return null;$d=json_decode((string)$r['scan_data'],true);return new ContentScan((int)$r['keyword_id'],(int)$r['strategy_id'],(string)$r['query'],is_array($d)?$d:array(),(string)$r['status'],(int)$r['version'],(int)$r['id']);}
}