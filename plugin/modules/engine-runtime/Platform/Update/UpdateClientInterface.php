<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Platform\Update;

defined( 'ABSPATH' ) || exit;

interface UpdateClientInterface {

	public function is_update_available(): bool;

	public function getMetadata(): ?UpdateMetadata;

	public function evaluate( bool $entitled ): array;

	public function verifyPackage( string $package_path ): bool;
}
