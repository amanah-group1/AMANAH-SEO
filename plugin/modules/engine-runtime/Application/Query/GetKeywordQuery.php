<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\Query;

defined( 'ABSPATH' ) || exit;

final class GetKeywordQuery {

	private $keyword_id;

	public function __construct( int $keyword_id ) {
		if ( $keyword_id < 1 ) {
			throw new \InvalidArgumentException( 'Keyword ID is required.' );
		}

		$this->keyword_id = $keyword_id;
	}

	public function getKeywordId(): int { return $this->keyword_id; }
}
