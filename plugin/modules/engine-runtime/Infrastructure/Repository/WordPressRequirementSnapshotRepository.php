<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\Repository;
use Amanah\SEOEngine\Application\Repository\RequirementSnapshotRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot;
defined('ABSPATH') || exit;
final class WordPressRequirementSnapshotRepository extends WordPressRepository implements RequirementSnapshotRepositoryInterface {
 public function __construct(\wpdb $wpdb){parent::__construct($wpdb,$wpdb->prefix.'amanah_seo_requirement_snapshots');}
 public function findByRequirementVersion(int $requirement_id,int $version):?RequirementSnapshot{$r=$this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->table} WHERE requirement_id=%d AND version=%d LIMIT 1",$requirement_id,$version),ARRAY_A);return $this->hydrate($r);}
 public function save(RequirementSnapshot $s):RequirementSnapshot{$data=array('requirement_id'=>$s->getRequirementId(),'version'=>$s->getVersion(),'snapshot_data'=>wp_json_encode($s->getData()),'created_at'=>current_time('mysql',true));if(false===$this->wpdb->insert($this->table,$data,array('%d','%d','%s','%s')))throw new \RuntimeException('Requirement snapshot could not be stored.');return new RequirementSnapshot($s->getRequirementId(),$s->getVersion(),$s->getData(),(int)$this->wpdb->insert_id);}
 private function hydrate($r):?RequirementSnapshot{if(!is_array($r))return null;$d=json_decode((string)$r['snapshot_data'],true);return new RequirementSnapshot((int)$r['requirement_id'],(int)$r['version'],is_array($d)?$d:array(),(int)$r['id']);}
}