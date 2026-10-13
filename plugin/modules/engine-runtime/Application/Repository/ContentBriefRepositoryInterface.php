<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Application\Repository;
use Amanah\SEOEngine\Domain\ContentBrief\ContentBrief;
defined( 'ABSPATH' ) || exit;
interface ContentBriefRepositoryInterface {
 public function findById( int $id ): ?ContentBrief;
 public function findByKeywordId( int $keyword_id ): ?ContentBrief;
 public function findAll( int $limit = 100 ): array;
 public function save( ContentBrief $brief ): ContentBrief;
}
