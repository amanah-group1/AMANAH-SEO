<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

use Amanah\SEOEngine\Application\Repository\ContentRevisionRepositoryInterface;
use Amanah\SEOEngine\Domain\Content\ContentRevision;

defined( 'ABSPATH' ) || exit;

final class WordPressContentRevisionRepository extends WordPressRepository implements ContentRevisionRepositoryInterface {
	public function __construct( \wpdb $wpdb ) {
		parent::__construct( $wpdb, $wpdb->prefix . 'amanah_seo_content_revisions' );
	}

	public function findLatestByContentId( int $content_id ): ?ContentRevision {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE content_id=%d ORDER BY revision_number DESC, id DESC LIMIT 1",
				$content_id
			),
			ARRAY_A
		);

		return $this->hydrate( is_array( $row ) ? $row : null );
	}

	public function save( ContentRevision $revision ): ContentRevision {
		$data = array(
			'content_id'             => $revision->getContentId(),
			'wordpress_post_id'      => $revision->getWordPressPostId(),
			'revision_number'        => $revision->getRevisionNumber(),
			'source'                 => $revision->getSource(),
			'title'                  => $revision->getTitle(),
			'content'                => $revision->getBody(),
			'excerpt'                => $revision->getExcerpt(),
			'content_hash'           => $revision->getContentHash(),
			'wordpress_modified_at'  => $revision->getWordPressModifiedAt(),
			'created_at'             => current_time( 'mysql', true ),
		);

		$formats = array( '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' );

		if ( null !== $revision->getId() ) {
			$this->wpdb->update( $this->table, $data, array( 'id' => $revision->getId() ), $formats, array( '%d' ) );
			return $revision;
		}

		if ( false === $this->wpdb->insert( $this->table, $data, $formats ) ) {
			throw new \RuntimeException( 'Content revision could not be stored.' );
		}

		return new ContentRevision(
			$revision->getContentId(),
			$revision->getWordPressPostId(),
			$revision->getRevisionNumber(),
			$revision->getSource(),
			$revision->getTitle(),
			$revision->getBody(),
			$revision->getExcerpt(),
			$revision->getContentHash(),
			$revision->getWordPressModifiedAt(),
			(int) $this->wpdb->insert_id,
			current_time( 'mysql', true )
		);
	}

	private function hydrate( ?array $row ): ?ContentRevision {
		if ( null === $row ) {
			return null;
		}

		return new ContentRevision(
			(int) $row['content_id'],
			(int) $row['wordpress_post_id'],
			(int) $row['revision_number'],
			(string) $row['source'],
			(string) $row['title'],
			(string) $row['content'],
			(string) $row['excerpt'],
			(string) $row['content_hash'],
			(string) $row['wordpress_modified_at'],
			(int) $row['id'],
			(string) $row['created_at']
		);
	}
}
