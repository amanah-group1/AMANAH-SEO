<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\Content\ContentRevision;

interface ContentRevisionRepositoryInterface {
	public function findLatestByContentId( int $content_id ): ?ContentRevision;
	public function save( ContentRevision $revision ): ContentRevision;
}
