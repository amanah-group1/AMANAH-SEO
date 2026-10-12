<?php
/**
 * Transparent, read-only checks against published WordPress content.
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Content_Checks {
 const SCAN_LIMIT = 100;

 public static function register() {
  add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
 }

 public static function menu() {
  if ( ! current_user_can( 'manage_amanah_seo' ) ) {
   return;
  }
  add_submenu_page(
   'amanah-seo',
   'Pemeriksaan Konten',
   'Pemeriksaan Konten',
   'manage_amanah_seo',
   'amanah-seo-content-checks',
   array( __CLASS__, 'page' )
  );
 }

 /**
  * Checks only published posts/pages. This is a bounded, deterministic scan:
  * no SEO score, external metrics, content changes, or automatic persistence.
  */
 public static function scan_data() {
  $query = new WP_Query( array(
   'post_type'              => array( 'post', 'page' ),
   'post_status'            => 'publish',
   'posts_per_page'         => self::SCAN_LIMIT,
   'orderby'                => 'modified',
   'order'                  => 'DESC',
   'no_found_rows'          => true,
   'ignore_sticky_posts'    => true,
   'update_post_meta_cache' => false,
   'update_post_term_cache' => false,
   'fields'                 => 'all',
  ) );

  $posts = is_object( $query ) && is_array( $query->posts ) ? $query->posts : array();
  $title_counts = array();
  foreach ( $posts as $post ) {
   $title = trim( wp_strip_all_tags( (string) $post->post_title ) );
   if ( '' !== $title ) {
    $key = function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );
    $title_counts[ $key ] = isset( $title_counts[ $key ] ) ? $title_counts[ $key ] + 1 : 1;
   }
  }

  $issues = array();
  foreach ( $posts as $post ) {
   $title = trim( wp_strip_all_tags( (string) $post->post_title ) );
   $content = trim( wp_strip_all_tags( (string) $post->post_content ) );
   $key = function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );
   $type_object = get_post_type_object( $post->post_type );
   $type_label = is_object( $type_object ) && isset( $type_object->labels->singular_name ) ? $type_object->labels->singular_name : $post->post_type;
   $url = get_permalink( $post );

   if ( '' === $title ) {
    $issues[] = self::issue( $post, $type_label, $url, 'title_missing', 'Judul kosong', 'Konten terbit tidak memiliki judul yang terbaca.', 'Periksa apakah konten memang perlu memiliki judul yang jelas.' );
   }
   if ( '' === $content ) {
    $issues[] = self::issue( $post, $type_label, $url, 'content_empty', 'Konten kosong', 'Isi konten kosong setelah markup HTML diabaikan.', 'Periksa apakah halaman memang sengaja kosong atau kontennya belum lengkap.' );
   }
   if ( '' !== $title && isset( $title_counts[ $key ] ) && $title_counts[ $key ] > 1 ) {
    $issues[] = self::issue( $post, $type_label, $url, 'title_duplicate', 'Judul sama digunakan lebih dari sekali', 'Judul ini muncul pada lebih dari satu konten terbit dalam cakupan pemindaian.', 'Bandingkan maksud setiap halaman dan ubah judul hanya jika duplikasi memang tidak disengaja.' );
   }
  }

  return array(
   'checked_at' => current_time( 'mysql' ),
   'scanned'    => count( $posts ),
   'limit'      => self::SCAN_LIMIT,
   'truncated'  => count( $posts ) === self::SCAN_LIMIT,
   'issues'     => $issues,
  );
 }

 private static function issue( $post, $type_label, $url, $code, $label, $evidence, $suggestion ) {
  return array(
   'post_id'    => (int) $post->ID,
   'title'      => trim( (string) $post->post_title ) !== '' ? (string) $post->post_title : '(Tanpa judul)',
   'type'       => (string) $type_label,
   'url'        => (string) $url,
   'code'       => $code,
   'label'      => $label,
   'evidence'   => $evidence,
   'suggestion' => $suggestion,
  );
 }

 public static function page() {
  if ( ! current_user_can( 'manage_amanah_seo' ) ) {
   wp_die( esc_html__( 'You are not allowed to access this page.', 'amanah-seo' ), '', array( 'response' => 403 ) );
  }

  $data = self::scan_data();
  echo '<div class="wrap"><h1>Pemeriksaan Konten</h1>';
  if(isset($_GET['amanah_notice'])){
   $notice=sanitize_key(wp_unslash($_GET['amanah_notice']));
   $messages=array('content_issue_imported'=>'Temuan berbasis bukti berhasil ditambahkan ke daftar Temuan.','content_issue_exists'=>'Temuan aktif untuk masalah dan konten ini sudah ada.','content_issue_stale'=>'Hasil pemeriksaan sudah berubah. Jalankan ulang dan periksa hasil terbaru.','error'=>'Temuan tidak dapat dibuat. Periksa kondisi dan coba lagi.');
   if(isset($messages[$notice])) echo '<div class="notice '.('content_issue_imported'===$notice?'notice-success':'notice-info').' is-dismissible"><p>'.esc_html($messages[$notice]).'</p></div>';
  }
  echo '<p>Pemeriksaan deterministik dan baca-saja terhadap artikel/halaman terbit WordPress. Tidak mengubah konten, metadata, tautan, atau status publikasi.</p>';
  echo '<p><strong>Diperiksa:</strong> ' . esc_html( $data['checked_at'] ) . ' &nbsp; <strong>Konten diperiksa:</strong> ' . esc_html( (string) $data['scanned'] ) . ' dari maksimal ' . esc_html( (string) $data['limit'] ) . '</p>';
  if ( $data['truncated'] ) {
   echo '<div class="notice notice-warning inline"><p>Cakupan mencapai batas 100 konten. Hasil hanya mewakili konten yang masuk dalam pemindaian terbaru, bukan seluruh website.</p></div>';
  }
  echo '<h2>Ringkasan</h2><p><strong>Temuan pemeriksaan:</strong> ' . esc_html( (string) count( $data['issues'] ) ) . '</p>';
  if ( 0 === $data['scanned'] ) {
   echo '<p>Belum ada artikel atau halaman terbit untuk diperiksa. Ini berarti tidak ada data dalam cakupan pemindaian, bukan bukti bahwa seluruh website bebas masalah.</p>';
  } elseif ( empty( $data['issues'] ) ) {
   echo '<p>Tidak ada masalah yang terdeteksi oleh tiga pemeriksaan dasar ini. Ini bukan audit SEO lengkap.</p>';
  } else {
   echo '<table class="widefat striped"><thead><tr><th>Temuan</th><th>Konten</th><th>Bukti</th><th>Saran pemeriksaan</th><th>Tindakan</th></tr></thead><tbody>';
   foreach ( $data['issues'] as $issue ) {
    echo '<tr><td><strong>' . esc_html( $issue['label'] ) . '</strong><br><code>' . esc_html( $issue['code'] ) . '</code></td><td><a href="' . esc_url( $issue['url'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $issue['title'] ) . '</a><br>' . esc_html( $issue['type'] ) . ' · ID ' . esc_html( (string) $issue['post_id'] ) . '</td><td>' . esc_html( $issue['evidence'] ) . '</td><td>' . esc_html( $issue['suggestion'] ) . '</td><td><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="amanah_seo_import_content_issue"><input type="hidden" name="post_id" value="' . esc_attr( (string) $issue['post_id'] ) . '"><input type="hidden" name="issue_code" value="' . esc_attr( $issue['code'] ) . '">';
    wp_nonce_field( 'amanah_seo_import_content_issue_' . $issue['post_id'] . '_' . $issue['code'] );
    echo '<button class="button button-primary" type="submit">Jadikan Temuan</button></form></td></tr>';
   }
   echo '</tbody></table>';
  }
  echo '<div class="notice notice-info inline"><p>Pemeriksaan ini hanya menandai judul kosong, isi kosong setelah markup diabaikan, dan judul duplikat dalam cakupan pemindaian. Hasil tidak otomatis disimpan sebagai temuan, tidak menghasilkan skor SEO, dan tidak menggunakan data GSC.</p></div>';
  echo '</div>';
 }
}
