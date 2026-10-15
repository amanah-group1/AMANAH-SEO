<?php
/**
 * CORE-006 persistence contract.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

interface AMANAH_SEO_AI_Content_Analysis_Repository_Interface {
	public function next_version( $post_id );
	public function save( AMANAH_SEO_AI_Content_Analysis $analysis );
	public function update( AMANAH_SEO_AI_Content_Analysis $analysis );
	public function find_by_id( $analysis_id );
	public function find_latest_by_content( $post_id );
}
