<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_AI_Safety_Validator {
	public function validate( $task, $decoded ) {
		if ( ! is_array( $decoded ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'AI response format is invalid.', 'validation', 400, 'response_contract_not_object' );
		}

		$required = array(
			'entity_analysis'     => array( 'summary', 'expertise', 'schema', 'recommendation' ),
			'content_analysis'    => array( 'analysis' ),
			'content_generation'  => array( 'title', 'outline', 'article', 'faq', 'schema', 'article_package' ),
			'content_generation_quality' => array( 'quality_evaluation' ),
			'content_refinement' => array( 'article_package' ),
			'faq_generation'      => array( 'faq', 'schema' ),
			'schema_generation'  => array( 'schema' ),
			'audit_recommendation' => array( 'priority', 'recommendation', 'action' ),
		);

		if ( empty( $required[ $task ] ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'Unsupported AI task.', 'validation', 400, 'unsupported_task' );
		}

		foreach ( $required[ $task ] as $key ) {
			if ( ! array_key_exists( $key, $decoded ) ) {
				throw new AMANAH_SEO_AI_AI_Exception( 'AI response is missing a required field.', 'validation', 400, 'response_contract_missing_field' );
			}
		}

		if ( 'content_analysis' === $task ) {
			$this->validate_content_analysis( $decoded['analysis'] );
		}

		return $decoded;
	}

	private function validate_content_analysis( $analysis ) {
		if ( ! is_array( $analysis ) || empty( $analysis ) || $this->contains_object_like_values( $analysis ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'AI content analysis is not a valid structured object.', 'validation', 400, 'response_contract_invalid_analysis' );
		}

		$required = array( 'score', 'weaknesses', 'recommendations', 'faq_suggestions' );
		foreach ( $required as $key ) {
			if ( ! array_key_exists( $key, $analysis ) ) {
				throw new AMANAH_SEO_AI_AI_Exception( sprintf( 'AI content analysis is missing the required field: %s.', $key ), 'validation', 400, 'response_contract_missing_analysis_field' );
			}
		}

		$unexpected = array_diff( array_keys( $analysis ), $required );
		if ( ! empty( $unexpected ) ) {
			throw new AMANAH_SEO_AI_AI_Exception( sprintf( 'AI content analysis contains unsupported fields: %s.', implode( ', ', array_map( 'sanitize_key', $unexpected ) ) ), 'validation', 400, 'response_contract_unsupported_analysis_field' );
		}

		if ( ! is_int( $analysis['score'] ) || $analysis['score'] < 0 || $analysis['score'] > 100 ) {
			throw new AMANAH_SEO_AI_AI_Exception( 'AI content analysis score is invalid.', 'validation', 400, 'response_contract_invalid_score' );
		}

		foreach ( array( 'weaknesses', 'recommendations', 'faq_suggestions' ) as $key ) {
			if ( ! is_array( $analysis[ $key ] ) || $this->contains_object_like_values( $analysis[ $key ] ) ) {
				throw new AMANAH_SEO_AI_AI_Exception( 'AI content analysis list is invalid.', 'validation', 400, 'response_contract_invalid_analysis_list' );
			}
		}
	}

	private function contains_object_like_values( $value ) {
		if ( is_object( $value ) || is_resource( $value ) ) {
			return true;
		}
		if ( is_array( $value ) ) {
			foreach ( $value as $item ) {
				if ( $this->contains_object_like_values( $item ) ) {
					return true;
				}
			}
		}
		return false;
	}
}
