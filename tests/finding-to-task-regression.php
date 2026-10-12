<?php
/**
 * Regression tests for preselecting a finding when creating a task.
 * Run: php tests/finding-to-task-regression.php
 */
define('ABSPATH', __DIR__ . '/');
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-admin.php';
$passed=0;$failed=0;
function task_flow_verify($condition,$label){global $passed,$failed;if($condition){$passed++;echo "PASS: {$label}\n";}else{$failed++;echo "FAIL: {$label}\n";}}
$findings=array((object)array('id'=>7),(object)array('id'=>8));
task_flow_verify(AMANAH_SEO_Admin::validated_finding_selection(7,$findings)===7,'valid finding ID is preselected');
task_flow_verify(AMANAH_SEO_Admin::validated_finding_selection('8',$findings)===8,'numeric string finding ID is accepted');
task_flow_verify(AMANAH_SEO_Admin::validated_finding_selection(99,$findings)===0,'unknown or stale finding ID is not selected');
task_flow_verify(AMANAH_SEO_Admin::validated_finding_selection(0,$findings)===0,'zero means no finding selected');
task_flow_verify(AMANAH_SEO_Admin::validated_finding_selection(-7,$findings)===0,'negative finding ID is rejected');
task_flow_verify(AMANAH_SEO_Admin::validated_finding_selection(7,null)===0,'invalid findings collection safely falls back to no selection');
echo "\nRESULT: {$passed} passed; {$failed} failed.\n";
exit($failed>0?1:0);
