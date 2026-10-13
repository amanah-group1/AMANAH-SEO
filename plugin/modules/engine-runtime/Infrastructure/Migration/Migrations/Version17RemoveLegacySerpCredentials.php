<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility marker for the historical SERP credential migration.
 *
 * The standalone ENGINE implementation deleted the stored DataForSEO secret.
 * The single-plugin integration must preserve existing credentials: migration
 * execution is automatic and must not destroy user configuration implicitly.
 */
final class Version17RemoveLegacySerpCredentials implements MigrationInterface {

	public function getVersion(): int {
		return 17;
	}

	public function getName(): string {
		return 'remove_legacy_serp_credentials';
	}

	public function up(): void {
		// Intentionally preserve existing credentials during internalization.
		// Any credential cleanup requires a separate, explicit, backed-up action.
	}
}
