<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\Repository;
use Amanah\SEOEngine\Application\Repository\IntelligenceRunRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentIntelligence\IntelligenceRun;
defined('ABSPATH') || exit;
final class WordPressIntelligenceRunRepository extends WordPressRepository implements IntelligenceRunRepositoryInterface {
 public function __construct(\wpdb $wpdb){parent::__construct($wpdb,$wpdb->prefix.'amanah_seo_intelligence_runs');}
 public function findById(int $id):?IntelligenceRun{$r=$this->findRawById($id);return $this->hydrate($r);}
 public function findLatestByKeywordId(int $keyword_id):?IntelligenceRun{$r=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE keyword_id=%d ORDER BY version DESC,id DESC LIMIT 1",$keyword_id),ARRAY_A);return $this->hydrate($r);}
 public function save(IntelligenceRun $run):IntelligenceRun{$now=current_time('mysql',true);$data=array('keyword_id'=>$run->getKeywordId(),'source_type'=>$run->getSourceType(),'source_reference'=>$run->getSourceReference(),'status'=>$run->getStatus(),'version'=>$run->getVersion(),'started_at'=>$now,'updated_at'=>$now);$f=array('%d','%s','%s','%s','%d','%s','%s');if(null!==$run->getId()){ $data['completed_at']='COMPLETED'===$run->getStatus()? $now:null;$this->wpdb->update($this->table,$data,array('id'=>$run->getId()),$f+array('%s'),array('%d'));return $run;} $data['created_at']=$now;if(false===$this->wpdb->insert($this->table,$data,$f+array('%s')))throw new \RuntimeException('Intelligence run could not be stored.');return new IntelligenceRun($run->getKeywordId(),$run->getSourceType(),$run->getSourceReference(),$run->getStatus(),$run->getVersion(),(int)$this->wpdb->insert_id);}
 private function hydrate($r):?IntelligenceRun{if(!is_array($r))return null;return new IntelligenceRun((int)$r['keyword_id'],(string)$r['source_type'],null!==$r['source_reference']?(string)$r['source_reference']:null,(string)$r['status'],(int)$r['version'],(int)$r['id']);}
}