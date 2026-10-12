<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Application\UseCase;

use Amanah\SEOEngine\Application\Port\ContentReaderInterface;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRevisionRepositoryInterface;
use Amanah\SEOEngine\Domain\Content\Content;
use Amanah\SEOEngine\Domain\Content\ContentRevision;

defined( 'ABSPATH' ) || exit;

final class SyncWordPressContent {
	private $contents;
	private $revisions;
	private $reader;

	public function __construct(
		ContentRepositoryInterface $contents,
		ContentRevisionRepositoryInterface $revisions,
		ContentReaderInterface $reader
	) {
		$this->contents = $contents;
		$this->revisions = $revisions;
		$this->reader = $reader;
	}

	public function execute( int $content_id ): Content {
		$content = $this->contents->findById( $content_id );
		if ( null === $content ) {
			throw new \RuntimeException( 'Content not found.' );
		}

		$post_id = $content->getWordPressPostId();
		if ( null === $post_id ) {
			throw new \RuntimeException( 'WordPress draft is not linked to this content.' );
		}

		$post = $this->reader->getPost( $post_id );
		if ( null === $post ) {
			throw new \RuntimeException( 'Linked WordPress post could not be found.' );
		}

		$hash = $this->makeHash( $post );
		$latest = $this->revisions->findLatestByContentId( $content_id );

		if ( null !== $latest && hash_equals( $latest->getContentHash(), $hash ) ) {
			return $content;
		}

		$revision_number = null !== $latest ? $latest->getRevisionNumber() + 1 : 1;
		$revision = new ContentRevision(
			$content_id,
			$post_id,
			$revision_number,
			'wordpress',
			(string) $post['title'],
			(string) $post['content'],
			(string) $post['excerpt'],
			$hash,
			(string) $post['modified_at']
		);
		$this->revisions->save( $revision );

		if ( null === $latest && $hash === $this->makeHashFromContent( $content ) ) {
			return $content;
		}

		$updated = new Content(
			(string) $post['content'],
			$content->getVersion() + 1,
			'generated',
			$content->getKeywordId(),
			$content->getBlueprintId(),
			$content->getId(),
			(string) $post['title'],
			(string) $post['excerpt'],
			$content->getMetadata(),
			$post_id
		);

		return $this->contents->save( $updated );
	}

	private function makeHashFromContent( Content $content ): string {
		return hash(
			'sha256',
			wp_json_encode(
				array(
					'title'   => (string) $content->getTitle(),
					'content' => (string) $content->getBody(),
					'excerpt' => (string) $content->getExcerpt(),
				)
			)
		);
	}

	private function makeHash( array $post ): string {
		return hash(
			'sha256',
			wp_json_encode(
				array(
					'title'   => (string) $post['title'],
					'content' => (string) $post['content'],
					'excerpt' => (string) $post['excerpt'],
				)
			)
		);
	}
}
