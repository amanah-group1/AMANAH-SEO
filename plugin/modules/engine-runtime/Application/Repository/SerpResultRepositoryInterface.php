<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\ContentScanning\SerpResult;
defined('ABSPATH') || exit;
interface SerpResultRepositoryInterface { public function deleteByScanId(int $scan_id):void; public function save(SerpResult $result):SerpResult; public function findByScanId(int $scan_id):array; }