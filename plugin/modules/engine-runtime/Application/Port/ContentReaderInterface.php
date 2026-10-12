<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Port;

interface ContentReaderInterface {
	public function getPost( int $post_id ): ?array;
}
