<?php
/** Regression checks for finding-to-task navigation. Run: php tests/finding-task-navigation-regression.php */
define('ABSPATH', __DIR__ . '/');
$admin = file_get_contents(__DIR__ . '/../plugin/includes/class-amanah-seo-admin.php');
$checks = array(
 'finding row links to task creation' => strpos($admin, 'Buat pekerjaan dari temuan ini') !== false,
 'link carries finding ID in query' => strpos($admin, "'finding_id'=>(int)$x->id") !== false,
 'task page reads requested finding ID' => strpos($admin, "isset($_GET['finding_id'])?absint($_GET['finding_id']):0") !== false,
 'selection validates against listed findings' => strpos($admin, '(int)$available_finding->id===$requested_finding_id') !== false,
 'task select reflects selected finding' => strpos($admin, 'selected($selected_finding_id,(int)$f->id,false)') !== false,
 'task remains explicit user submission' => strpos($admin, 'value="amanah_seo_create_task"') !== false,
);
$passed=0;$failed=0;
foreach($checks as $label=>$ok){echo ($ok?'PASS: ':'FAIL: ').$label."\n";if($ok)$passed++;else$failed++;}
echo "\nRESULT: {$passed} passed; {$failed} failed.\n";
exit($failed?1:0);
