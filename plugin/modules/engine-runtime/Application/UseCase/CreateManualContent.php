<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\DTO\ContentData;
use Amanah\SEOEngine\Application\Port\ContentPublisherInterface;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Domain\Content\Content;
use Amanah\SEOEngine\Domain\Content\ContentValidationService;

defined( 'ABSPATH' ) || exit;

final class CreateManualContent {

	private $blueprints;
	private $contents;
	private $validation;
	private $publisher;

	public function __construct(
		PageBlueprintRepositoryInterface $blueprints,
		ContentRepositoryInterface $contents,
		ContentValidationService $validation,
		ContentPublisherInterface $publisher
	) {
		$this->blueprints = $blueprints;
		$this->contents = $contents;
		$this->validation = $validation;
		$this->publisher = $publisher;
	}

	public function execute( int $keyword_id ): ContentData {
		$blueprint = $this->blueprints->findByKeywordId( $keyword_id );

		if ( null === $blueprint ) {
			throw new \RuntimeException( 'Page Blueprint not found. Create Page Blueprint before creating manual Content.' );
		}

		$existing = $this->contents->findLatestByBlueprintId( (int) $blueprint->getId() );
		if ( null !== $existing ) {
			return ContentData::fromDomain( $existing );
		}

		$data = $blueprint->getData();
		$keyword_data = isset( $data['keyword'] ) && is_array( $data['keyword'] ) ? $data['keyword'] : array();
		$keyword = trim( (string) ( $keyword_data['phrase'] ?? $keyword_data['keyword'] ?? '' ) );
		$title = '' !== $keyword ? $keyword : 'Untitled SEO Content';

		$body = $this->buildStarterBody( $data );
		$content = new Content(
			$body,
			1,
			'draft',
			$keyword_id,
			$blueprint->getId(),
			null,
			$title,
			'',
			array(
				'creation_mode' => 'manual',
				'starter_source' => 'page_blueprint',
			)
		);

		$this->validation->validate( $content, $blueprint );
		$saved = $this->contents->save( $content );

		$post_id = $this->publisher->createDraft(
			array(
				'title' => $saved->getTitle(),
				'content' => $saved->getBody(),
				'excerpt' => $saved->getExcerpt(),
				'post_type' => 'post',
			)
		);

		$linked = new Content(
			$saved->getBody(),
			$saved->getVersion(),
			$saved->getStatus(),
			$saved->getKeywordId(),
			$saved->getBlueprintId(),
			$saved->getId(),
			$saved->getTitle(),
			$saved->getExcerpt(),
			$saved->getMetadata(),
			$post_id
		);

		return ContentData::fromDomain( $this->contents->save( $linked ) );
	}

	private function buildStarterBody( array $data ): string {
		$sections = isset( $data['sections'] ) && is_array( $data['sections'] ) ? $data['sections'] : array();
		$body = '<p><strong>Draf awal berdasarkan Page Blueprint.</strong> Lengkapi dan revisi seluruh bagian sebelum Quality Evaluation dan publish.</p>';

		foreach ( $sections as $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}

			$title = trim( (string) ( $section['title'] ?? '' ) );
			if ( '' === $title ) {
				continue;
			}

			$body .= '<h2>' . esc_html( $title ) . '</h2>';
			$body .= '<p>Tulis pembahasan yang memenuhi tujuan bagian ini sesuai Page Blueprint.</p>';
		}

		return $body;
	}
}
