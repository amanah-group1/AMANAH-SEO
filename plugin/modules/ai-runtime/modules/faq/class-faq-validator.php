<?php
/**
 * CORE-007 FAQ response validator.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_FAQ_Validator {
	public function validate( $result ) {
		if ( ! is_array( $result ) || ! isset( $result['faq'] ) || ! is_array( $result['faq'] ) || empty( $result['faq'] ) ) {
			return new WP_Error( 'invalid_faq_response', __( 'The FAQ response is invalid.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
		}

		$faq = array();
		foreach ( $result['faq'] as $item ) {
			if ( ! is_array( $item ) || empty( trim( (string) ( $item['question'] ?? '' ) ) ) || empty( trim( (string) ( $item['answer'] ?? '' ) ) ) ) {
				return new WP_Error( 'invalid_faq_item', __( 'The generated FAQ contains an invalid item.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
			}
			$faq[] = array(
				'question' => sanitize_text_field( $item['question'] ),
				'answer'   => sanitize_textarea_field( $item['answer'] ),
			);
		}

		return array(
			'faq'    => $faq,
			'schema' => isset( $result['schema'] ) ? $result['schema'] : null,
		);
	}
}
