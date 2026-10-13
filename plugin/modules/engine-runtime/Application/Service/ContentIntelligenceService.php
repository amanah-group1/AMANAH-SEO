<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\Service;
use Amanah\SEOEngine\Application\Repository\IntelligenceRunRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\IntelligenceFindingRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRequirementRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\RequirementSnapshotRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentIntelligence\IntelligenceRun;
use Amanah\SEOEngine\Domain\ContentIntelligence\IntelligenceFinding;
use Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement;
use Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot;
defined('ABSPATH') || exit;
final class ContentIntelligenceService {
 private $runs;private $findings;private $requirements;private $snapshots;
 public function __construct(IntelligenceRunRepositoryInterface $runs,IntelligenceFindingRepositoryInterface $findings,ContentRequirementRepositoryInterface $requirements,RequirementSnapshotRepositoryInterface $snapshots){$this->runs=$runs;$this->findings=$findings;$this->requirements=$requirements;$this->snapshots=$snapshots;}
 public function startFullRun(int $keyword_id,?string $reference=null):IntelligenceRun{$latest=$this->runs->findLatestByKeywordId($keyword_id);$version=null!==$latest?$latest->getVersion()+1:1;return $this->runs->save(new IntelligenceRun($keyword_id,'FULL',$reference,'RUNNING',$version));}
 public function completeRun(IntelligenceRun $run):IntelligenceRun{return $this->runs->save(new IntelligenceRun($run->getKeywordId(),$run->getSourceType(),$run->getSourceReference(),'COMPLETED',$run->getVersion(),$run->getId()));}
 public function getActiveRequirements(int $keyword_id):array{return $this->requirements->findByKeywordId($keyword_id,'ACTIVE');}
 public function getSnapshotId(int $requirement_id,int $version):?int{$s=$this->snapshots->findByRequirementVersion($requirement_id,$version);return null!==$s?$s->getId():null;}
 public function materialize(int $keyword_id,IntelligenceRun $run,array $analysis):array{$out=array();$items=array();
  foreach((array)($analysis['topics']??array()) as $x)if(is_array($x)&&''!==trim((string)($x['topic']??'')))$items[]=array('type'=>'TOPIC','subject'=>(string)$x['topic'],'description'=>'Topic identified from competitor coverage.','materiality'=>((int)($x['coverage']??0)>1?'HIGH':'MEDIUM'),'confidence'=>'MEDIUM','raw'=>$x);
  foreach((array)($analysis['questions']??array()) as $x)if(is_array($x)&&''!==trim((string)($x['question']??'')))$items[]=array('type'=>'QUESTION','subject'=>(string)$x['question'],'description'=>'Question identified from competitor content.','materiality'=>'MEDIUM','confidence'=>'MEDIUM','raw'=>$x);
  foreach((array)($analysis['opportunities']??array()) as $x){$s=is_array($x)?(string)($x['topic']??$x['subject']??$x['opportunity']??''):(string)$x;if(''!==trim($s))$items[]=array('type'=>'OPPORTUNITY','subject'=>$s,'description'=>'Coverage opportunity identified by content intelligence.','materiality'=>'HIGH','confidence'=>'MEDIUM','raw'=>$x);}
  foreach((array)($analysis['support_keyword_correlation']??array()) as $x)if(is_array($x)&&''!==trim((string)($x['phrase']??'')))$items[]=array('type'=>'SUPPORT_KEYWORD','subject'=>(string)$x['phrase'],'description'=>'Potential support keyword requiring natural coverage.','materiality'=>'HIGH','confidence'=>'HIGH','raw'=>$x);
  foreach($items as $item){$subject=trim($item['subject']);$key='run:'.$run->getId().'|'.$item['type'].'|'.substr(hash('sha256',mb_strtolower($subject,'UTF-8')),0,32);$raw=$item['raw'];$e=array('source_type'=>'CONTENT_SCAN','source_reference'=>$run->getSourceReference(),'excerpt'=>(string)($raw['excerpt']??''),'url'=>(string)($raw['url']??''),'position'=>(int)($raw['position']??0),'metadata'=>array('analysis'=>$raw));$finding=$this->findings->save(new IntelligenceFinding($run->getId(),$key,$item['type'],$subject,$item['description'],$e,'content_scan',$item['materiality'],$item['confidence'],'ACTIVE'));$out[]=$finding;$rkey='keyword:'.$keyword_id.'|finding:'.hash('sha256',$item['type'].'|'.mb_strtolower($subject,'UTF-8'));$old=$this->requirements->findByKey($rkey);$version=null!==$old?$old->getVersion()+1:1;$mandatory='SUPPORT_KEYWORD'===$item['type']||'TOPIC'===$item['type'];$rule='SUPPORT_KEYWORD'===$item['type']?array('method'=>'phrase_presence','target'=>$subject,'minimum'=>1):array('method'=>'semantic_presence','target'=>$subject,'minimum'=>1);$req=$this->requirements->save(new ContentRequirement($keyword_id,(int)$finding->getId(),$rkey,$this->mapType($item['type']),$this->statement($item['type'],$subject),$mandatory?'HIGH':'MEDIUM',$mandatory,$rule,'ACTIVE',$version,null!==$old?$old->getId():null));if(null===$this->snapshots->findByRequirementVersion((int)$req->getId(),$version))$this->snapshots->save(new RequirementSnapshot((int)$req->getId(),$version,array('requirement_id'=>$req->getId(),'version'=>$version,'keyword_id'=>$keyword_id,'finding_id'=>$finding->getId(),'type'=>$req->getType(),'statement'=>$req->getStatement(),'priority'=>$req->getPriority(),'mandatory'=>$req->isMandatory(),'verification_rule'=>$req->getVerificationRule())));}
  return $out;
 }
 private function mapType(string $t):string{return array('TOPIC'=>'TOPIC','QUESTION'=>'QUESTION','SUPPORT_KEYWORD'=>'SUPPORT_KEYWORD','OPPORTUNITY'=>'CONTENT_GAP')[$t]??'OTHER';}
 private function statement(string $t,string $s):string{return 'SUPPORT_KEYWORD'===$t?'Cover the support keyword naturally in the article body: '.$s:('QUESTION'===$t?'Answer the important question clearly and directly: '.$s:'Cover the identified '.$this->mapType($t).' in a useful and relevant way: '.$s);}
}