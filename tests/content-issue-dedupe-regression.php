<?php
/**
 * Regression tests for content-issue identity and duplicate prevention.
 * Run: php tests/content-issue-dedupe-regression.php
 */
define('ABSPATH', __DIR__ . '/');
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-admin.php';

$passed = 0;
$failed = 0;
function verify($condition, $label) {
 global $passed, $failed;
 if ($condition) { $passed++; echo "PASS: {$label}\n"; }
 else { $failed++; echo "FAIL: {$label}\n"; }
}
$title = '[title_duplicate] AMANAH Tes';
$base = (object) array(
 'source' => 'wordpress',
 'title' => $title,
 'status' => 'open',
 'evidence' => 'Pemeriksaan: title_duplicate; konten ID 13; URL https://example.test/?p=13; bukti: duplicate title.',
);
verify(AMANAH_SEO_Admin::is_duplicate_content_issue($base, $title, 13, 'title_duplicate'), 'same issue code and post is recognized as an active duplicate');
verify(!AMANAH_SEO_Admin::is_duplicate_content_issue($base, $title, 16, 'title_duplicate'), 'same title on a different post remains a separate finding');
verify(!AMANAH_SEO_Admin::is_duplicate_content_issue($base, $title, 13, 'content_empty'), 'different issue code on the same post remains a separate finding');
$resolved = clone $base;
$resolved->status = 'resolved';
verify(!AMANAH_SEO_Admin::is_duplicate_content_issue($resolved, $title, 13, 'title_duplicate'), 'resolved findings do not block a new active finding');
$other_source = clone $base;
$other_source->source = 'manual';
verify(!AMANAH_SEO_Admin::is_duplicate_content_issue($other_source, $title, 13, 'title_duplicate'), 'manual findings do not block a WordPress content issue');
$legacy = clone $base;
$legacy->evidence = 'Older WordPress finding without a stable issue identity.';
verify(!AMANAH_SEO_Admin::is_duplicate_content_issue($legacy, $title, 13, 'title_duplicate'), 'unidentified legacy findings do not suppress distinct evidence-backed issues');
echo "\nRESULT: {$passed} passed; {$failed} failed.\n";
exit($failed > 0 ? 1 : 0);
