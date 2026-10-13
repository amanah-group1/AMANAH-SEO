<?php
/**
 * Internal ENGINE module bootstrap for the single AMANAH SEO plugin.
 */

defined( 'ABSPATH' ) || exit;

$engine_runtime_path = __DIR__ . '/';

require_once $engine_runtime_path . 'Core/Autoload/ClassLoader.php';

$engine_loader = new \Amanah\SEOEngine\Core\Autoload\ClassLoader(
	'Amanah\\SEOEngine\\',
	$engine_runtime_path
);
$engine_loader->register();

\Amanah\SEOEngine\Core\Bootstrap\Plugin::boot();
