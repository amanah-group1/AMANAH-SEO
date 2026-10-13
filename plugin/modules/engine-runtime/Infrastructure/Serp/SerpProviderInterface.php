<?php
declare(strict_types=1);
namespace Amanah\SEOEngine\Infrastructure\Serp;
defined('ABSPATH') || exit;
interface SerpProviderInterface { public function search(string $keyword,array $options=array()):array; }