<?php
/**
 * Plugin Name: AMANAH SEO
 * Description: Core workspace for SEO findings, tasks, approvals, and history.
 * Version: 0.2.0
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * Author: Amanah Group
 * License: GPL-2.0-or-later
 * Text Domain: amanah-seo
 */
defined( 'ABSPATH' ) || exit;
define( 'AMANAH_SEO_VERSION', '0.2.0' );
define( 'AMANAH_SEO_PATH', plugin_dir_path( __FILE__ ) );
require_once AMANAH_SEO_PATH . 'includes/class-amanah-seo-store.php';
require_once AMANAH_SEO_PATH . 'includes/class-amanah-seo-activator.php';
require_once AMANAH_SEO_PATH . 'includes/class-amanah-seo-admin.php';
require_once AMANAH_SEO_PATH . 'includes/class-amanah-seo-site-inspector.php';
require_once AMANAH_SEO_PATH . 'includes/class-amanah-seo-content-checks.php';
require_once AMANAH_SEO_PATH . 'includes/class-amanah-seo-modules.php';
require_once AMANAH_SEO_PATH . 'includes/class-amanah-seo-module-navigation.php';
register_activation_hook( __FILE__, array( 'AMANAH_SEO_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'AMANAH_SEO_Modules', 'deactivate_engine' ) );
add_action( 'plugins_loaded', array( 'AMANAH_SEO_Activator', 'maybe_upgrade' ) );
add_action( 'plugins_loaded', array( 'AMANAH_SEO_Admin', 'register' ) );
add_action( 'plugins_loaded', array( 'AMANAH_SEO_Site_Inspector', 'register' ) );
add_action( 'plugins_loaded', array( 'AMANAH_SEO_Content_Checks', 'register' ) );
add_action( 'plugins_loaded', array( 'AMANAH_SEO_Modules', 'boot' ), 5 );
