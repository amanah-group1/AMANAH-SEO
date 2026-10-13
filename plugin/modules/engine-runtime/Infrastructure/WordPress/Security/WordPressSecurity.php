<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\WordPress\Security;

defined( 'ABSPATH' ) || exit;

final class WordPressSecurity {

	public function canManage(): bool {
		return current_user_can( 'manage_options' );
	}

	public function verifyNonce( string $nonce, string $action ): bool {
		return false !== wp_verify_nonce( $nonce, $action );
	}

	public function requireManageCapability(): void {
		if ( ! $this->canManage() ) {
			wp_die( esc_html__( 'You are not authorized to perform this action.', 'amanah-seo-engine' ), 403 );
		}
	}
}
