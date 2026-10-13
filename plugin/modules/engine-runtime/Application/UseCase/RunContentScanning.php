<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\UseCase;
use Amanah\SEOEngine\Application\Repository\KeywordRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentScanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\SerpResultRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ScanAnalysisRepositoryInterface;
use Amanah\SEOEngine\Application\KeywordIntelligence\KeywordIntelligenceEngine;
use Amanah\SEOEngine\AI\AIExecutionService;
use Amanah\SEOEngine\AI\AIRequest;
use Amanah\SEOEngine\Infrastructure\AI\SecretStore;
use Amanah\SEOEngine\Infrastructure\Serp\SerpProviderInterface;
use Amanah\SEOEngine\Infrastructure\ContentScanning\ExternalPageFetcher;
use Amanah\SEOEngine\Infrastructure\ContentScanning\HtmlContentExtractor;
use Amanah\SEOEngine\Infrastructure\ContentScanning\ContentScanningAnalyzer;
use Amanah\SEOEngine\Domain\ContentScanning\ContentScan;
use Amanah\SEOEngine\Domain\ContentScanning\SerpResult;
use Amanah\SEOEngine\Domain\ContentScanning\ScanAnalysis;
use Amanah\SEOEngine\Application\Service\ContentIntelligenceService;
defined('ABSPATH') || exit;
final class RunContentScanning {
 private $keywords;private $strategies;private $scans;private $results;private $analyses;private $intelligence;private $serp;private $fetcher;private $extractor;private $analyzer;private $ai;private $secrets;private $content_intelligence;
 public function __construct(KeywordRepositoryInterface $keywords,ContentStrategyRepositoryInterface $strategies,ContentScanRepositoryInterface $scans,SerpResultRepositoryInterface $results,ScanAnalysisRepositoryInterface $analyses,KeywordIntelligenceEngine $intelligence,SerpProviderInterface $serp,ExternalPageFetcher $fetcher,HtmlContentExtractor $extractor,ContentScanningAnalyzer $analyzer,AIExecutionService $ai,SecretStore $secrets,ContentIntelligenceService $content_intelligence){$this->keywords=$keywords;$this->strategies=$strategies;$this->scans=$scans;$this->results=$results;$this->analyses=$analyses;$this->intelligence=$intelligence;$this->serp=$serp;$this->fetcher=$fetcher;$this->extractor=$extractor;$this->analyzer=$analyzer;$this->ai=$ai;$this->secrets=$secrets;$this->content_intelligence=$content_intelligence;}
 public function execute(int $keyword_id):ContentScan{
  $keyword=$this->keywords->findById($keyword_id);if(null===$keyword)throw new \RuntimeException('Keyword not found.');
  $strategy=$this->strategies->findByKeywordId($keyword_id);if(null===$strategy)throw new \RuntimeException('SEO Strategy not found. Generate Strategy first.');
  $phrase=(string)$keyword->getPhrase();$gemini_secret=$this->secrets->get('gemini');if(null===$gemini_secret||''===$gemini_secret)throw new \RuntimeException('Gemini credentials are not configured.');
  $existing=$this->scans->findLatestByKeywordId($keyword_id);$version=null!==$existing?$existing->getVersion()+1:1;
  $scan=$this->scans->save(new ContentScan($keyword_id,(int)$strategy->getId(),$phrase,array('phase'=>'scanning','started_at'=>current_time('mysql',true)),'running',$version));
  $scan_id=(int)$scan->getId();$this->results->deleteByScanId($scan_id);$intelligence_run=$this->content_intelligence->startFullRun($keyword_id,'content_scan:'.$scan_id);
  $serp=$this->serp->search($phrase,array('api_key'=>$gemini_secret));
  $pages=array();$analyzed=0;$failed=0;
  foreach($serp as $item){$f=$this->fetcher->fetch($item['url']);$page_data=array();if('analyzed'===$f['status']){$page_data=$this->extractor->extract($f['html']);++$analyzed;}else{++$failed;}
   $result=$this->results->save(new SerpResult($scan_id,(int)$item['position'],(int)$item['page_number'],$item['title'],$item['url'],$item['domain'],$item['snippet'],$item['result_type'],$f['status'],(int)$f['http_status'],(string)$f['error'],$page_data));if('analyzed'===$f['status'])$pages[]=array_merge(array('position'=>$result->getPosition(),'url'=>$result->getUrl(),'title'=>$result->getTitle()),$page_data);
  }
  $candidates=$this->intelligence->discover($phrase);$support_data=(array)($strategy->getData()['support_keywords']??array());$autocomplete=array();foreach($candidates as $c)$autocomplete[]=(string)$c['keyword'];
  $analysis=$this->analyzer->analyze($pages,$autocomplete,$support_data);
  $analysis=$this->enhanceWithGemini($phrase,$strategy->getData(),$analysis,$pages);
  $this->analyses->save(new ScanAnalysis($scan_id,$analysis,1,'review'));$this->content_intelligence->materialize($keyword_id,$intelligence_run,$analysis);$this->content_intelligence->completeRun($intelligence_run);
  $final=$this->scans->save(new ContentScan($keyword_id,(int)$strategy->getId(),$phrase,array('phase'=>'review','result_count'=>count($serp),'analyzed_count'=>$analyzed,'failed_count'=>$failed,'autocomplete'=>$autocomplete,'analysis'=>$analysis,'completed_at'=>current_time('mysql',true)),'review',$version+1,$scan_id));
  return $final;
 }
 private function enhanceWithGemini(string $phrase,array $strategy,array $analysis,array $pages):array{
  $secret=$this->secrets->get('gemini');if(null===$secret||''===$secret)return $analysis;
  $context=array('keyword'=>$phrase,'strategy'=>$strategy,'deterministic_analysis'=>$analysis,'competitor_pages'=>array_slice($pages,0,30));
  $request=new AIRequest('content_scanning_analysis','gemini-3.5-flash-lite','Analyze the supplied Google SERP competitor evidence for an editorial content scan. Do not invent facts. Search intent is context, never a heading. Potential Support Keywords are evidence and required coverage, but must not automatically become headings. Return JSON only with keys topics, questions, common_coverage, limited_coverage, opportunities, autocomplete_correlation, support_keyword_correlation. opportunities must identify meaningful under-covered topics grounded in the supplied pages.',array('scan'=>$context),true,array('topics','questions','common_coverage','limited_coverage','opportunities','autocomplete_correlation','support_keyword_correlation'));
  $result=$this->ai->executeTask($request,'gemini',array('api_key'=>$secret),array('text_generation'));if(!$result->isSuccessful()||null===$result->getResponse())return $analysis;
  $decoded=json_decode($result->getResponse()->getContent(),true);return is_array($decoded)?array_merge($analysis,$decoded):$analysis;
 }
}