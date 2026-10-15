<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\WordPress\Content;

use Amanah\SEOEngine\Application\Port\ContentPublisherInterface;

defined( 'ABSPATH' ) || exit;

final class WordPressContentPublisher implements ContentPublisherInterface {

	public function createDraft( array $data ): int {
		$data['post_status'] = 'draft';
		return $this->savePost( $data );
	}

	public function publish( array $data ): int {
		return $this->savePost( $data );
	}

	public function updateDraft( array $data ): int {
		if ( empty( $data['post_id'] ) ) {
			throw new \InvalidArgumentException( 'WordPress post ID is required to update a draft.' );
		}
		$data['post_status'] = 'draft';
		return $this->savePost( $data );
	}

	private function savePost( array $data ): int {
		$post = array(
			'post_title'   => isset( $data['title'] ) ? sanitize_text_field( (string) $data['title'] ) : '',
			'post_content' => isset( $data['content'] ) ? wp_kses_post( (string) $data['content'] ) : '',
			'post_excerpt' => isset( $data['excerpt'] ) ? wp_kses_post( (string) $data['excerpt'] ) : '',
			'post_status'  => isset( $data['post_status'] ) ? sanitize_key( (string) $data['post_status'] ) : 'draft',
			'post_type'    => isset( $data['post_type'] ) ? sanitize_key( (string) $data['post_type'] ) : 'post',
		);

		if ( isset( $data['post_id'] ) ) {
			$post['ID'] = absint( $data['post_id'] );
		}

		$post_id = wp_insert_post( wp_slash( $post ), true );

		if ( is_wp_error( $post_id ) ) {
			throw new \RuntimeException( 'WordPress content operation failed.' );
		}

		return (int) $post_id;
	}
}
