<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

defined( 'ABSPATH' ) || exit;

interface RepositoryInterface {

	public function deleteById( int $id ): bool;
}