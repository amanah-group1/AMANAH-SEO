<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\Content\Content;
interface ContentRepositoryInterface {
 public function findById( int $id ): ?Content;
 public function findLatestByBlueprintId( int $blueprint_id ): ?Content;
 public function findAll( int $limit = 100 ): array;
 public function save( Content $content ): Content;
}
