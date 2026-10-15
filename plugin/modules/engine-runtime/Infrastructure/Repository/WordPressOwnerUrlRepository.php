<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\OwnerUrlRepositoryInterface;
use Amanah\SEOEngine\Domain\OwnerUrl\OwnerUrl;

defined( 'ABSPATH' ) || exit;

final class WordPressOwnerUrlRepository extends WordPressRepository implements OwnerUrlRepositoryInterface {

	public function __construct( \wpdb $wpdb ) {
		parent::__construct(
			$wpdb,
			$wpdb->prefix . 'amanah_seo_owner_urls'
		);
	}

	public function save( OwnerUrl $owner_url ): OwnerUrl {
		$data = array(
			'url'               => $owner_url->getUrl(),
			'normalized_url'    => $owner_url->getNormalizedUrl(),
			'wordpress_post_id' => $owner_url->getWordPressPostId(),
			'post_type'         => $owner_url->getPostType(),
			'status'            => $owner_url->getStatus(),
			'created_at'        => current_time( 'mysql', true ),
			'updated_at'        => current_time( 'mysql', true ),
		);

		$formats = array( '%s', '%s', '%d', '%s', '%s', '%s', '%s' );

		if ( null !== $owner_url->getId() ) {
			$updated = $this->wpdb->update(
				$this->table,
				$data,
				array( 'id' => $owner_url->getId() ),
				$formats,
				array( '%d' )
			);

			if ( false === $updated ) {
				throw new \RuntimeException( 'Owner URL could not be stored.' );
			}

			return $owner_url;
		}

		$inserted = $this->wpdb->insert( $this->table, $data, $formats );

		if ( false === $inserted ) {
			throw new \RuntimeException( 'Owner URL could not be stored.' );
		}

		return new OwnerUrl(
			$owner_url->getUrl(),
			$owner_url->getStatus(),
			(int) $this->wpdb->insert_id,
			$owner_url->getWordPressPostId(),
			$owner_url->getPostType()
		);
	}

	public function findById( int $id ): ?OwnerUrl {
		return $this->hydrate( $this->findRawById( $id ) );
	}

	public function findByNormalizedUrl( string $normalized_url ): ?OwnerUrl {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE normalized_url = %s LIMIT 1",
				$normalized_url
			),
			ARRAY_A
		);

		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function findAll( int $limit = 100 ): array {
		$limit = max( 1, min( 100, $limit ) );

		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d",
				$limit
			),
			ARRAY_A
		);

		$items = array();

		foreach ( $rows as $row ) {
			$owner_url = $this->hydrate( is_array( $row ) ? $row : null );

			if ( null !== $owner_url ) {
				$items[] = $owner_url;
			}
		}

		return $items;
	}

	private function hydrate( ?array $row ): ?OwnerUrl {
		if ( null === $row ) {
			return null;
		}

		return new OwnerUrl(
			(string) $row['url'],
			(string) $row['status'],
			(int) $row['id'],
			null !== $row['wordpress_post_id'] ? (int) $row['wordpress_post_id'] : null,
			null !== $row['post_type'] ? (string) $row['post_type'] : null
		);
	}
}
