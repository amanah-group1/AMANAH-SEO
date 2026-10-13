<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Command;

defined( 'ABSPATH' ) || exit;

final class AssignKeywordOwnerUrlCommand {

	private $keyword_id;
	private $owner_url_id;

	public function __construct( int $keyword_id, int $owner_url_id ) {
		if ( $keyword_id < 1 || $owner_url_id < 1 ) {
			throw new \InvalidArgumentException( 'Keyword and Owner URL IDs are required.' );
		}

		$this->keyword_id   = $keyword_id;
		$this->owner_url_id = $owner_url_id;
	}

	public function getKeywordId(): int { return $this->keyword_id; }
	public function getOwnerUrlId(): int { return $this->owner_url_id; }
}
