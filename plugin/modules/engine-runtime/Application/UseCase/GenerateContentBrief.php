<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\UseCase;
use Amanah\SEOEngine\Application\DTO\ContentBriefData;
use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentPlanRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBriefService;
defined( 'ABSPATH' ) || exit;
final class GenerateContentBrief {
 private $plan_repository; private $strategy_repository; private $brief_repository; private $brief_service;
 public function __construct(ContentPlanRepositoryInterface $plan_repository,ContentStrategyRepositoryInterface $strategy_repository,ContentBriefRepositoryInterface $brief_repository,ContentBriefService $brief_service){$this->plan_repository=$plan_repository;$this->strategy_repository=$strategy_repository;$this->brief_repository=$brief_repository;$this->brief_service=$brief_service;}
 public function execute(int $keyword_id): ContentBriefData {
  $plan=$this->plan_repository->findByKeywordId($keyword_id); if(null===$plan) throw new \RuntimeException('Content Plan not found. Generate Content Plan before creating a Content Brief.');
  $strategy=$this->strategy_repository->findByKeywordId($keyword_id); if(null===$strategy) throw new \RuntimeException('SEO strategy not found. Generate SEO Strategy before creating a Content Brief.');
  if(null!==$plan->getStrategyId() && $plan->getStrategyId()!==$strategy->getId()) throw new \DomainException('Content Plan and SEO Strategy relationship is invalid.');
  $existing=$this->brief_repository->findByKeywordId($keyword_id); $version=null!==$existing?$existing->getVersion()+1:1;
  $data=$this->brief_service->buildData($plan,$strategy);
  $brief=new ContentBrief($keyword_id,$data,$version,'draft',$strategy->getId());
  $this->brief_service->validate($brief,$plan,$strategy);
  return ContentBriefData::fromDomain($this->brief_repository->save($brief));
 }
}
