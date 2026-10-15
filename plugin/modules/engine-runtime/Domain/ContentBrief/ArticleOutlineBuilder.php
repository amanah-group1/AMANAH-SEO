<?php
declare( strict_types=1 );
namespace Amanah\SEOEngine\Domain\ContentBrief;
defined( 'ABSPATH' ) || exit;

/**
 * Deterministically builds an editable article outline from the primary
 * keyword and Potential Support Keywords. Search intent is editorial context,
 * never a literal heading.
 */
final class ArticleOutlineBuilder {
	public function build( string $primary_phrase, string $article_type, array $intent_coverage, array $support_keywords ): array {
		$primary_phrase = trim( $primary_phrase );
		$article_type   = strtolower( trim( $article_type ) );
		$outline = array(
			array( 'id' => 'h1_main', 'level' => 1, 'type' => 'h1', 'title' => '' !== $primary_phrase ? $primary_phrase : 'Artikel Utama' ),
			array( 'id' => 'overview', 'level' => 2, 'type' => 'h2', 'title' => $this->overviewTitle( $primary_phrase ) ),
		);
		$sections = array();
		foreach ( $support_keywords as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$id = (int) ( $item['id'] ?? 0 );
			$phrase = trim( (string) ( $item['phrase'] ?? $item['keyword'] ?? '' ) );
			if ( $id < 1 || '' === $phrase ) {
				continue;
			}
			$category = $this->classify( $phrase );
			if ( ! isset( $sections[ $category ] ) ) {
				$sections[ $category ] = array(
					'id' => 'topic_' . $category,
					'title' => $this->sectionTitle( $category, $primary_phrase ),
					'keywords' => array(),
					'index' => (int) $index,
				);
			}
			$sections[ $category ]['keywords'][] = array( 'id' => $id, 'phrase' => $phrase );
		}

		$mapping = array();
		foreach ( $this->orderSections( $sections ) as $section ) {
			$outline[] = array( 'id' => $section['id'], 'level' => 2, 'type' => 'h2', 'title' => $section['title'] );
			if ( 1 === count( $section['keywords'] ) ) {
				$keyword = $section['keywords'][0];
				if ( $this->isHeadingWorthyPhrase( $keyword['phrase'] ) ) {
					$outline[] = array(
						'id' => 'support_' . $keyword['id'],
						'level' => 3,
						'type' => 'h3',
						'title' => $this->headingFromPhrase( $keyword['phrase'] ),
					);
				}
			}
		}

		// Preserve the supplied support-keyword order for stable mapping persistence.
		foreach ( $support_keywords as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$id = (int) ( $item['id'] ?? 0 );
			$phrase = trim( (string) ( $item['phrase'] ?? $item['keyword'] ?? '' ) );
			if ( $id < 1 || '' === $phrase ) {
				continue;
			}
			$category = $this->classify( $phrase );
			if ( ! isset( $sections[ $category ] ) ) {
				continue;
			}
			$mapping[] = array(
				'keyword_id' => $id,
				'phrase' => $phrase,
				'section_id' => $sections[ $category ]['id'],
				'required' => true,
			);
		}

		if ( 'pillar' === $article_type && ! empty( $intent_coverage ) && empty( $sections ) ) {
			$outline[] = array(
				'id' => 'comprehensive_coverage',
				'level' => 2,
				'type' => 'h2',
				'title' => 'Informasi Penting tentang ' . $this->topicName( $primary_phrase ),
			);
		}
		$outline[] = array( 'id' => 'conclusion', 'level' => 2, 'type' => 'h2', 'title' => 'Kesimpulan' );

		return array( 'article_outline' => $outline, 'support_keyword_mapping' => $mapping );
	}

	private function overviewTitle( string $primary_phrase ): string {
		$topic = $this->topicName( $primary_phrase );
		return '' !== $topic ? 'Mengenal ' . $topic : 'Pengenalan';
	}

