<?php
/**
 * CORE-007 FAQ post-meta repository.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_FAQ_Repository {
	const META_KEY = '_amanah_seo_ai_faq';

	public function save( $post_id, array $payload ) {
		return update_post_meta( absint( $post_id ), self::META_KEY, $payload );
	}

	public function find( $post_id ) {
		$value = get_post_meta( absint( $post_id ), self::META_KEY, true );
		return is_array( $value ) ? $value : null;
	}
}
