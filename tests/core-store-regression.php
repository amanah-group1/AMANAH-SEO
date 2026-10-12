<?php
/**
 * Dependency-free regression tests for AMANAH SEO Core data rules.
 * Run: php tests/core-store-regression.php
 */
define( 'ABSPATH', __DIR__ . '/' );

function current_time( $type ) { return '2026-10-12 12:00:00'; }
function get_current_user_id() { return 7; }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function sanitize_textarea_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function absint( $value ) { return abs( (int) $value ); }
function is_wp_error( $value ) { return $value instanceof WP_Error; }

class WP_Error {
    private $code;
    private $message;
    public function __construct( $code, $message = '' ) { $this->code = $code; $this->message = $message; }
    public function get_error_code() { return $this->code; }
    public function get_error_message() { return $this->message; }
}

class AMANAH_SEO_Test_DB {
    public $prefix = 'wp_';
    public $insert_id = 100;
    public $inserts = array();
    public $updates = array();
    public $row = null;
    public function insert( $table, $data, $formats = null ) {
        $this->insert_id++;
        $this->inserts[] = array( 'table' => $table, 'data' => $data, 'formats' => $formats );
        return 1;
    }
    public function prepare( $query, ...$args ) { return $query; }
    public function get_row( $query ) { return $this->row; }
    public function update( $table, $data, $where, $formats = null, $where_formats = null ) {
        $this->updates[] = array( 'table' => $table, 'data' => $data, 'where' => $where, 'formats' => $formats, 'where_formats' => $where_formats );
        if ( isset( $data['approval_status'] ) && is_object( $this->row ) ) {
            $this->row->approval_status = $data['approval_status'];
        }
        return 1;
    }
    public function get_results( $query ) { return array(); }
}
$wpdb = new AMANAH_SEO_Test_DB();
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-store.php';

$passed = 0;
$failed = 0;
function check( $condition, $name ) {
    global $passed, $failed;
    if ( $condition ) { $passed++; echo "PASS: {$name}\n"; }
    else { $failed++; echo "FAIL: {$name}\n"; }
}

$task = array(
    'finding_id' => 12,
    'title' => 'Periksa title halaman',
    'description' => 'Pastikan title relevan.',
    'owner' => 'Admin',
    'success_criteria' => 'Title diperiksa dan hasil dicatat.',
    'status' => 'not_started',
);
$id = AMANAH_SEO_Store::create_task( $task );
$task_insert = $wpdb->inserts[0];
check( is_int( $id ), 'task can be created with valid input' );
check( count( $task_insert['data'] ) === 11, 'task insert has 11 stored fields' );
check( count( $task_insert['formats'] ) === 11, 'task insert format count matches stored fields' );
check( $task_insert['formats'][7] === '%s' && $task_insert['formats'][8] === '%d', 'completion evidence is formatted as string and creator as integer' );
check( $task_insert['data']['approval_status'] === 'not_required', 'ordinary task does not require approval by default' );

$completed = $task;
$completed['status'] = 'completed';
$result = AMANAH_SEO_Store::create_task( $completed );
check( is_wp_error( $result ) && $result->get_error_code() === 'completion_evidence_required', 'task cannot be created as completed without evidence' );

$wpdb->row = (object) array( 'status' => 'in_progress', 'approval_status' => 'not_required' );
$result = AMANAH_SEO_Store::update_task_status( 101, 'completed', '' );
check( is_wp_error( $result ) && $result->get_error_code() === 'completion_evidence_required', 'completion requires evidence' );

$wpdb->row = (object) array( 'status' => 'awaiting_approval', 'approval_status' => 'pending' );
$result = AMANAH_SEO_Store::update_task_status( 101, 'completed', 'Hasil diverifikasi.' );
check( is_wp_error( $result ) && $result->get_error_code() === 'approval_pending', 'pending approval blocks completion' );

$result = AMANAH_SEO_Store::update_task_status( 101, 'unknown_status', 'Evidence' );
check( is_wp_error( $result ) && $result->get_error_code() === 'invalid_task_status', 'invalid task status is rejected' );

$wpdb->row = (object) array( 'id' => 101, 'approval_status' => 'pending' );
$result = AMANAH_SEO_Store::decide_approval( 101, 'approved', 'Ditinjau.' );
check( true === $result, 'authorized approval decision can be recorded by the data layer' );
check( $wpdb->row->approval_status === 'approved', 'approval decision updates pending state' );

$wpdb->row = (object) array( 'id' => 101, 'approval_status' => 'not_required' );
$result = AMANAH_SEO_Store::decide_approval( 101, 'approved', '' );
check( is_wp_error( $result ) && $result->get_error_code() === 'approval_not_pending', 'approval cannot be decided when no request is pending' );

$result = AMANAH_SEO_Store::update_finding_status( 12, 'resolved', '' );
check( is_wp_error( $result ) && $result->get_error_code() === 'finding_note_required', 'resolved finding requires a result note' );

$result = AMANAH_SEO_Store::update_finding_status( 12, 'invented', 'Note' );
check( is_wp_error( $result ) && $result->get_error_code() === 'invalid_finding_status', 'invalid finding status is rejected' );

$wpdb->row = (object) array( 'id' => 12, 'status' => 'open' );
$result = AMANAH_SEO_Store::update_finding_status( 12, 'resolved', 'Pemeriksaan selesai dan hasil dicatat.' );
$last_update = end( $wpdb->updates );
check( true === $result, 'finding status can be updated with a result note' );
check( $last_update['data']['status'] === 'resolved', 'finding status update persists selected status' );

echo "\nRESULT: {$passed} passed; {$failed} failed.\n";
exit( $failed > 0 ? 1 : 0 );
