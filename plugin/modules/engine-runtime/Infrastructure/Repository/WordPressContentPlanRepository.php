<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\ContentPlanRepositoryInterface;
use Amanah\SEOEngine\Domain\ContentPlan\ContentPlan;

defined( 'ABSPATH' ) || exit;

final class WordPressContentPlanRepository extends WordPressRepository implements ContentPlanRepositoryInterface {

	public function __construct( \wpdb $wpdb ) {
		parent::__construct( $wpdb, $wpdb->prefix . 'amanah_seo_content_plans' );
	}

	public function save( ContentPlan $plan ): ContentPlan {
		$data = array(
			'keyword_id' => $plan->getKeywordId(),
			'strategy_id' => $plan->getStrategyId(),
			'plan_data' => wp_json_encode( $plan->getData() ),
			'version' => $plan->getVersion(),
			'status' => $plan->getStatus(),
			'created_at' => current_time( 'mysql', true ),
			'updated_at' => current_time( 'mysql', true ),
		);
		$formats = array( '%d', '%d', '%s', '%d', '%s', '%s', '%s' );

		if ( null !== $plan->getId() ) {
			$updated = $this->wpdb->update(
				$this->table,
				$data,
				array( 'id' => $plan->getId() ),
				$formats,
				array( '%d' )
			);

			if ( false === $updated ) {
				throw new \RuntimeException( 'Content Plan could not be stored.' );
			}

			return $plan;
		}

		$inserted = $this->wpdb->insert( $this->table, $data, $formats );

		if ( false === $inserted ) {
			throw new \RuntimeException( 'Content Plan could not be stored.' );
		}

		return new ContentPlan(
			$plan->getKeywordId(),
			$plan->getData(),
			$plan->getVersion(),
			$plan->getStatus(),
			$plan->getStrategyId(),
			(int) $this->wpdb->insert_id
		);
	}

	public function findById( int $id ): ?ContentPlan {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d LIMIT 1", $id ),
			ARRAY_A
		);

		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function findByKeywordId( int $keyword_id ): ?ContentPlan {
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
			$this->wpdb->prepare( "SELECT * FROM {$this->table} ORDER BY id DESC LIMIT %d", $limit ),
			ARRAY_A
		);

		$items = array();
		foreach ( $rows as $row ) {
			$plan = $this->hydrate( is_array( $row ) ? $row : null );
			if ( null !== $plan ) {
				$items[] = $plan;
			}
		}

		return $items;
	}

	private function hydrate( ?array $row ): ?ContentPlan {
		if ( null === $row ) {
			return null;
		}

		$data = json_decode( (string) $row['plan_data'], true );

		return new ContentPlan(
			(int) $row['keyword_id'],
			is_array( $data ) ? $data : array(),
			(int) $row['version'],
			(string) $row['status'],
			isset( $row['strategy_id'] ) ? (int) $row['strategy_id'] : null,
			(int) $row['id']
		);
	}
}
