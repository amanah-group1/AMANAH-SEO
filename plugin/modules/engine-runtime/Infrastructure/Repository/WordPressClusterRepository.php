<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\ClusterRepositoryInterface;
use Amanah\SEOEngine\Domain\Cluster\Cluster;

defined( 'ABSPATH' ) || exit;

final class WordPressClusterRepository extends WordPressRepository implements ClusterRepositoryInterface {
	public function __construct( \wpdb $wpdb ) {
		parent::__construct( $wpdb, $wpdb->prefix . 'amanah_seo_clusters' );
	}

	public function save( Cluster $cluster ): Cluster {
		$data = array(
			'name' => $cluster->getName(),
			'slug' => $cluster->getSlug(),
			'description' => $cluster->getDescription(),
			'status' => $cluster->getStatus(),
			'created_at' => current_time( 'mysql', true ),
			'updated_at' => current_time( 'mysql', true ),
		);
		$formats = array( '%s', '%s', '%s', '%s', '%s', '%s' );

		if ( null !== $cluster->getId() ) {
			$updated = $this->wpdb->update( $this->table, $data, array( 'id' => $cluster->getId() ), $formats, array( '%d' ) );
			if ( false === $updated ) {
				throw new \RuntimeException( 'Cluster could not be stored.' );
			}
			return $cluster;
		}

		$inserted = $this->wpdb->insert( $this->table, $data, $formats );
		if ( false === $inserted ) {
			throw new \RuntimeException( 'Cluster could not be stored.' );
		}

		return new Cluster( $cluster->getName(), $cluster->getSlug(), $cluster->getStatus(), $cluster->getDescription(), (int) $this->wpdb->insert_id );
	}

	public function findById( int $id ): ?Cluster {
		$row = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ), ARRAY_A );
		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function findBySlug( string $slug ): ?Cluster {
		$row = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE slug = %s", $slug ), ARRAY_A );
		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function findAll( int $limit = 100 ): array {
		$limit = max( 1, min( 100, $limit ) );
		$rows = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d", $limit ), ARRAY_A );
		$items = array();

		foreach ( $rows as $row ) {
			$cluster = $this->hydrate( is_array( $row ) ? $row : null );
			if ( null !== $cluster ) {
				$items[] = $cluster;
			}
		}

		return $items;
	}

	private function hydrate( ?array $row ): ?Cluster {
		if ( null === $row ) {
			return null;
		}

		return new Cluster( (string) $row['name'], (string) $row['slug'], (string) $row['status'], (string) $row['description'], (int) $row['id'] );
	}
}
