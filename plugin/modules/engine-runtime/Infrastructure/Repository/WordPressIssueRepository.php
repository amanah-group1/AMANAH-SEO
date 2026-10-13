<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\IssueRepositoryInterface;
use Amanah\SEOEngine\Domain\Issue\Issue;

defined( 'ABSPATH' ) || exit;

final class WordPressIssueRepository extends WordPressRepository implements IssueRepositoryInterface {

	public function __construct( \wpdb $wpdb ) {
		parent::__construct( $wpdb, $wpdb->prefix . 'amanah_seo_issues' );
	}

	public function findByCanonicalKey( string $canonical_key ): ?Issue {
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
				"SELECT * FROM {$this->table} ORDER BY FIELD(status,'open','in_progress','resolved','dismissed'), FIELD(severity,'critical','warning','task'), id DESC LIMIT %d",
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

	public function save( Issue $issue ): Issue {
		$now = current_time( 'mysql', true );
		$data = array(
			'canonical_key' => $issue->getCanonicalKey(),
			'source' => $issue->getSource(),
			'issue_type' => $issue->getIssueType(),
			'severity' => $issue->getSeverity(),
			'status' => $issue->getStatus(),
			'url' => $issue->getUrl(),
			'message' => $issue->getMessage(),
			'evidence' => $issue->getEvidence(),
			'first_seen_at' => $issue->getFirstSeenAt() ?: $now,
			'last_seen_at' => $issue->getLastSeenAt() ?: $now,
			'resolved_at' => $issue->getResolvedAt(),
			'dismissed_at' => $issue->getDismissedAt(),
			'updated_at' => $now,
		);
		$formats = array( '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s' );

		if ( null !== $issue->getId() ) {
			$this->wpdb->update( $this->table, $data, array( 'id' => $issue->getId() ), $formats, array( '%d' ) );
			return $issue;
		}

		$data['created_at'] = $now;
		$formats[] = '%s';
		$inserted = $this->wpdb->insert( $this->table, $data, $formats );
		if ( false === $inserted ) {
			throw new \RuntimeException( 'Issue could not be stored: ' . (string) $this->wpdb->last_error );
		}
		return new Issue(
			$issue->getCanonicalKey(), $issue->getSource(), $issue->getIssueType(),
			$issue->getSeverity(), $issue->getStatus(), $issue->getUrl(),
			$issue->getMessage(), $issue->getEvidence(), (int) $this->wpdb->insert_id,
			$issue->getFirstSeenAt() ?: $now, $issue->getLastSeenAt() ?: $now,
			$issue->getResolvedAt(), $issue->getDismissedAt()
		);
	}

	public function updateStatus( int $id, string $status, ?string $resolved_at = null, ?string $dismissed_at = null ): bool {
		$updated = $this->wpdb->update(
			$this->table,
			array(
				'status' => $status,
				'resolved_at' => $resolved_at,
				'dismissed_at' => $dismissed_at,
				'updated_at' => current_time( 'mysql', true ),
			),
			array( 'id' => $id ),
			array( '%s','%s','%s','%s' ),
			array( '%d' )
		);
		return false !== $updated;
	}

	private function hydrate( ?array $row ): ?Issue {
		if ( null === $row ) { return null; }
		return new Issue(
			(string) $row['canonical_key'],
			(string) $row['source'],
			(string) $row['issue_type'],
			(string) $row['severity'],
			(string) $row['status'],
			(string) $row['url'],
			(string) $row['message'],
			(string) $row['evidence'],
			(int) $row['id'],
			(string) $row['first_seen_at'],
			(string) $row['last_seen_at'],
			null !== $row['resolved_at'] ? (string) $row['resolved_at'] : null,
			null !== $row['dismissed_at'] ? (string) $row['dismissed_at'] : null
		);
	}
}
