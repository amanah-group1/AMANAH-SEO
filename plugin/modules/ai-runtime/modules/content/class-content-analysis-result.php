<?php
/**
 * CORE-006 ContentAnalysisResult output envelope.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Content_Analysis_Result {
	private $analysis;

	public function __construct( AMANAH_SEO_AI_Content_Analysis $analysis ) {
		$this->analysis = $analysis;
	}

	public function to_array() {
		return array(
			'analysis_id'        => $this->analysis->get_analysis_id(),
			'content_reference' => $this->analysis->get_content_reference(),
			'input_context'     => $this->analysis->get_input_context(),
			'analysis'           => $this->analysis->get_analysis(),
			'status'             => $this->analysis->get_status(),
			'version'            => $this->analysis->get_version(),
			'traceability'       => $this->analysis->get_traceability(),
		);
	}
}
