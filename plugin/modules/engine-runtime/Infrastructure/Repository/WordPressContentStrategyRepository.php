<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\ContentStrategyRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentStrategy\ContentStrategy;

defined( 'ABSPATH' ) || exit;

final class WordPressContentStrategyRepository extends WordPressRepository implements ContentStrategyRepositoryInterface {

	public function __construct( \wpdb $wpdb ) {
		parent::__construct( $wpdb, $wpdb->prefix . 'amanah_seo_content_strategies' );
	}

	public function save( ContentStrategy $strategy ): ContentStrategy {
		$data = array(
			'keyword_id' => $strategy->getKeywordId(),
			'strategy_data' => wp_json_encode( $strategy->getData() ),
			'version' => $strategy->getVersion(),
			'status' => $strategy->getStatus(),
			'created_at' => current_time( 'mysql', true ),
			'updated_at' => current_time( 'mysql', true ),
		);
		$formats = array( '%d', '%s', '%d', '%s', '%s', '%s' );

		if ( null !== $strategy->getId() ) {
			$updated = $this->wpdb->update(
				$this->table,
				$data,
				array( 'id' => $strategy->getId() ),
				$formats,
				array( '%d' )
			);

			if ( false === $updated ) {
				throw new \RuntimeException( 'Content strategy could not be stored.' );
			}

			return $strategy;
		}

		$inserted = $this->wpdb->insert( $this->table, $data, $formats );

		if ( false === $inserted ) {
			throw new \RuntimeException( 'Content strategy could not be stored.' );
		}

		return new ContentStrategy(
			$strategy->getKeywordId(),
			$strategy->getData(),
			$strategy->getVersion(),
			$strategy->getStatus(),
			(int) $this->wpdb->insert_id
		);
	}

	public function findById( int $id ): ?ContentStrategy {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE id = %d LIMIT 1",
				$id
			),
			ARRAY_A
		);

		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function findByKeywordId( int $keyword_id ): ?ContentStrategy {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE keyword_id = %d ORDER BY version DESC, id DESC LIMIT 1",
				$keyword_id
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
			$strategy = $this->hydrate( is_array( $row ) ? $row : null );

			if ( null !== $strategy ) {
				$items[] = $strategy;
			}
		}

		return $items;
	}

	private function hydrate( ?array $row ): ?ContentStrategy {
		if ( null === $row ) {
			return null;
		}

		$data = json_decode( (string) $row['strategy_data'], true );

		return new ContentStrategy(
			(int) $row['keyword_id'],
			is_array( $data ) ? $data : array(),
			(int) $row['version'],
			(string) $row['status'],
			(int) $row['id']
		);
	}
}
