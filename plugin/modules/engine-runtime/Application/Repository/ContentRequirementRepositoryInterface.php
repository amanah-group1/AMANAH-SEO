<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\ContentIntelligence\ContentRequirement;
defined('ABSPATH') || exit;
interface ContentRequirementRepositoryInterface { public function findByKey(string $key):?ContentRequirement; public function findByKeywordId(int $keyword_id,string $status=''):array; public function save(ContentRequirement $requirement):ContentRequirement; }