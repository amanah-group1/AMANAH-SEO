<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\RecommendationRepositoryInterface;
use Amanah\SEOEngine\Domain\Recommendation\Recommendation;

defined( 'ABSPATH' ) || exit;

final class WordPressRecommendationRepository extends WordPressRepository implements RecommendationRepositoryInterface {

	public function __construct( \wpdb $wpdb ) {
		parent::__construct( $wpdb, $wpdb->prefix . 'amanah_seo_recommendations' );
	}

	public function findByCanonicalKey( string $canonical_key ): ?Recommendation {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE canonical_key = %s LIMIT 1",
				$canonical_key
			),
			ARRAY_A
		);
		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function findAll( int $limit = 100 ): array {
		$limit = max( 1, min( 500, $limit ) );
		$rows = $this->wpdb->get_results(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} ORDER BY FIELD(status,'pending','in_progress','completed','dismissed','verified'), FIELD(priority,'critical','warning','task'), id DESC LIMIT %d",
				$limit
			),
			ARRAY_A
		);
		$items = array();
		foreach ( $rows as $row ) {
			$item = $this->hydrate( is_array( $row ) ? $row : null );
			if ( null !== $item ) { $items[] = $item; }
		}
		return $items;
	}

	public function save( Recommendation $recommendation ): Recommendation {
		$now = current_time( 'mysql', true );
		$data = array(
			'canonical_key' => $recommendation->getCanonicalKey(),
			'issue_id' => $recommendation->getIssueId(),
			'priority' => $recommendation->getPriority(),
			'recommendation' => $recommendation->getRecommendation(),
			'target_url' => $recommendation->getTargetUrl(),
			'status' => $recommendation->getStatus(),
			'execution_mode' => $recommendation->getExecutionMode(),
			'accepted_at' => $recommendation->getAcceptedAt(),
			'completed_at' => $recommendation->getCompletedAt(),
			'dismissed_at' => $recommendation->getDismissedAt(),
			'verified_at' => $recommendation->getVerifiedAt(),
			'updated_at' => $now,
		);
		$formats = array( '%s','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s' );

		if ( null !== $recommendation->getId() ) {
			$this->wpdb->update( $this->table, $data, array( 'id' => $recommendation->getId() ), $formats, array( '%d' ) );
			return $recommendation;
		}

		$data['created_at'] = $now;
		$formats[] = '%s';
		$inserted = $this->wpdb->insert( $this->table, $data, $formats );
		if ( false === $inserted ) {
			throw new \RuntimeException( 'Recommendation could not be stored.' );
		}
		return new Recommendation(
			$recommendation->getCanonicalKey(), $recommendation->getIssueId(),
			$recommendation->getPriority(), $recommendation->getRecommendation(),
			$recommendation->getTargetUrl(), $recommendation->getStatus(),
			$recommendation->getExecutionMode(), (int) $this->wpdb->insert_id,
			$recommendation->getAcceptedAt(), $recommendation->getCompletedAt(),
			$recommendation->getDismissedAt(), $recommendation->getVerifiedAt()
		);
	}

	public function updateStatus( int $id, string $status ): bool {
		$now = current_time( 'mysql', true );
		$data = array( 'status' => $status, 'updated_at' => $now );
		$formats = array( '%s', '%s' );
		if ( 'in_progress' === $status || 'completed' === $status ) { $data['accepted_at'] = $now; $formats[] = '%s'; }
		if ( 'completed' === $status ) { $data['completed_at'] = $now; $formats[] = '%s'; }
		if ( 'dismissed' === $status ) { $data['dismissed_at'] = $now; $formats[] = '%s'; }
		if ( 'verified' === $status ) { $data['verified_at'] = $now; $formats[] = '%s'; }
		return false !== $this->wpdb->update( $this->table, $data, array( 'id' => $id ), $formats, array( '%d' ) );
	}

	private function hydrate( ?array $row ): ?Recommendation {
		if ( null === $row ) { return null; }
		return new Recommendation(
			(string) $row['canonical_key'], (int) $row['issue_id'],
			(string) $row['priority'], (string) $row['recommendation'],
			(string) $row['target_url'], (string) $row['status'],
			(string) $row['execution_mode'], (int) $row['id'],
			null !== $row['accepted_at'] ? (string) $row['accepted_at'] : null,
			null !== $row['completed_at'] ? (string) $row['completed_at'] : null,
			null !== $row['dismissed_at'] ? (string) $row['dismissed_at'] : null,
			null !== $row['verified_at'] ? (string) $row['verified_at'] : null
		);
	}
}