	private function sectionTitle( string $category, string $primary_phrase ): string {
		$topic = $this->topicName( $primary_phrase );
		$titles = array(
			'definition' => 'Apa Itu ' . $topic . '?',
			'types' => 'Jenis dan Karakteristik ' . $topic,
			'character' => 'Karakter dan Pengalaman ' . $topic,
			'benefits' => 'Manfaat dan Keunggulan ' . $topic,
			'how_to' => 'Cara Menggunakan atau Menikmati ' . $topic,
			'selection' => 'Cara Memilih ' . $topic,
			'comparison' => 'Perbandingan ' . $topic,
			'pricing' => 'Harga dan Pertimbangan Pembelian ' . $topic,
			'questions' => 'Pertanyaan Umum tentang ' . $topic,
			'other' => 'Informasi Lain tentang ' . $topic,
		);
		return $titles[ $category ] ?? $titles['other'];
	}

	private function topicName( string $primary_phrase ): string {
		return '' !== trim( $primary_phrase ) ? trim( $primary_phrase ) : 'Topik Ini';
	}

	private function classify( string $phrase ): string {
		$text = $this->normalize( $phrase );
		$rules = array(
			'definition' => array( 'apa itu', 'pengertian', 'arti', 'adalah', 'apa yang dimaksud' ),
			'how_to' => array( 'cara', 'langkah', 'tutorial', 'membuat', 'menyeduh', 'menggunakan', 'memakai', 'mengolah' ),
			'pricing' => array( 'harga', 'biaya', 'berapa', 'murah', 'mahal' ),
			'selection' => array( 'memilih', 'pilih', 'asli', 'terbaik', 'bagus', 'kualitas', 'tips' ),
			'comparison' => array( 'vs', 'versus', 'dibanding', 'perbedaan', 'beda dengan' ),
			'types' => array( 'jenis', 'varian', 'tipe', 'macam', 'robusta', 'arabica', 'arabika' ),
			'benefits' => array( 'manfaat', 'khasiat', 'kegunaan', 'kelebihan' ),
			'character' => array( 'rasa', 'karakter', 'aroma', 'enak', 'nikmat', 'kandungan' ),
			'questions' => array( 'siapa', 'kapan', 'dimana', 'di mana', 'mengapa', 'kenapa' ),
		);
		foreach ( $rules as $category => $markers ) {
			foreach ( $markers as $marker ) {
				if ( false !== strpos( $text, $marker ) ) {
					return $category;
				}
			}
		}
		return 'other';
	}

	private function normalize( string $value ): string {
		$value = strtolower( trim( $value ) );
		$value = preg_replace( '/\s+/', ' ', $value );
		return is_string( $value ) ? $value : '';
	}

	private function isHeadingWorthyPhrase( string $phrase ): bool {
		$text = $this->normalize( $phrase );
		return false !== strpos( $text, '?' )
			|| false !== strpos( $text, 'cara ' )
			|| false !== strpos( $text, 'langkah ' )
			|| false !== strpos( $text, 'tutorial ' )
			|| false !== strpos( $text, 'apa itu ' )
			|| false !== strpos( $text, 'pengertian ' )
			|| false !== strpos( $text, 'asli' )
			|| false !== strpos( $text, 'terbaik' )
			|| false !== strpos( $text, 'memilih' )
			|| false !== strpos( $text, 'enak' )
			|| false !== strpos( $text, 'nikmat' );
	}

	private function headingFromPhrase( string $phrase ): string {
		$title = trim( preg_replace( '/[?]+$/', '', $phrase ) );
		return '' !== $title ? ucfirst( $title ) : 'Subtopik';
	}

	private function orderSections( array $sections ): array {
		$order = array( 'definition', 'types', 'character', 'benefits', 'how_to', 'selection', 'comparison', 'pricing', 'questions', 'other' );
		$ordered = array();
		foreach ( $order as $category ) {
			if ( isset( $sections[ $category ] ) ) {
				$ordered[] = $sections[ $category ];
			}
		}
		return $ordered;
	}
}
