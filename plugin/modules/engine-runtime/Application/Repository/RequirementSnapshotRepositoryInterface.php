<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\ContentIntelligence\RequirementSnapshot;
defined('ABSPATH') || exit;
interface RequirementSnapshotRepositoryInterface { public function findByRequirementVersion(int $requirement_id,int $version):?RequirementSnapshot; public function save(RequirementSnapshot $snapshot):RequirementSnapshot; }