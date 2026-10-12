<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Application\Command\CreateSearchIntentCommand;
use Amanah\SEOEngine\Application\UseCase\CreateSearchIntent;
use Amanah\SEOEngine\Application\UseCase\ListSearchIntents;

defined( 'ABSPATH' ) || exit;

final class SearchIntentAdmin {
	private $create_intent;
	private $list_intents;

	public function __construct( CreateSearchIntent $create_intent, ListSearchIntents $list_intents ) {
		$this->create_intent = $create_intent;
		$this->list_intents = $list_intents;
	}

	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to manage search intents.', 'amanah-seo-engine' ) );
		}
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['amanah_search_intent_action'] ) ) {
			$this->handlePost();
		}

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'SEO Strategy', 'amanah-seo-engine' ) . '</h1>';
		settings_errors( 'amanah_search_intents' );
		echo '<h2>' . esc_html__( 'Search Intents', 'amanah-seo-engine' ) . '</h2>';
		echo '<form method="post">';
		wp_nonce_field( 'amanah_create_search_intent', 'amanah_search_intent_nonce' );
		echo '<input type="hidden" name="amanah_search_intent_action" value="create" />';
		echo '<table class="form-table" role="presentation"><tbody>';
		echo '<tr><th scope="row"><label for="amanah_search_intent_slug">Slug</label></th><td><select id="amanah_search_intent_slug" name="search_intent_slug">';
		foreach ( array( 'informational', 'commercial', 'transactional', 'navigational' ) as $slug ) {
			echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( ucfirst( $slug ) ) . '</option>';
		}
		echo '</select></td></tr>';
		echo '<tr><th scope="row"><label for="amanah_search_intent_name">Name</label></th><td><input type="text" id="amanah_search_intent_name" name="search_intent_name" class="regular-text" required /></td></tr>';
		echo '<tr><th scope="row"><label for="amanah_search_intent_description">Description</label></th><td><textarea id="amanah_search_intent_description" name="search_intent_description" class="large-text" rows="4"></textarea></td></tr>';
		echo '</tbody></table>';
		echo '<p><button type="submit" class="button button-primary">Add Search Intent</button></p>';
		echo '</form>';

		$items = $this->list_intents->execute();
		echo '<h2>Search Intent Records</h2>';
		if ( empty( $items ) ) {
			echo '<p>No search intent records yet.</p>';
		} else {
			echo '<table class="widefat striped"><thead><tr><th>ID</th><th>Slug</th><th>Name</th><th>Description</th></tr></thead><tbody>';
			foreach ( $items as $item ) {
				$data = $item->toArray();
				echo '<tr><td>' . esc_html( (string) $data['id'] ) . '</td><td>' . esc_html( $data['slug'] ) . '</td><td>' . esc_html( $data['name'] ) . '</td><td>' . esc_html( $data['description'] ) . '</td></tr>';
			}
			echo '</tbody></table>';
		}
		echo '</div>';
	}

	private function handlePost(): void {
		check_admin_referer( 'amanah_create_search_intent', 'amanah_search_intent_nonce' );
		$slug = sanitize_key( wp_unslash( (string) ( $_POST['search_intent_slug'] ?? '' ) ) );
		$name = sanitize_text_field( wp_unslash( (string) ( $_POST['search_intent_name'] ?? '' ) ) );
		$description = sanitize_textarea_field( wp_unslash( (string) ( $_POST['search_intent_description'] ?? '' ) ) );
		try {
			$this->create_intent->execute( new CreateSearchIntentCommand( $slug, $name, $description ) );
			add_settings_error( 'amanah_search_intents', 'search_intent_created', 'Search intent created successfully.', 'success' );
		} catch ( \Throwable $e ) {
			add_settings_error( 'amanah_search_intents', 'search_intent_create_error', 'Search intent could not be created.', 'error' );
		}
	}
}
