<?php
/**
 * CORE-006 ContentAnalysis domain model.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Content_Analysis {
	private $analysis_id;
	private $content_reference;
	private $input_context;
	private $analysis;
	private $status;
	private $version;
	private $traceability;

	public function __construct( $analysis_id, array $content_reference, array $input_context, array $analysis, $status, $version, array $traceability = array() ) {
		$this->analysis_id        = (string) $analysis_id;
		$this->content_reference = $content_reference;
		$this->input_context     = $input_context;
		$this->analysis           = $analysis;
		$this->status              = (string) $status;
		$this->version             = absint( $version );
		$this->traceability       = $traceability;
	}

	public function get_analysis_id() { return $this->analysis_id; }
	public function get_content_reference() { return $this->content_reference; }
	public function get_input_context() { return $this->input_context; }
	public function get_analysis() { return $this->analysis; }
	public function get_status() { return $this->status; }
	public function get_version() { return $this->version; }
	public function get_traceability() { return $this->traceability; }

	public function with_result( array $analysis, array $traceability = array() ) {
		return new self( $this->analysis_id, $this->content_reference, $this->input_context, $analysis, 'COMPLETED', $this->version, $traceability );
	}

	public function with_failure( array $traceability = array() ) {
		return new self( $this->analysis_id, $this->content_reference, $this->input_context, array(), 'FAILED', $this->version, $traceability );
	}

	public function to_array() {
		return array(
			'analysis_id'        => $this->analysis_id,
			'content_reference' => $this->content_reference,
			'input_context'     => $this->input_context,
			'analysis'           => $this->analysis,
			'status'             => $this->status,
			'version'            => $this->version,
			'traceability'       => $this->traceability,
		);
	}
}
