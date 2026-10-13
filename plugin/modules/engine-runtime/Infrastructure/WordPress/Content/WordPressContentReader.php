<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\WordPress\Content;

use Amanah\SEOEngine\Application\Port\ContentReaderInterface;

defined( 'ABSPATH' ) || exit;

final class WordPressContentReader implements ContentReaderInterface {
	public function getPost( int $post_id ): ?array {
		if ( $post_id < 1 ) {
			return null;
		}

		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post ) {
			return null;
		}

		return array(
			'id'         => (int) $post->ID,
			'title'      => (string) $post->post_title,
			'content'    => (string) $post->post_content,
			'excerpt'    => (string) $post->post_excerpt,
			'status'     => (string) $post->post_status,
			'modified_at'=> (string) $post->post_modified_gmt,
		);
	}
}
