<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Portability;

defined( 'ABSPATH' ) || exit;

interface PortabilityRepositoryInterface {

 public function export( array $scopes ): array;

 public function previewImport( array $document, array $resolutions = array() ): array;

 public function import( array $document, string $mode = 'merge', array $resolutions = array() ): array;
}
