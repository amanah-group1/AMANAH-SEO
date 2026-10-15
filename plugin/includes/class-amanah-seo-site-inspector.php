<?php
/**
 * Read-only WordPress content inventory for AMANAH SEO.
 */
defined( 'ABSPATH' ) || exit;

final class AMANAH_SEO_Site_Inspector {
 public static function register() {
  add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
 }

 public static function menu() {
  if ( ! current_user_can( 'manage_amanah_seo' ) ) {
   return;
  }
  add_submenu_page(
   'amanah-seo',
   'Kondisi Website',
   'Kondisi Website',
   'manage_amanah_seo',
   'amanah-seo-site-condition',
   array( __CLASS__, 'page' )
  );
 }

 /**
  * Read actual WordPress records without changing posts or metadata.
  */
 public static function inventory_data() {
  $post_counts = wp_count_posts( 'post' );
  $page_counts = wp_count_posts( 'page' );
  $query = new WP_Query( array(
   'post_type'              => array( 'post', 'page' ),
   'post_status'            => 'publish',
   'posts_per_page'         => 20,
   'orderby'                => 'modified',
   'order'                  => 'DESC',
   'no_found_rows'          => true,
   'ignore_sticky_posts'    => true,
   'update_post_meta_cache' => false,
   'update_post_term_cache' => false,
  ) );

  return array(
   'post_counts' => self::normalize_counts( $post_counts ),
   'page_counts' => self::normalize_counts( $page_counts ),
   'recent'      => is_array( $query->posts ) ? $query->posts : array(),
   'checked_at'  => current_time( 'mysql' ),
  );
 }

 private static function normalize_counts( $counts ) {
  $result = array( 'publish' => 0, 'draft' => 0, 'pending' => 0, 'private' => 0, 'future' => 0, 'trash' => 0 );
  if ( is_object( $counts ) ) {
   foreach ( $result as $status => $unused ) {
    if ( isset( $counts->$status ) ) {
     $result[ $status ] = max( 0, (int) $counts->$status );
    }
   }
  }
  return $result;
 }

 public static function page() {
  if ( ! current_user_can( 'manage_amanah_seo' ) ) {
   wp_die( esc_html__( 'You are not allowed to access this page.', 'amanah-seo' ), '', array( 'response' => 403 ) );
  }

  $data = self::inventory_data();
  echo '<div class="wrap"><h1>Kondisi Website</h1>';
  echo '<p>Inventaris baca-saja dari data WordPress yang tersedia. Halaman ini tidak mengubah konten, metadata, atau status publikasi.</p>';
  echo '<p><strong>Website:</strong> <code>' . esc_html( home_url( '/' ) ) . '</code> &nbsp; <strong>Diperiksa:</strong> ' . esc_html( $data['checked_at'] ) . '</p>';

  echo '<h2>Ringkasan konten</h2><table class="widefat striped"><thead><tr><th>Jenis</th><th>Terbit</th><th>Draf</th><th>Menunggu</th><th>Privat</th><th>Terjadwal</th><th>Tempat sampah</th></tr></thead><tbody>';
  self::render_count_row( 'Artikel', $data['post_counts'] );
  self::render_count_row( 'Halaman', $data['page_counts'] );
  echo '</tbody></table>';

  echo '<h2>Konten terbit terbaru</h2>';
  if ( empty( $data['recent'] ) ) {
   echo '<p>Belum ada artikel atau halaman terbit yang dapat ditampilkan.</p>';
  } else {
   echo '<table class="widefat striped"><thead><tr><th>Judul</th><th>Jenis</th><th>Terakhir diubah</th><th>URL publik</th></tr></thead><tbody>';
   foreach ( $data['recent'] as $post ) {
    $title = trim( (string) get_the_title( $post ) );
    if ( '' === $title ) {
     $title = '(Tanpa judul)';
    }
    $type_object = get_post_type_object( $post->post_type );
    $type_label = is_object( $type_object ) && isset( $type_object->labels->singular_name ) ? $type_object->labels->singular_name : $post->post_type;
    $url = get_permalink( $post );
    echo '<tr><td>' . esc_html( $title ) . '</td><td>' . esc_html( $type_label ) . '</td><td>' . esc_html( get_post_modified_time( 'Y-m-d H:i:s', false, $post ) ) . '</td><td><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $url ) . '</a></td></tr>';
   }
   echo '</tbody></table>';
  }

  echo '<div class="notice notice-info inline"><p>Ini adalah inventaris WordPress, bukan audit SEO lengkap. Metrik Google Search Console, skor SEO, dan temuan otomatis tidak ditampilkan sebelum sumber serta metode pengukurannya tersedia dan diverifikasi.</p></div>';
  echo '</div>';
 }

 private static function render_count_row( $label, $counts ) {
  echo '<tr><th>' . esc_html( $label ) . '</th>';
  foreach ( array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ) as $status ) {
   echo '<td>' . esc_html( (string) $counts[ $status ] ) . '</td>';
  }
  echo '</tr>';
 }
}
