<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\Repository;
use Amanah\SEOEngine\Application\Repository\ScanAnalysisRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentScanning\ScanAnalysis;
defined('ABSPATH') || exit;
final class WordPressScanAnalysisRepository extends WordPressRepository implements ScanAnalysisRepositoryInterface {
 public function __construct(\wpdb $wpdb){parent::__construct($wpdb,$wpdb->prefix.'amanah_seo_scan_analyses');}
 public function findByScanId(int $scan_id):?ScanAnalysis{$r=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE scan_id=%d ORDER BY version DESC,id DESC LIMIT 1",$scan_id),ARRAY_A);if(!is_array($r))return null;$d=json_decode((string)$r['analysis_data'],true);return new ScanAnalysis((int)$r['scan_id'],is_array($d)?$d:array(),(int)$r['version'],(string)$r['status'],(int)$r['id']);}
 public function save(ScanAnalysis $a):ScanAnalysis{$data=array('scan_id'=>$a->getScanId(),'analysis_data'=>wp_json_encode($a->getData()),'version'=>$a->getVersion(),'status'=>$a->getStatus(),'created_at'=>current_time('mysql',true),'updated_at'=>current_time('mysql',true));$f=array('%d','%s','%d','%s','%s','%s');$ok=$this->wpdb->insert($this->table,$data,$f);if(false===$ok)throw new \RuntimeException('Scan analysis could not be stored.');return new ScanAnalysis($a->getScanId(),$a->getData(),$a->getVersion(),$a->getStatus(),(int)$this->wpdb->insert_id);}
}