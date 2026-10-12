<?php
/**
 * Finding-to-task selection regression tests.
 * Run: php tests/finding-to-task-regression.php
 */
define('ABSPATH', __DIR__ . '/');
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-admin.php';
$passed=0;$failed=0;
function verify_task_selection($condition,$label){global $passed,$failed;if($condition){$passed++;echo "PASS: {$label}\n";}else{$failed++;echo "FAIL: {$label}\n";}}
$findings=array((object)array('id'=>7),(object)array('id'=>8));
verify_task_selection(AMANAH_SEO_Admin::validated_finding_selection(7,$findings)===7,'known finding ID is preselected');
verify_task_selection(AMANAH_SEO_Admin::validated_finding_selection('8',$findings)===8,'numeric-string finding ID is accepted');
verify_task_selection(AMANAH_SEO_Admin::validated_finding_selection(99,$findings)===0,'unknown finding ID is not preselected');
verify_task_selection(AMANAH_SEO_Admin::validated_finding_selection(0,$findings)===0,'zero leaves finding unselected');
verify_task_selection(AMANAH_SEO_Admin::validated_finding_selection(-7,$findings)===0,'negative finding ID is rejected');
verify_task_selection(AMANAH_SEO_Admin::validated_finding_selection(7,null)===0,'invalid finding collection safely leaves selection empty');
echo "\nRESULT: {$passed} passed; {$failed} failed.\n";
exit($failed>0?1:0);
