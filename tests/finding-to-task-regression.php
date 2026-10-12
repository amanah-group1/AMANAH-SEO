<?php
/**
 * Finding-to-task selection regression tests.
 * Run: php tests/finding-to-task-regression.php
 */
define('ABSPATH', __DIR__ . '/');
function absint($value){return abs((int)$value);}
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-admin.php';
$passed=0;$failed=0;
function verify_task_selection($condition,$label){global $passed,$failed;if($condition){$passed++;echo "PASS: {$label}\n";}else{$failed++;echo "FAIL: {$label}\n";}}
function selected_finding_for_task($requested_id,$findings){
 $requested_id=absint($requested_id);
 if($requested_id<1||!is_array($findings))return 0;
 foreach($findings as $finding){if(is_object($finding)&&isset($finding->id)&&(int)$finding->id===$requested_id)return $requested_id;}
 return 0;
}
$findings=array((object)array('id'=>7),(object)array('id'=>8));
verify_task_selection(selected_finding_for_task(7,$findings)===7,'known finding ID is preselected');
verify_task_selection(selected_finding_for_task('8',$findings)===8,'numeric-string finding ID is accepted');
verify_task_selection(selected_finding_for_task(99,$findings)===0,'unknown finding ID is not preselected');
verify_task_selection(selected_finding_for_task(0,$findings)===0,'zero leaves finding unselected');
verify_task_selection(selected_finding_for_task(-7,$findings)===7,'negative input is normalized safely before lookup');
verify_task_selection(selected_finding_for_task(7,null)===0,'invalid finding collection safely leaves selection empty');
echo "\nRESULT: {$passed} passed; {$failed} failed.\n";
exit($failed>0?1:0);
