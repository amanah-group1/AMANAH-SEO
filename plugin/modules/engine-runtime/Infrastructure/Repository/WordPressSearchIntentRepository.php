<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\SearchIntentRepositoryInterface;
use Amanah\SEOEngine\Domain\SearchIntent\SearchIntent;

defined( 'ABSPATH' ) || exit;

final class WordPressSearchIntentRepository extends WordPressRepository implements SearchIntentRepositoryInterface {
	public function __construct( \wpdb $wpdb ) {
		parent::__construct( $wpdb, $wpdb->prefix . 'amanah_seo_search_intents' );
	}

	public function save( SearchIntent $intent ): SearchIntent {
		$data = array(
			'slug' => $intent->getSlug(),
			'name' => $intent->getName(),
			'description' => $intent->getDescription(),
			'created_at' => current_time( 'mysql', true ),
			'updated_at' => current_time( 'mysql', true ),
		);
		$formats = array( '%s', '%s', '%s', '%s', '%s' );

		if ( null !== $intent->getId() ) {
			$updated = $this->wpdb->update( $this->table, $data, array( 'id' => $intent->getId() ), $formats, array( '%d' ) );
			if ( false === $updated ) {
				throw new \RuntimeException( 'Search intent could not be stored.' );
			}
			return $intent;
		}

		$inserted = $this->wpdb->insert( $this->table, $data, $formats );
		if ( false === $inserted ) {
			throw new \RuntimeException( 'Search intent could not be stored.' );
		}

		return new SearchIntent( $intent->getSlug(), $intent->getName(), $intent->getDescription(), (int) $this->wpdb->insert_id );
	}

	public function findById( int $id ): ?SearchIntent {
		$row = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ), ARRAY_A );
		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function findBySlug( string $slug ): ?SearchIntent {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE slug = %s", $slug ),
			ARRAY_A
		);
		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function findAll( int $limit = 100 ): array {
		$limit = max( 1, min( 100, $limit ) );
		$rows = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d", $limit ), ARRAY_A );
		$items = array();
		foreach ( $rows as $row ) {
			$intent = $this->hydrate( is_array( $row ) ? $row : null );
			if ( null !== $intent ) {
				$items[] = $intent;
			}
		}
		return $items;
	}

	private function hydrate( ?array $row ): ?SearchIntent {
		if ( null === $row ) {
			return null;
		}
		return new SearchIntent( (string) $row['slug'], (string) $row['name'], (string) $row['description'], (int) $row['id'] );
	}
}
