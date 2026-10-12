<?php
/**
 * CORE-008 Phase 2 deterministic Schema Type Resolver.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Schema_Type_Resolver {
	private $allowed_types = array(
		'Organization',
		'Person',
		'Article',
		'FAQ',
		'LocalBusiness',
		'Product',
	);

	public function resolve( $context ) {
		if ( ! is_array( $context ) ) {
			return new WP_Error(
				'schema_type_unresolved',
				__( 'Schema type could not be resolved from the supplied context.', 'amanah-seo-ai' ),
				array( 'status' => 422 )
			);
		}

		$explicit = isset( $context['schema_type'] ) ? trim( (string) $context['schema_type'] ) : '';
		if ( '' !== $explicit ) {
			if ( in_array( $explicit, $this->allowed_types, true ) ) {
				return $explicit;
			}

			return new WP_Error(
				'schema_type_unresolved',
				__( 'The supplied Schema type is not supported by the locked Schema taxonomy.', 'amanah-seo-ai' ),
				array( 'status' => 422 )
			);
		}

		$content_type = isset( $context['content_type'] ) ? strtolower( trim( (string) $context['content_type'] ) ) : '';
		if ( 'article' === $content_type ) {
			return 'Article';
		}
		if ( 'faq' === $content_type ) {
			return 'FAQ';
		}

		$entity_type = isset( $context['entity_type'] ) ? strtolower( trim( (string) $context['entity_type'] ) ) : '';
		$map         = array(
			'person'         => 'Person',
			'individual'     => 'Person',
			'organization'   => 'Organization',
			'organisation'  => 'Organization',
			'company'        => 'Organization',
			'corporation'    => 'Organization',
			'nonprofit'      => 'Organization',
			'ngo'            => 'Organization',
			'agency'         => 'Organization',
			'localbusiness'  => 'LocalBusiness',
			'local business' => 'LocalBusiness',
			'product'        => 'Product',
		);

		if ( isset( $map[ $entity_type ] ) ) {
			return $map[ $entity_type ];
		}

		return new WP_Error(
			'schema_type_unresolved',
			__( 'Schema type could not be resolved from the Entity Context.', 'amanah-seo-ai' ),
			array( 'status' => 422 )
		);
	}

	public function get_allowed_types() {
		return $this->allowed_types;
	}
}
