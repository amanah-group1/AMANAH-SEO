<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\PageBlueprint\PageBlueprint;
defined( 'ABSPATH' ) || exit;
interface PageBlueprintRepositoryInterface { public function findById( int $id ): ?PageBlueprint; public function findByKeywordId( int $keyword_id ): ?PageBlueprint; public function findAll( int $limit = 100 ): array; public function save( PageBlueprint $blueprint ): PageBlueprint; }
