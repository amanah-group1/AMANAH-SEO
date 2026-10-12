<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Migration\Migrations;

use Amanah\SEOEngine\Infrastructure\Migration\MigrationInterface;

defined( 'ABSPATH' ) || exit;

final class Version15DefaultSearchIntents implements MigrationInterface {

	private $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public function getVersion(): int {
		return 15;
	}

	public function getName(): string {
		return 'default_search_intents';
	}

	public function up(): void {
		$table = $this->wpdb->prefix . 'amanah_seo_search_intents';

		$defaults = array(
			array( 'informational', 'Informational', 'Intent to learn, understand, or research a topic.' ),
			array( 'commercial', 'Commercial', 'Intent to compare options, products, or services before deciding.' ),
			array( 'transactional', 'Transactional', 'Intent to take an action such as buying, ordering, booking, or signing up.' ),
			array( 'navigational', 'Navigational', 'Intent to reach a specific website, brand, page, or destination.' ),
		);

		foreach ( $defaults as $intent ) {
			$exists = $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT id FROM {$table} WHERE slug = %s LIMIT 1",
					$intent[0]
				)
			);

			if ( null !== $exists ) {
				continue;
			}

			$this->wpdb->insert(
				$table,
				array(
					'slug'        => $intent[0],
					'name'        => $intent[1],
					'description' => $intent[2],
					'created_at'  => current_time( 'mysql', true ),
					'updated_at'  => current_time( 'mysql', true ),
				),
				array( '%s', '%s', '%s', '%s', '%s' )
			);

			if ( false === $this->wpdb->insert_id ) {
				throw new \RuntimeException( 'Default search intent could not be created.' );
			}
		}
	}
}
