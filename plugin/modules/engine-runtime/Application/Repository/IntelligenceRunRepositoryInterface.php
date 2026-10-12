<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\ContentIntelligence\IntelligenceRun;
defined('ABSPATH') || exit;
interface IntelligenceRunRepositoryInterface { public function findById(int $id):?IntelligenceRun; public function findLatestByKeywordId(int $keyword_id):?IntelligenceRun; public function save(IntelligenceRun $run):IntelligenceRun; }