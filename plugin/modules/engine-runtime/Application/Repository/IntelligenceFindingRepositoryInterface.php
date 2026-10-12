<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\ContentIntelligence\IntelligenceFinding;
defined('ABSPATH') || exit;
interface IntelligenceFindingRepositoryInterface { public function findByKey(string $key):?IntelligenceFinding; public function findByRunId(int $run_id):array; public function save(IntelligenceFinding $finding):IntelligenceFinding; }