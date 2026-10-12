<?php
/**
 * Dependency-free tests for deterministic, read-only content checks.
 * Run: php tests/content-checks-regression.php
 */
define( 'ABSPATH', __DIR__ . '/' );
function add_action( $hook, $callback ) {}
function current_user_can( $capability ) { return true; }
function current_time( $type ) { return '2026-10-12 13:00:00'; }
function wp_strip_all_tags( $value ) { return trim( strip_tags( (string) $value ) ); }
function get_post_type_object( $type ) { return (object) array( 'labels' => (object) array( 'singular_name' => 'post' === $type ? 'Artikel' : 'Halaman' ) ); }
function get_permalink( $post ) { return 'https://example.test/?p=' . $post->ID; }
class WP_Query {
 public static $last_args = array();
 public static $fixture = array();
 public $posts = array();
 public function __construct( $args ) { self::$last_args = $args; $this->posts = self::$fixture; }
}
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-content-checks.php';
$passed = 0;
$failed = 0;
function check( $condition, $label ) {
 global $passed, $failed;
 if ( $condition ) { $passed++; echo "PASS: {$label}\n"; }
 else { $failed++; echo "FAIL: {$label}\n"; }
}
WP_Query::$fixture = array(
 (object) array( 'ID' => 1, 'post_type' => 'post', 'post_title' => '', 'post_content' => '<p> </p>' ),
 (object) array( 'ID' => 2, 'post_type' => 'post', 'post_title' => 'Kopi Liong', 'post_content' => '<p>Isi A</p>' ),
 (object) array( 'ID' => 3, 'post_type' => 'page', 'post_title' => 'kopi liong', 'post_content' => '<p>Isi B</p>' ),
 (object) array( 'ID' => 4, 'post_type' => 'page', 'post_title' => 'Kontak', 'post_content' => '' ),
);
$data = AMANAH_SEO_Content_Checks::scan_data();
check( count( $data['issues'] ) === 5, 'reports only the expected missing-title, empty-content, and duplicate-title issues' );
check( count( array_filter( $data['issues'], function( $issue ) { return 'title_missing' === $issue['code']; } ) ) === 1, 'detects a published item with no title' );
check( count( array_filter( $data['issues'], function( $issue ) { return 'content_empty' === $issue['code']; } ) ) === 2, 'detects empty content after stripping HTML' );
check( count( array_filter( $data['issues'], function( $issue ) { return 'title_duplicate' === $issue['code']; } ) ) === 2, 'detects duplicate titles without case sensitivity' );
check( WP_Query::$last_args['post_status'] === 'publish', 'only scans published records' );
check( WP_Query::$last_args['posts_per_page'] === 100, 'scan is bounded to 100 records' );
check( WP_Query::$last_args['no_found_rows'] === true, 'avoids an unnecessary total-row query' );
check( $data['checked_at'] === '2026-10-12 13:00:00', 'records the scan timestamp' );
check( $data['issues'][0]['url'] === 'https://example.test/?p=1', 'each finding includes its source content URL' );
WP_Query::$fixture = array();
$empty = AMANAH_SEO_Content_Checks::scan_data();
check( 0 === $empty['scanned'] && empty( $empty['issues'] ), 'empty website produces no fabricated findings' );
echo "\nRESULT: {$passed} passed; {$failed} failed.\n";
exit( $failed > 0 ? 1 : 0 );
