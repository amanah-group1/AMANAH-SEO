<?php
/**
 * Registers AI settings inside the AMANAH SEO host menu.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_AI_Admin_Menu {
	private $controller;

	public function __construct( $controller ) {
		$this->controller = $controller;
	}

	public function register() {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 99 );
	}

	public function register_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_submenu_page(
			'amanah-seo',
			__( 'AI — Settings', 'amanah-seo-ai' ),
			__( 'AI — Settings', 'amanah-seo-ai' ),
			'manage_options',
			'amanah-seo-ai-settings',
			array( $this->controller, 'render' )
		);
	}
}
