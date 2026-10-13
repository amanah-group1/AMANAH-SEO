<?php
/**
 * CORE-007 Schema response validator.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Schema_Validator {
	private $allowed_types = array( 'Organization', 'Person', 'Article', 'FAQ', 'LocalBusiness', 'Product' );

	public function validate( $schema, $requested_type ) {
		if ( ! is_array( $schema ) || empty( $schema['@context'] ) || empty( $schema['@type'] ) ) {
			return new WP_Error( 'invalid_schema_response', __( 'The generated Schema is invalid.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
		}

		$type           = sanitize_text_field( $schema['@type'] );
		$requested_type = sanitize_text_field( $requested_type );

		if ( ! in_array( $requested_type, $this->allowed_types, true ) || $type !== $requested_type ) {
			return new WP_Error( 'schema_type_mismatch', __( 'The generated Schema type does not match the requested type.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
		}

		if ( 'https://schema.org' !== $schema['@context'] && 'http://schema.org' !== $schema['@context'] ) {
			return new WP_Error( 'invalid_schema_context', __( 'The generated Schema context is invalid.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
		}

		$required = array(
			'Organization'  => array( 'name' ),
			'Person'        => array( 'name' ),
			'FAQ'           => array( 'name' ),
			'LocalBusiness' => array( 'name' ),
			'Product'       => array( 'name' ),
		);

		if ( 'Article' === $requested_type ) {
			$has_identity = ( isset( $schema['headline'] ) && '' !== trim( (string) $schema['headline'] ) )
				|| ( isset( $schema['name'] ) && '' !== trim( (string) $schema['name'] ) );
			if ( ! $has_identity ) {
				return new WP_Error( 'schema_required_field_missing', __( 'The generated Schema is missing a required field.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
			}
		} else {
			foreach ( $required[ $requested_type ] as $field ) {
				if ( ! isset( $schema[ $field ] ) || '' === trim( (string) $schema[ $field ] ) ) {
					return new WP_Error( 'schema_required_field_missing', __( 'The generated Schema is missing a required field.', 'amanah-seo-ai' ), array( 'status' => 502 ) );
				}
			}
		}

		return $schema;
	}
}
