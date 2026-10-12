<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Service;

use Amanah\SEOEngine\Application\Repository\IssueRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\RecommendationRepositoryInterface;
use Amanah\SEOEngine\Domain\Issue\Issue;
use Amanah\SEOEngine\Domain\Recommendation\Recommendation;

defined( 'ABSPATH' ) || exit;

final class IssueRecommendationService {

	private $issues;
	private $recommendations;
	private $intelligence;

	public function __construct(
		IssueRepositoryInterface $issues,
		RecommendationRepositoryInterface $recommendations,
		SEOIntelligenceService $intelligence
	) {
		$this->issues = $issues;
		$this->recommendations = $recommendations;
		$this->intelligence = $intelligence;
	}

	public function build(): array {
		$data = $this->intelligence->build();
		$now = current_time( 'mysql', true );
		$current_keys = array();

		foreach ( $data['issues'] as $source_issue ) {
			$canonical_key = sha1( 'live_scan|' . (string) $source_issue['code'] . '|' . $this->normalizeUrl( (string) $source_issue['url'] ) );
			$current_keys[ $canonical_key ] = true;
			$existing = $this->issues->findByCanonicalKey( $canonical_key );
			$status = null !== $existing ? $existing->getStatus() : 'open';
			$issue = new Issue(
				$canonical_key,
				'live_scan',
				(string) $source_issue['code'],
				$this->normalizeSeverity( (string) $source_issue['severity'] ),
				$status,
				(string) $source_issue['url'],
				(string) $source_issue['message'],
				wp_json_encode( $source_issue ),
				null !== $existing ? $existing->getId() : null,
				null !== $existing ? $existing->getFirstSeenAt() : $now,
				$now,
				'open' === $status ? null : ( null !== $existing ? $existing->getResolvedAt() : null ),
				'open' === $status ? null : ( null !== $existing ? $existing->getDismissedAt() : null )
			);
			$issue = $this->issues->save( $issue );

			$recommendation_key = sha1( $canonical_key . '|' . (string) $source_issue['action'] );
			$existing_recommendation = $this->recommendations->findByCanonicalKey( $recommendation_key );
			$recommendation = new Recommendation(
				$recommendation_key,
				(int) $issue->getId(),
				$this->normalizeSeverity( (string) $source_issue['severity'] ),
				(string) $source_issue['action'],
				(string) $source_issue['url'],
				null !== $existing_recommendation ? $existing_recommendation->getStatus() : 'pending',
				'manual',
				null !== $existing_recommendation ? $existing_recommendation->getId() : null,
				null !== $existing_recommendation ? $existing_recommendation->getAcceptedAt() : null,
				null !== $existing_recommendation ? $existing_recommendation->getCompletedAt() : null,
				null !== $existing_recommendation ? $existing_recommendation->getDismissedAt() : null,
				null !== $existing_recommendation ? $existing_recommendation->getVerifiedAt() : null
			);
			$this->recommendations->save( $recommendation );
		}

		return array(
			'issues' => $this->issues->findAll( 500 ),
			'recommendations' => $this->recommendations->findAll( 500 ),
			'synced_at' => $now,
		);
	}

	public function updateIssueStatus( int $id, string $status ): bool {
		$allowed = array( 'open', 'in_progress', 'resolved', 'dismissed' );
		if ( ! in_array( $status, $allowed, true ) ) { return false; }
		$now = current_time( 'mysql', true );
		return $this->issues->updateStatus(
			$id,
			$status,
			'resolved' === $status ? $now : null,
			'dismissed' === $status ? $now : null
		);
	}

	public function updateRecommendationStatus( int $id, string $status ): bool {
		$allowed = array( 'pending', 'in_progress', 'completed', 'dismissed', 'verified' );
		if ( ! in_array( $status, $allowed, true ) ) { return false; }
		return $this->recommendations->updateStatus( $id, $status );
	}

	private function normalizeSeverity( string $severity ): string {
		if ( 'critical' === $severity ) { return 'critical'; }
		if ( 'warning' === $severity ) { return 'warning'; }
		return 'task';
	}

	private function normalizeUrl( string $url ): string {
		return rtrim( strtolower( (string) strtok( $url, '#' ) ), '/' );
	}
}
