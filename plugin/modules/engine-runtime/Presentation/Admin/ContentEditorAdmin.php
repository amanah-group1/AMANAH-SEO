<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Application\UseCase\UpdateContent;

defined( 'ABSPATH' ) || exit;

final class ContentEditorAdmin {
 private $update_content;

 public function __construct( UpdateContent $update_content ) {
  $this->update_content = $update_content;
 }

 public function render(): void {
  $content_id = absint( $_GET['edit_content_id'] ?? $_GET['content_id'] ?? 0 );
  if ( $content_id < 1 ) {
   wp_die( esc_html__( 'Invalid content.', 'amanah-seo-engine' ) );
  }

  $return_url = add_query_arg(
   array( 'page' => 'amanah-seo-engine-content-production' ),
   admin_url( 'admin.php' )
  );

  $this->renderEditor( $content_id, $return_url );
 }

 public function renderInline( int $content_id, string $return_url ): void {
  $this->renderEditor( $content_id, $return_url );
 }

 public function handleSaveRequest(): void {
  if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['amanah_content_editor_action'] ) ) {
   return;
  }

  if ( ! current_user_can( 'manage_options' ) ) {
   wp_die( esc_html__( 'You are not allowed to edit content.', 'amanah-seo-engine' ) );
  }

  $page = sanitize_key( (string) ( $_GET['page'] ?? '' ) );
  if ( ! in_array( $page, array( 'amanah-seo-engine-content-production', 'amanah-seo-engine-content-editor' ), true ) ) {
   return;
  }

  $content_id = absint( $_GET['edit_content_id'] ?? $_GET['content_id'] ?? 0 );
  if ( $content_id < 1 ) {
   wp_die( esc_html__( 'Invalid content.', 'amanah-seo-engine' ) );
  }

  check_admin_referer( 'amanah_content_editor_save', 'amanah_content_editor_nonce' );

  try {
   $title = sanitize_text_field( wp_unslash( (string) ( $_POST['content_title'] ?? '' ) ) );
   $body = wp_kses_post( wp_unslash( (string) ( $_POST['content_body'] ?? '' ) ) );
   $excerpt = wp_kses_post( wp_unslash( (string) ( $_POST['content_excerpt'] ?? '' ) ) );

   $this->update_content->execute( $content_id, $title, $body, $excerpt );

   $return_url = add_query_arg(
    array(
     'page' => 'amanah-seo-engine-content-production',
     'amanah_content_saved' => '1',
    ),
    admin_url( 'admin.php' )
   );

   wp_safe_redirect( $return_url );
   exit;
  } catch ( \Throwable $e ) {
   add_settings_error( 'amanah_content_editor', 'save_error', $e->getMessage(), 'error' );
  }
 }

 private function renderEditor( int $content_id, string $return_url ): void {
  if ( ! current_user_can( 'manage_options' ) ) {
   wp_die( esc_html__( 'You are not allowed to edit content.', 'amanah-seo-engine' ) );
  }

  $content_repository = \Amanah\SEOEngine\Core\Bootstrap\Plugin::boot()->get( 'content_repository' );
  $content = $content_repository->findById( $content_id );
  if ( null === $content ) {
   wp_die( esc_html__( 'Content not found.', 'amanah-seo-engine' ) );
  }

  echo '<div class="wrap">';
  echo '<h1>Edit Content</h1>';
  settings_errors( 'amanah_content_editor' );

  echo '<form method="post">';
  wp_nonce_field( 'amanah_content_editor_save', 'amanah_content_editor_nonce' );
  echo '<input type="hidden" name="amanah_content_editor_action" value="save" />';
  echo '<table class="form-table" role="presentation">';
  echo '<tr><th scope="row"><label for="content_title">Title</label></th><td><input id="content_title" name="content_title" type="text" class="large-text" value="' . esc_attr( (string) $content->getTitle() ) . '" /></td></tr>';
  echo '<tr><th scope="row"><label for="content_excerpt">Excerpt</label></th><td><textarea id="content_excerpt" name="content_excerpt" rows="4" class="large-text">' . esc_textarea( (string) $content->getExcerpt() ) . '</textarea></td></tr>';
  echo '</table>';
  echo '<h2>Article Content</h2>';
  wp_editor(
   $content->getBody(),
   'amanah_content_body',
   array(
    'textarea_name' => 'content_body',
    'textarea_rows' => 24,
    'media_buttons' => true,
    'teeny' => false,
   )
  );
  echo '<p style="margin-top:16px;">';
  echo '<button type="submit" class="button button-primary">Save &amp; Close</button> ';
  echo '<a class="button" href="' . esc_url( $return_url ) . '">Cancel</a>';
  echo '</p>';
  echo '</form>';
  echo '</div>';
 }
}
