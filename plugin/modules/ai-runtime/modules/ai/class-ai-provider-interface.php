<?php
defined( 'ABSPATH' ) || exit;
interface AMANAH_SEO_AI_AI_Provider_Interface {
	public function get_name();
	public function generate( AMANAH_SEO_AI_AI_Request $request );
}
