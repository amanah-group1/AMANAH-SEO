<?php
/**
 * Plugin Loader Class
 *
 * Handles WordPress action and filter registration.
 *
 * @package AMANAH_SEO_AI
 */

defined( 'ABSPATH' ) || exit;

/**
 * AMANAH_SEO_AI_Loader
 */
class AMANAH_SEO_AI_Loader {

	/**
	 * Registered actions.
	 *
	 * @var array
	 */
	private $actions = array();

	/**
	 * Registered filters.
	 *
	 * @var array
	 */
	private $filters = array();

	/**
	 * Add an action to the loader.
	 *
	 * @param string   $hook          WordPress hook name.
	 * @param object   $component     Component object.
	 * @param string   $callback      Callback method.
	 * @param int      $priority      Hook priority.
	 * @param int      $accepted_args Accepted arguments.
	 * @return void
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = array( $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a filter to the loader.
	 *
	 * @param string   $hook          WordPress hook name.
	 * @param object   $component     Component object.
	 * @param string   $callback      Callback method.
	 * @param int      $priority      Hook priority.
	 * @param int      $accepted_args Accepted arguments.
	 * @return void
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = array( $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Register all queued hooks with WordPress.
	 *
	 * @return void
	 */
	public function run() {
		foreach ( $this->actions as $hook ) {
			add_action( $hook[0], array( $hook[1], $hook[2] ), $hook[3], $hook[4] );
		}

		foreach ( $this->filters as $hook ) {
			add_filter( $hook[0], array( $hook[1], $hook[2] ), $hook[3], $hook[4] );
		}
	}
}
