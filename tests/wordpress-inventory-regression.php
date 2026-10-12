<?php
/**
 * Dependency-free tests for the read-only WordPress inventory query.
 * Run: php tests/wordpress-inventory-regression.php
 */
define( 'ABSPATH', __DIR__ . '/' );

function add_action( $hook, $callback ) {}
function current_user_can( $capability ) { return true; }
function wp_count_posts( $type ) {
 if ( 'post' === $type ) {
  return (object) array( 'publish' => 7, 'draft' => 2, 'pending' => 1, 'private' => 1, 'future' => 3, 'trash' => 1 );
 }
 return (object) array( 'publish' => 4, 'draft' => 1, 'pending' => 0, 'private' => 0, 'future' => 0, 'trash' => 0 );
}
function current_time( $type ) { return '2026-10-12 12:00:00'; }

class WP_Query {
 public static $last_args = array();
 public $posts = array();
 public function __construct( $args ) {
  self::$last_args = $args;
  $this->posts = array(
   (object) array( 'ID' => 12, 'post_type' => 'post', 'post_title' => 'Artikel contoh' ),
   (object) array( 'ID' => 13, 'post_type' => 'page', 'post_title' => 'Halaman contoh' ),
  );
 }
}
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-site-inspector.php';

$passed = 0;
$failed = 0;
function check( $condition, $label ) {
 global $passed, $failed;
 if ( $condition ) { $passed++; echo "PASS: {$label}\n"; }
 else { $failed++; echo "FAIL: {$label}\n"; }
}

$data = AMANAH_SEO_Site_Inspector::inventory_data();
check( $data['post_counts']['publish'] === 7, 'published post count comes from WordPress' );
check( $data['post_counts']['draft'] === 2, 'draft post count comes from WordPress' );
check( $data['page_counts']['publish'] === 4, 'published page count comes from WordPress' );
check( count( $data['recent'] ) === 2, 'inventory returns recent published records from query' );
check( $data['checked_at'] === '2026-10-12 12:00:00', 'inventory records its check time' );
check( WP_Query::$last_args['post_status'] === 'publish', 'inventory query is restricted to published content' );
check( WP_Query::$last_args['posts_per_page'] === 20, 'inventory has a bounded result size' );
check( WP_Query::$last_args['orderby'] === 'modified' && WP_Query::$last_args['order'] === 'DESC', 'inventory orders by latest modification' );
check( true === WP_Query::$last_args['no_found_rows'], 'inventory query avoids unnecessary total-row query' );
check( ! isset( WP_Query::$last_args['suppress_filters'] ) && ! isset( WP_Query::$last_args['meta_query'] ), 'inventory does not inject filters or alter metadata' );

echo "\nRESULT: {$passed} passed; {$failed} failed.\n";
exit( $failed > 0 ? 1 : 0 );
