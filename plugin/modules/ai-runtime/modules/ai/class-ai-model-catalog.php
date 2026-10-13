<?php
/**
 * Controlled AI model catalog for the Settings surface.
 *
 * @package AMANAH_SEO_AI
 */

defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Model_Catalog {

	/**
	 * Return the controlled model list for a provider.
	 *
	 * The Settings UI must not depend on a live provider discovery request just
	 * to render its model selector. Provider availability/authentication is
	 * validated when AI execution actually runs.
	 *
	 * @param string $provider Provider slug.
	 * @param string $api_key  Retained for interface compatibility.
	 * @return array
	 */
	public function get_models( $provider, $api_key = '' ) {
		unset( $api_key );

		$provider = sanitize_key( $provider );

		$models = array(
			'gemini' => array(
				'gemini-3.8-flash',
				'gemini-3.7-flash',
				'gemini-3.6-flash',
				'gemini-3.5-flash',
				'gemini-3.5-flash-lite',
				'gemini-3.1-flash-lite',
			),
			'openai' => array(
				'gpt-6-astra',
				'gpt-6.1-sol',
				'gpt-6-luna',
				'gpt-5.6-sol',
				'gpt-5.6-terra',
				'gpt-5.6-luna',
				'gpt-5.5',
				'gpt-5.4',
				'gpt-5.4-mini',
			),
		);

		return isset( $models[ $provider ] ) ? $models[ $provider ] : array();
	}
}
