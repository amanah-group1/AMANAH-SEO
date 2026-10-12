<?php
/**
 * Regression tests for dashboard summaries and next-action ordering.
 * Run: php tests/dashboard-work-direction-regression.php
 */
define( 'ABSPATH', __DIR__ . '/' );
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-admin.php';

$passed = 0;
$failed = 0;
function dashboard_check( $condition, $name ) {
    global $passed, $failed;
    if ( $condition ) { $passed++; echo "PASS: {$name}\n"; }
    else { $failed++; echo "FAIL: {$name}\n"; }
}
function dashboard_finding( $status, $priority ) {
    return (object) array( 'status' => $status, 'priority' => $priority );
}
function dashboard_task( $status ) {
    return (object) array( 'status' => $status );
}

$findings = array(
    dashboard_finding( 'open', 'urgent' ),
    dashboard_finding( 'in_progress', 'high' ),
    dashboard_finding( 'resolved', 'urgent' ),
    dashboard_finding( 'open', 'medium' ),
);
$tasks = array(
    dashboard_task( 'awaiting_approval' ),
    dashboard_task( 'blocked' ),
    dashboard_task( 'failed' ),
    dashboard_task( 'in_progress' ),
    dashboard_task( 'completed' ),
    dashboard_task( 'cancelled' ),
);
$summary = AMANAH_SEO_Admin::dashboard_summary( $findings, $tasks );
dashboard_check( 3 === $summary['open_findings'], 'dashboard counts only open and in-progress findings' );
dashboard_check( 2 === $summary['priority_findings'], 'dashboard counts only open high/urgent findings as priority findings' );
dashboard_check( 4 === $summary['tasks_active'], 'dashboard excludes completed and cancelled tasks from active total' );
dashboard_check( 1 === $summary['tasks_in_progress'], 'dashboard counts in-progress tasks' );
dashboard_check( 1 === $summary['tasks_waiting_approval'], 'dashboard counts pending-approval tasks by status' );
dashboard_check( 1 === $summary['tasks_blocked'], 'dashboard counts blocked tasks' );
dashboard_check( 1 === $summary['tasks_failed'], 'dashboard counts failed tasks' );
dashboard_check( 1 === $summary['tasks_completed'], 'dashboard counts completed tasks' );
dashboard_check( 'review_approval' === $summary['next_action'], 'approval review takes precedence as next action' );

$summary = AMANAH_SEO_Admin::dashboard_summary(
    array( dashboard_finding( 'open', 'medium' ) ),
    array( dashboard_task( 'blocked' ) )
);
dashboard_check( 'resolve_blockers' === $summary['next_action'], 'blocked tasks direct dashboard to blocker resolution' );

$summary = AMANAH_SEO_Admin::dashboard_summary(
    array( dashboard_finding( 'open', 'high' ) ),
    array()
);
dashboard_check( 'prioritize_finding' === $summary['next_action'], 'high-priority finding is surfaced as next action' );

$summary = AMANAH_SEO_Admin::dashboard_summary(
    array( dashboard_finding( 'open', 'low' ) ),
    array( dashboard_task( 'in_progress' ) )
);
dashboard_check( 'continue_task' === $summary['next_action'], 'in-progress work is next when no approval or blocker is pending' );

$summary = AMANAH_SEO_Admin::dashboard_summary( array(), array() );
dashboard_check( 'scan_website' === $summary['next_action'], 'empty workspace points to a fresh website/content review without inventing metrics' );

echo "\nDashboard regression: {$passed} passed, {$failed} failed.\n";
exit( $failed > 0 ? 1 : 0 );
