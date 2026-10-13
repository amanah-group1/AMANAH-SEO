<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\UseCase;
use Amanah\SEOEngine\Application\Repository\ContentScanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\AI\AIExecutionService;
use Amanah\SEOEngine\AI\AIRequest;
use Amanah\SEOEngine\Infrastructure\AI\SecretStore;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
defined('ABSPATH') || exit;
final class GenerateOutlineFromScanning {
 private $scans;private $strategies;private $briefs;private $ai;private $secrets;
 public function __construct(ContentScanRepositoryInterface $scans,ContentStrategyRepositoryInterface $strategies,ContentBriefRepositoryInterface $briefs,AIExecutionService $ai,SecretStore $secrets){$this->scans=$scans;$this->strategies=$strategies;$this->briefs=$briefs;$this->ai=$ai;$this->secrets=$secrets;}
 public function execute(int $keyword_id):ContentBrief{
  $scan=$this->scans->findLatestByKeywordId($keyword_id);if(null===$scan||'review'!==$scan->getStatus())throw new \RuntimeException('Complete and review Content Scanning before creating the outline.');
  $strategy=$this->strategies->findByKeywordId($keyword_id);if(null===$strategy)throw new \RuntimeException('SEO Strategy not found.');
  $secret=$this->secrets->get('gemini');if(null===$secret||''===$secret)throw new \RuntimeException('Gemini credentials are not configured.');
  $data=$scan->getData();$request=new AIRequest('article_outline_from_scan','gemini-3.5-flash-lite','Create an editable article outline from the supplied scanning evidence. Use real SERP topics, common coverage, limited coverage, opportunities, autocomplete evidence, support keyword correlation, and strategy context. Search intent is context only and must never be a heading. Potential Support Keywords must be covered in the article but are not automatically headings. Return JSON only with article_outline and support_keyword_mapping. article_outline items require id, level (1/2/3), type, title. Keep exactly one H1. support_keyword_mapping items require keyword_id, phrase, section_id, required=true.',array('scan'=>$data,'strategy'=>$strategy->getData()),true,array('article_outline','support_keyword_mapping'));
  $result=$this->ai->executeTask($request,'gemini',array('api_key'=>$secret),array('text_generation'));if(!$result->isSuccessful()||null===$result->getResponse())throw new \RuntimeException('Outline generation failed: '.($result->getErrorCategory()?:'ai_unavailable'));
  $decoded=json_decode($result->getResponse()->getContent(),true);if(!is_array($decoded)||empty($decoded['article_outline']))throw new \RuntimeException('Generated outline response is invalid.');
  $existing=$this->briefs->findByKeywordId($keyword_id);$version=null!==$existing?$existing->getVersion()+1:1;$strategy_data=$strategy->getData();$plan_data=array();
  $brief_data=array('brief_version'=>2,'source'=>'content_scanning','keyword'=>$strategy_data['keyword']??array(),'support_keywords'=>$strategy_data['support_keywords']??array(),'target_url'=>$strategy_data['owner_url']['url']??'','content_requirement'=>'new_content','content_role'=>$strategy_data['content_role']??'supporting','article_type'=>$strategy_data['article_type']??'supporting','search_intent'=>$strategy_data['search_intent']??null,'intent_coverage'=>$strategy_data['intent_coverage']??array(),'cluster'=>$strategy_data['cluster']??null,'content_objective'=>'Build content from reviewed SERP and competitor evidence.','content_direction'=>'Use the approved scanning evidence as the governing research source.','strategy_id'=>$strategy->getId(),'content_plan_id'=>null,'issues'=>$strategy_data['issues']??array(),'scanning_id'=>$scan->getId(),'article_outline'=>$decoded['article_outline'],'support_keyword_mapping'=>$decoded['support_keyword_mapping']??array(),'outline_status'=>'draft');
  $brief=new ContentBrief($keyword_id,$brief_data,$version,'draft',$strategy->getId());return $this->briefs->save($brief);
 }
}