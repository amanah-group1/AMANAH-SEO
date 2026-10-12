<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\ContentScanning\ContentScan;
defined('ABSPATH') || exit;
interface ContentScanRepositoryInterface { public function findById(int $id):?ContentScan; public function findLatestByKeywordId(int $keyword_id):?ContentScan; public function save(ContentScan $scan):ContentScan; }