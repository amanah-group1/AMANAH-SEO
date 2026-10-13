<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\UseCase;
use Amanah\SEOEngine\Application\Repository\ContentScanRepositoryInterface;
defined('ABSPATH') || exit;
final class SaveScanningReview {
 private $scans;
 public function __construct(ContentScanRepositoryInterface $scans){$this->scans=$scans;}
 public function execute(int $scan_id,array $review):void{
  $scan=$this->scans->findById($scan_id);if(null===$scan)throw new \RuntimeException('Content Scan not found.');
  $current=$scan->getData();foreach(array('topics','questions','common_coverage','limited_coverage','opportunities','autocomplete','analysis') as $key){if(array_key_exists($key,$review))$current[$key]=$review[$key];}
  $this->scans->save(new \Amanah\SEOEngine\Domain\ContentScanning\ContentScan($scan->getKeywordId(),$scan->getStrategyId(),$scan->getQuery(),$current,'review',$scan->getVersion()+1,$scan->getId()));
 }
}