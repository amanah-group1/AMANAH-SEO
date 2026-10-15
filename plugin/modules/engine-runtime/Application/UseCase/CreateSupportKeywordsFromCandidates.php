<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Command\CreateKeywordCommand;

defined( 'ABSPATH' ) || exit;

final class CreateSupportKeywordsFromCandidates {

	private $create_keyword;

	public function __construct( CreateKeyword $create_keyword ) {
		$this->create_keyword = $create_keyword;
	}

	public function execute( int $pillar_keyword_id, array $candidates ): array {
		$created = array();

		foreach ( $candidates as $candidate ) {
			if ( ! is_array( $candidate ) ) {
				continue;
			}

			$phrase = trim( (string) ( $candidate['keyword'] ?? '' ) );
			$target_url = trim( (string) ( $candidate['target_url'] ?? '' ) );

			if ( '' === $phrase || '' === $target_url ) {
				continue;
			}

			$created[] = $this->create_keyword->execute(
				new CreateKeywordCommand(
					$phrase,
					'supporting',
					$target_url,
					'support',
					$pillar_keyword_id
				)
			);
		}

		return $created;
	}
}
