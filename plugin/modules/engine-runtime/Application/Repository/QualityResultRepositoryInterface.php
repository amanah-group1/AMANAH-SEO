<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Repository;

use Amanah\SEOEngine\Domain\Quality\QualityResult;

interface QualityResultRepositoryInterface {

	public function save( QualityResult $result ): QualityResult;

	public function findById( int $id ): ?QualityResult;

	public function findLatestByContentId( int $content_id ): ?QualityResult;
}
