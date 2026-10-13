<?php
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Generation_Requirements_Builder {
	public function build( array $analysis ) {
		$requirements = array(
			'must_improve'=>array(),'must_cover'=>array(),'must_answer'=>array(),
			'must_preserve'=>array(),'must_avoid'=>array(),'recommended_direction'=>array(),
		);
		$findings = isset( $analysis['findings'] ) && is_array( $analysis['findings'] ) ? $analysis['findings'] : array();
		foreach ( $findings as $finding ) {
			if ( ! is_array( $finding ) || empty( $finding['statement'] ) ) { continue; }
			$type = isset( $finding['requirement_type'] ) && in_array( $finding['requirement_type'], AMANAH_SEO_AI_Generation_Requirements::TYPES, true ) ? $finding['requirement_type'] : 'must_improve';
			$requirements[ $type ][] = $this->item( $finding, $type );
		}
		$this->legacy_list( $analysis, 'weaknesses', 'must_improve', $requirements );
		$this->legacy_list( $analysis, 'coverage_gaps', 'must_cover', $requirements );
		$this->legacy_list( $analysis, 'faq_suggestions', 'must_answer', $requirements );
		$this->legacy_list( $analysis, 'strengths', 'must_preserve', $requirements );
		$this->legacy_list( $analysis, 'must_avoid', 'must_avoid', $requirements );
		$this->legacy_list( $analysis, 'recommendations', 'recommended_direction', $requirements );
		return AMANAH_SEO_AI_Generation_Requirements::normalize( $requirements );
	}
	private function legacy_list( array $analysis, $field, $type, array &$requirements ) {
		if ( empty( $analysis[ $field ] ) || ! is_array( $analysis[ $field ] ) ) { return; }
		foreach ( $analysis[ $field ] as $i => $value ) {
			if ( is_array( $value ) ) { $statement = isset( $value['statement'] ) ? $value['statement'] : ''; } else { $statement = (string) $value; }
			if ( '' === trim( $statement ) ) { continue; }
			$requirements[ $type ][] = array(
				'requirement_id' => sanitize_key( 'gr-' . $type . '-' . ( $i + 1 ) ),
				'source_finding' => sanitize_key( $field ) . '-' . ( $i + 1 ),
				'quality_dimension' => 'UNKNOWN/UNASSESSED',
				'requirement_type' => $type,
				'severity' => 'UNKNOWN/UNASSESSED',
				'statement' => $statement,
				'rationale' => 'Derived from existing CORE-006 analysis output; dimension/severity remain unassessed when evidence is insufficient.',
				'disposition' => in_array( $type, array( 'must_improve','must_cover','must_answer','must_avoid' ), true ) ? 'mandatory' : 'recommended',
			);
		}
	}
	private function item( array $finding, $type ) {
		return array(
			'requirement_id' => isset( $finding['finding_id'] ) ? $finding['finding_id'] : wp_generate_uuid4(),
			'source_finding' => isset( $finding['finding_id'] ) ? $finding['finding_id'] : '',
			'quality_dimension' => isset( $finding['quality_dimension'] ) ? $finding['quality_dimension'] : 'UNKNOWN/UNASSESSED',
			'requirement_type' => $type,
			'severity' => isset( $finding['severity'] ) ? $finding['severity'] : 'UNKNOWN/UNASSESSED',
			'statement' => $finding['statement'],
			'rationale' => isset( $finding['evidence'] ) ? $finding['evidence'] : '',
			'disposition' => isset( $finding['disposition'] ) ? $finding['disposition'] : 'recommended',
		);
	}
}
