<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\KeywordIntelligence;

use Amanah\SEOEngine\Application\KeywordIntelligence\KeywordCandidateSourceInterface;

defined( 'ABSPATH' ) || exit;

final class PillarTemplateCandidateSource implements KeywordCandidateSourceInterface {

	public function findCandidates( string $pillar_phrase ): array {
		$templates = array(
			'apa itu %s',
			'cara memilih %s',
			'cara menggunakan %s',
			'cara membuat %s',
			'cara menyeduh %s',
			'harga %s',
			'%s asli',
			'%s terbaik',
			'jenis %s',
			'manfaat %s',
			'perbedaan %s',
			'%s untuk pemula',
			'%s untuk usaha',
		);

		$candidates = array();

		foreach ( $templates as $template ) {
			$candidates[] = array(
				'keyword' => trim( sprintf( $template, $pillar_phrase ) ),
				'source' => 'fallback_template',
			);
		}

		return $candidates;
	}
}
