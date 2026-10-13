<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\ContentScanning\ScanAnalysis;
defined('ABSPATH') || exit;
interface ScanAnalysisRepositoryInterface { public function findByScanId(int $scan_id):?ScanAnalysis; public function save(ScanAnalysis $analysis):ScanAnalysis; }