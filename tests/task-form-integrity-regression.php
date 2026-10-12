<?php
/**
 * Regression tests for task form validation and malformed-title presentation.
 * Run: php tests/task-form-integrity-regression.php
 */
define('ABSPATH', __DIR__ . '/');
if (!class_exists('WP_Error')) {
    class WP_Error {
        private $code;
        public function __construct($code, $message = '') { $this->code = $code; }
        public function get_error_code() { return $this->code; }
    }
}
if (!function_exists('wp_unslash')) { function wp_unslash($value) { return $value; } }
if (!function_exists('sanitize_text_field')) { function sanitize_text_field($value) { return trim(strip_tags((string)$value)); } }
if (!function_exists('sanitize_textarea_field')) { function sanitize_textarea_field($value) { return trim(strip_tags((string)$value)); } }
if (!function_exists('esc_html')) { function esc_html($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); } }
if (!function_exists('absint')) { function absint($value) { return abs((int)$value); } }
require_once __DIR__ . '/../plugin/includes/class-amanah-seo-admin.php';

$passed = 0;
$failed = 0;
function task_integrity_check($condition, $label) {
    global $passed, $failed;
    if ($condition) { $passed++; echo "PASS: {$label}\n"; }
    else { $failed++; echo "FAIL: {$label}\n"; }
}
$valid = array(
    'title' => '  Periksa judul halaman  ',
    'description' => ' Tinjau bukti dan catat hasil. ',
    'owner' => ' Tim konten ',
    'success_criteria' => ' Hasil pemeriksaan tercatat. ',
);
$normalized = AMANAH_SEO_Admin::normalize_task_input($valid);
task_integrity_check(is_array($normalized) && 'Periksa judul halaman' === $normalized['title'], 'valid task fields are trimmed and accepted');
task_integrity_check(is_array($normalized) && 'Tinjau bukti dan catat hasil.' === $normalized['description'], 'description is normalized');
task_integrity_check(is_wp_error(AMANAH_SEO_Admin::normalize_task_input(array('title'=>'', 'description'=>'x', 'success_criteria'=>'y'))), 'required task title cannot be empty');
$too_long = $valid; $too_long['title'] = str_repeat('x', 256);
$result = AMANAH_SEO_Admin::normalize_task_input($too_long);
task_integrity_check(is_wp_error($result) && 'task_field_too_long' === $result->get_error_code(), 'title exceeding 255 characters is rejected');
$too_long = $valid; $too_long['owner'] = str_repeat('x', 191);
$result = AMANAH_SEO_Admin::normalize_task_input($too_long);
task_integrity_check(is_wp_error($result) && 'task_field_too_long' === $result->get_error_code(), 'owner exceeding 190 characters is rejected');
$too_long = $valid; $too_long['description'] = str_repeat('x', 5001);
$result = AMANAH_SEO_Admin::normalize_task_input($too_long);
task_integrity_check(is_wp_error($result) && 'task_field_too_long' === $result->get_error_code(), 'description exceeding 5000 characters is rejected');
$too_long = $valid; $too_long['success_criteria'] = str_repeat('x', 2001);
$result = AMANAH_SEO_Admin::normalize_task_input($too_long);
task_integrity_check(is_wp_error($result) && 'task_field_too_long' === $result->get_error_code(), 'success criteria exceeding 2000 characters is rejected');
$corrupt = $valid;
$corrupt['title'] = 'Pekerjaan Temuan Pemilik Kriteria selesai Tenggat Dependensi Status / persetujuan Ubah status / persetujuan 24';
$result = AMANAH_SEO_Admin::normalize_task_input($corrupt);
task_integrity_check(is_wp_error($result) && 'suspicious_task_title' === $result->get_error_code(), 'form-header text is rejected as a task title');
task_integrity_check(!AMANAH_SEO_Admin::task_title_needs_review('Periksa konten untuk Post ID 18'), 'ordinary task title is not flagged');
$cell = AMANAH_SEO_Admin::task_title_cell($corrupt['title'], 26);
task_integrity_check(false !== strpos($cell, 'Judul perlu diperiksa — pekerjaan #26') && false === strpos($cell, $corrupt['title']), 'legacy malformed title is flagged without echoing the corrupt text');
$escaped = AMANAH_SEO_Admin::task_title_cell('<script>alert(1)</script>', 27);
task_integrity_check(false === strpos($escaped, '<script>') && false !== strpos($escaped, '&lt;script&gt;'), 'task title presentation escapes stored content');

echo "\nTask integrity regression: {$passed} passed, {$failed} failed.\n";
exit($failed > 0 ? 1 : 0);
