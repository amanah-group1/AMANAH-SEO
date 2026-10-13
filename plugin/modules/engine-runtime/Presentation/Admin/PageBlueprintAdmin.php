<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Presentation\Admin;

use Amanah\SEOEngine\Application\Repository\ContentBriefRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\PageBlueprintRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\ContentRepositoryInterface;
use Amanah\SEOEngine\Application\Repository\QualityResultRepositoryInterface;
use Amanah\SEOEngine\Application\UseCase\GeneratePageBlueprint;
use Amanah\SEOEngine\Application\UseCase\SaveContentOutline;
use Amanah\SEOEngine\Application\UseCase\GenerateContent;
use Amanah\SEOEngine\Application\UseCase\CreateManualContent;
use Amanah\SEOEngine\Application\UseCase\EvaluateContentQuality;
use Amanah\SEOEngine\Application\UseCase\ReviewContent;
use Amanah\SEOEngine\Application\UseCase\PublishContent;
use Amanah\SEOEngine\Application\UseCase\SyncWordPressContent;

defined( 'ABSPATH' ) || exit;

final class PageBlueprintAdmin {

 private $generate;
 private $briefs;
 private $blueprints;
 private $generate_content;
 private $create_manual_content;
 private $contents;
 private $quality_results;
 private $evaluate_quality;
 private $review_content;
 private $publish_content;
 private $sync_wordpress_content;
 private $content_editor_admin;
 private $save_content_outline;

 public function __construct(
  GeneratePageBlueprint $generate,
  ContentBriefRepositoryInterface $briefs,
  PageBlueprintRepositoryInterface $blueprints,
  GenerateContent $generate_content,
  ContentRepositoryInterface $contents,
  ?CreateManualContent $create_manual_content = null,
  ?QualityResultRepositoryInterface $quality_results = null,
  ?EvaluateContentQuality $evaluate_quality = null,
  ?ReviewContent $review_content = null,
  ?PublishContent $publish_content = null,
  ?SyncWordPressContent $sync_wordpress_content = null,
  ?ContentEditorAdmin $content_editor_admin = null,
  ?SaveContentOutline $save_content_outline = null
 ) {
  $this->generate = $generate;
  $this->briefs = $briefs;
  $this->blueprints = $blueprints;
  $this->generate_content = $generate_content;
  $this->contents = $contents;
  $this->create_manual_content = $create_manual_content;
  $this->quality_results = $quality_results;
  $this->evaluate_quality = $evaluate_quality;
  $this->review_content = $review_content;
  $this->publish_content = $publish_content;
  $this->sync_wordpress_content = $sync_wordpress_content;
  $this->content_editor_admin = $content_editor_admin;
  $this->save_content_outline = $save_content_outline;
 }

 public function render(): void {
  if ( ! current_user_can( 'manage_options' ) ) {
   wp_die( esc_html__( 'You are not allowed to manage Page Blueprints.', 'amanah-seo-engine' ) );
  }

  $briefs = $this->briefs->findAll();
  $items = array();
  foreach ( $briefs as $brief ) {
   $items[ $brief->getKeywordId() ] = $brief;
  }

  echo '<div class="wrap"><h1>Content Production</h1>';
  settings_errors( 'amanah_page_blueprint' );
  echo '<p>Production follows the controlled workflow: Blueprint → Generation → Quality → Manual Review → Approval → Publish.</p>';
  echo '<div id="amanah-production-busy" style="display:none;position:fixed;inset:0;background:rgba(255,255,255,.72);z-index:99999;align-items:center;justify-content:center;">';
  echo '<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:24px 28px;box-shadow:0 8px 30px rgba(0,0,0,.12);text-align:center;min-width:300px;">';
  echo '<div class="amanah-production-spinner" aria-hidden="true"></div>';
  echo '<strong id="amanah-production-busy-title" style="display:block;font-size:16px;margin-top:12px;">Sedang memproses...</strong>';
  echo '<p id="amanah-production-busy-text" style="margin:8px 0 0;color:#50575e;">Mohon tunggu. Jangan klik tombol lain selama proses berjalan.</p>';
  echo '</div></div>';
  echo '<style>.amanah-production-spinner{width:34px;height:34px;margin:0 auto;border:4px solid #dcdcde;border-top-color:#2271b1;border-radius:50%;animation:amanahProductionSpin .8s linear infinite}@keyframes amanahProductionSpin{to{transform:rotate(360deg)}}body.amanah-production-busy #wpcontent{pointer-events:none}</style>';
  echo '<table class="widefat striped"><thead><tr><th>Keyword</th><th>Brief</th><th>Blueprint</th><th>Content</th><th>Quality</th><th>Review</th><th>Action</th></tr></thead><tbody>';

  if ( empty( $items ) ) {
   echo '<tr><td colspan="7">No Content Brief records yet.</td></tr>';
  }

  foreach ( $items as $keyword_id => $brief ) {
   $d = $brief->getData();
   $keyword = isset( $d['keyword']['phrase'] ) ? $d['keyword']['phrase'] : ( isset( $d['keyword']['keyword'] ) ? $d['keyword']['keyword'] : 'Keyword #' . $keyword_id );
   $bp = $this->blueprints->findByKeywordId( (int) $keyword_id );
   $content = null !== $bp ? $this->contents->findLatestByBlueprintId( (int) $bp->getId() ) : null;
   $quality = null !== $content && null !== $this->quality_results ? $this->quality_results->findLatestByContentId( (int) $content->getId() ) : null;

   echo '<tr>';
   echo '<td>' . esc_html( (string) $keyword ) . '</td>';
   echo '<td>' . esc_html( $brief->getStatus() ) . '</td>';
   echo '<td>' . esc_html( null !== $bp ? $bp->getStatus() : 'Not created' ) . '</td>';
   echo '<td>' . esc_html( null !== $content ? $content->getStatus() : 'Not generated' ) . '</td>';
   $quality_is_current = null !== $quality && $quality->getContentVersion() === $content->getVersion();
   echo '<td>' . esc_html(
    null === $quality
     ? 'Not evaluated'
     : ( $quality_is_current
      ? strtoupper( $quality->getStatus() ) . ( null !== $quality->getScore() ? ' · ' . $quality->getScore() : '' )
      : 'REVALIDATION REQUIRED' )
   ) . '</td>';
   echo '<td>' . esc_html( null !== $content ? strtoupper( str_replace( '_', ' ', $content->getStatus() ) ) : '—' ) . '</td>';
   echo '<td>';

   if ( null !== $bp ) {
    $this->renderActionForm( 'generate_content', (int) $keyword_id, 0, 'Generate Content', 'amanah_generate_content' );
   }

   $this->renderActionForm( 'generate', (int) $keyword_id, 0, null !== $bp ? 'Refresh Blueprint' : 'Create Blueprint', 'amanah_generate_page_blueprint' );

   if ( null !== $content && null !== $content->getWordPressPostId() && null !== $this->sync_wordpress_content ) {
    $this->renderActionForm( 'sync_wordpress_content', 0, (int) $content->getId(), 'Sync from WordPress', 'amanah_sync_wordpress_content' );
   }

   if ( null !== $content && null !== $content->getWordPressPostId() ) {
    $edit_url = add_query_arg(
     array(
      'post' => (int) $content->getWordPressPostId(),
      'action' => 'edit',
      'amanah_editor' => '1',
     ),
     admin_url( 'post.php' )
    );
    echo '<a class="button" target="_blank" rel="noopener noreferrer" style="display:inline-block;margin:2px 4px 2px 0;" href="' . esc_url( $edit_url ) . '">Review / Edit Artikel</a>';
   }

   if ( null !== $content && null !== $this->evaluate_quality ) {
    $this->renderActionForm( 'evaluate_quality', 0, (int) $content->getId(), 'Evaluate Quality', 'amanah_evaluate_content_quality' );
   }

   if ( null !== $content && null !== $this->review_content ) {
    if ( 'ready_for_review' === $content->getStatus() ) {
     $this->renderActionForm( 'approve', 0, (int) $content->getId(), 'Approve', 'amanah_review_content' );
    }
    if ( in_array( $content->getStatus(), array( 'ready_for_review', 'quality_warning', 'quality_failed' ), true ) ) {
     $this->renderActionForm( 'request_revision', 0, (int) $content->getId(), 'Request Revision', 'amanah_review_content' );
    }
   }

   if ( null !== $content && null !== $this->publish_content && 'approved' === $content->getStatus() ) {
    $this->renderActionForm( 'publish', 0, (int) $content->getId(), 'Publish', 'amanah_publish_content', true );
   }

   echo '</td></tr>';
   echo '<tr><td colspan="7">';
   $this->renderOutlineEditor( (int) $keyword_id, $brief );
   echo '</td></tr>';
  }

    echo '</tbody></table>';
  echo '<script>
  document.addEventListener("DOMContentLoaded", function () {
   var forms = document.querySelectorAll("form");
   var overlay = document.getElementById("amanah-production-busy");
   var title = document.getElementById("amanah-production-busy-title");
   var message = document.getElementById("amanah-production-busy-text");
   if (!overlay) { return; }
   forms.forEach(function (form) {
    if (!form.querySelector("input[name=amanah_page_blueprint_action]")) { return; }
    form.addEventListener("submit", function () {
     var button = form.querySelector("button[type=submit]");
     var action = form.querySelector("input[name=amanah_page_blueprint_action]");
     var states = {
      generate: ["Membuat Page Blueprint...", "Sedang menyiapkan struktur artikel. Mohon tunggu."],
      generate_content: ["Generating Content...", "AI sedang menghasilkan konten berdasarkan Blueprint. Mohon tunggu dan jangan klik tombol lain."],
      create_manual_content: ["Membuat Draft Manual...", "Membuat draft awal dari Page Blueprint. Mohon tunggu."],
      evaluate_quality: ["Evaluating Quality...", "Konten sedang dievaluasi. Mohon tunggu."],
      approve: ["Approving Content...", "Persetujuan konten sedang diproses."],
      request_revision: ["Requesting Revision...", "Status konten sedang diperbarui."],
      publish: ["Publishing Content...", "Konten sedang dipublikasikan. Mohon tunggu sampai proses selesai."],
      sync_wordpress_content: ["Syncing Content...", "Konten sedang disinkronkan dengan WordPress."]
     };
     var state = action && states[action.value] ? states[action.value] : ["Sedang memproses...", "Mohon tunggu dan jangan klik tombol lain."];
     title.textContent = state[0];
     message.textContent = state[1];
     overlay.style.display = "flex";
     document.body.classList.add("amanah-production-busy");
     forms.forEach(function (otherForm) {
      otherForm.querySelectorAll("button[type=submit]").forEach(function (otherButton) {
       otherButton.disabled = true;
       otherButton.setAttribute("aria-disabled", "true");
      });
     });
     if (button) { button.textContent = "Sedang berjalan..."; }
    });
   });
  });
  </script>';
  echo '</div>';
 }

 private function renderOutlineEditor( int $keyword_id, $brief ): void {
  if ( null === $this->save_content_outline ) {
   return;
  }
  $data = $brief->getData();
  $outline = isset( $data['article_outline'] ) && is_array( $data['article_outline'] ) ? $data['article_outline'] : array();
  $support = isset( $data['support_keywords'] ) && is_array( $data['support_keywords'] ) ? $data['support_keywords'] : array();
  if ( empty( $outline ) ) {
   $outline = $this->buildFallbackOutline( $data, $support );
  }
  $mapping = array();
  foreach ( (array) ( $data['support_keyword_mapping'] ?? array() ) as $item ) {
   if ( is_array( $item ) && isset( $item['keyword_id'] ) ) {
    $mapping[ (int) $item['keyword_id'] ] = sanitize_key( (string) ( $item['section_id'] ?? '' ) );
   }
  }

  echo '<details open style="margin:8px 0 18px;border:1px solid #dcdcde;border-radius:6px;padding:12px;background:#fff;">';
  echo '<summary style="cursor:pointer;font-weight:600;font-size:14px;">Content Brief → Editable Article Outline</summary>';
  echo '<p class="description">Edit H1/H2/H3, add/remove/reorder sections, then map every Potential Support Keyword to the section where it must be covered. This saved outline becomes the governing input for Content Generation.</p>';
  echo '<form method="post" class="amanah-outline-editor" data-keyword-id="' . esc_attr( (string) $keyword_id ) . '">';
  wp_nonce_field( 'amanah_save_content_outline', 'amanah_outline_nonce' );
  echo '<input type="hidden" name="amanah_page_blueprint_action" value="save_outline" />';
  echo '<input type="hidden" name="keyword_id" value="' . esc_attr( (string) $keyword_id ) . '" />';
  echo '<div class="amanah-outline-rows">';
  foreach ( $outline as $index => $section ) {
   $this->renderOutlineRow( $index, $section, $outline );
  }
  echo '</div>';
  echo '<p><button type="button" class="button amanah-outline-add">Add H2</button> <button type="submit" class="button button-primary">Save Outline &amp; Map Keywords</button></p>';
  if ( ! empty( $support ) ) {
   echo '<h3>Potential Support Keywords</h3><table class="widefat striped"><thead><tr><th>Keyword</th><th>Source / Coverage</th><th>Outline Section</th></tr></thead><tbody class="amanah-support-mapping">';
   foreach ( $support as $item ) {
    if ( ! is_array( $item ) ) { continue; }
    $id = absint( $item['id'] ?? 0 );
    $phrase = trim( (string) ( $item['phrase'] ?? $item['keyword'] ?? '' ) );
    if ( $id < 1 || '' === $phrase ) { continue; }
    echo '<tr><td><strong>' . esc_html( $phrase ) . '</strong></td><td>' . esc_html( 'google_autocomplete' === (string) ( $item['source'] ?? '' ) ? 'Google Autocomplete' : 'Support Coverage' ) . '</td><td><select class="amanah-support-select" name="support_mapping[' . esc_attr( (string) $id ) . ']" data-selected="' . esc_attr( $mapping[ $id ] ?? '' ) . '"></select></td></tr>';
   }
   echo '</tbody></table>';
  }
  echo '</form>';
  echo '<script>
(function(){
 var root=document.currentScript.previousElementSibling, rows=root.querySelector(".amanah-outline-rows"), add=root.querySelector(".amanah-outline-add");
 function refresh(){
  var items=Array.prototype.slice.call(rows.querySelectorAll(".amanah-outline-row"));
  items.forEach(function(row,i){row.querySelector(".amanah-outline-index").textContent=(i+1);row.querySelector(".amanah-outline-up").disabled=i===0;row.querySelector(".amanah-outline-down").disabled=i===items.length-1;});
  var opts=items.map(function(row){return {id:row.dataset.sectionId,title:row.querySelector(".amanah-outline-title").value||"Untitled"};});
  root.querySelectorAll(".amanah-support-select").forEach(function(select){var selected=select.value||select.dataset.selected||"";select.innerHTML="";opts.forEach(function(o){var op=document.createElement("option");op.value=o.id;op.textContent=o.title;select.appendChild(op);});select.value=selected;if(!select.value&&opts[0])select.value=opts[0].id;});
 }
 function bind(row){
  row.querySelector(".amanah-outline-up").addEventListener("click",function(){var p=row.previousElementSibling;if(p)rows.insertBefore(row,p);refresh();});
  row.querySelector(".amanah-outline-down").addEventListener("click",function(){var n=row.nextElementSibling;if(n)rows.insertBefore(n,row);refresh();});
  row.querySelector(".amanah-outline-remove").addEventListener("click",function(){if(rows.children.length>1){row.remove();refresh();}});
  row.querySelector(".amanah-outline-level").addEventListener("change",function(){row.dataset.level=this.value;});
  row.querySelector(".amanah-outline-title").addEventListener("input",refresh);
 }
 rows.querySelectorAll(".amanah-outline-row").forEach(bind);
 add.addEventListener("click",function(){var i=rows.children.length+1,id="section_new_"+Date.now();var row=document.createElement("div");row.className="amanah-outline-row";row.dataset.sectionId=id;row.innerHTML="<span class=\"amanah-outline-index\"></span><select class=\"amanah-outline-level\" name=\"article_outline["+i+"][level]\"><option value=\"2\">H2</option><option value=\"3\">H3</option></select><input type=\"hidden\" name=\"article_outline["+i+"][id]\" value=\""+id+"\" /><input class=\"amanah-outline-title\" type=\"text\" name=\"article_outline["+i+"][title]\" value=\"\" placeholder=\"Section title\" required /><button type=\"button\" class=\"button amanah-outline-up\">↑</button> <button type=\"button\" class=\"button amanah-outline-down\">↓</button> <button type=\"button\" class=\"button amanah-outline-remove\">Remove</button>";rows.appendChild(row);bind(row);refresh();});
 refresh();
})();
</script>';
 }

 private function buildFallbackOutline( array $data, array $support ): array {
  $keyword = isset( $data['keyword'] ) && is_array( $data['keyword'] ) ? $data['keyword'] : array();
  $primary_phrase = trim( (string) ( $keyword['phrase'] ?? $keyword['keyword'] ?? '' ) );
  $article_type = isset( $data['article_type'] ) ? (string) $data['article_type'] : 'supporting';
  $intent_coverage = isset( $data['intent_coverage'] ) && is_array( $data['intent_coverage'] ) ? $data['intent_coverage'] : array();
  $builder = new \Amanah\SEOEngine\Domain\ContentBrief\ArticleOutlineBuilder();
  return $builder->build( $primary_phrase, $article_type, $intent_coverage, $support )['article_outline'];
 }

 private function renderOutlineRow( int $index, array $section, array $outline ): void {
  $id = sanitize_key( (string) ( $section['id'] ?? 'section_' . $index ) );
  $level = max( 1, min( 3, (int) ( $section['level'] ?? 2 ) ) );
  $title = (string) ( $section['title'] ?? '' );
  echo '<div class="amanah-outline-row" data-section-id="' . esc_attr( $id ) . '" style="display:flex;gap:6px;align-items:center;margin:6px 0;">';
  echo '<span class="amanah-outline-index" style="min-width:22px;"></span>';
  echo '<select class="amanah-outline-level" name="article_outline[' . esc_attr( (string) $index ) . '][level]"><option value="1"' . selected( 1, $level, false ) . '>H1</option><option value="2"' . selected( 2, $level, false ) . '>H2</option><option value="3"' . selected( 3, $level, false ) . '>H3</option></select>';
  echo '<input type="hidden" name="article_outline[' . esc_attr( (string) $index ) . '][id]" value="' . esc_attr( $id ) . '" />';
  echo '<input class="amanah-outline-title" style="flex:1;" type="text" name="article_outline[' . esc_attr( (string) $index ) . '][title]" value="' . esc_attr( $title ) . '" required />';
  echo '<button type="button" class="button amanah-outline-up">↑</button><button type="button" class="button amanah-outline-down">↓</button><button type="button" class="button amanah-outline-remove">Remove</button>';
  echo '</div>';
 }

 private function renderActionForm( string $action, int $keyword_id, int $content_id, ?string $label, string $nonce_action, bool $primary = false ): void {
  if ( null === $label ) {
   return;
  }
  echo '<form method="post" style="display:inline-block;margin:2px 4px 2px 0;">';
  wp_nonce_field( $nonce_action, 'amanah_content_nonce' );
  echo '<input type="hidden" name="amanah_page_blueprint_action" value="' . esc_attr( $action ) . '" />';
  echo '<input type="hidden" name="keyword_id" value="' . esc_attr( (string) $keyword_id ) . '" />';
  echo '<input type="hidden" name="content_id" value="' . esc_attr( (string) $content_id ) . '" />';
  echo '<button type="submit" class="button' . ( $primary ? ' button-primary' : '' ) . '">' . esc_html( $label ) . '</button>';
  echo '</form>';
 }

 public function handlePostRequest(): void {
  if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['amanah_page_blueprint_action'] ) ) {
   return;
  }
  if ( ! current_user_can( 'manage_options' ) ) {
   wp_die( esc_html__( 'You are not allowed to manage Page Blueprints.', 'amanah-seo-engine' ) );
  }
  $this->handlePost();
 }

 private function handlePost(): void {
  $action = sanitize_key( (string) ( $_POST['amanah_page_blueprint_action'] ?? '' ) );
  $keyword_id = absint( $_POST['keyword_id'] ?? 0 );
  $content_id = absint( $_POST['content_id'] ?? 0 );

  try {
   if ( 'save_outline' === $action ) {
    check_admin_referer( 'amanah_save_content_outline', 'amanah_outline_nonce' );
    if ( $keyword_id < 1 || null === $this->save_content_outline ) {
     throw new \InvalidArgumentException( 'Editable Article Outline service is not available.' );
    }
    $outline = isset( $_POST['article_outline'] ) && is_array( $_POST['article_outline'] ) ? wp_unslash( $_POST['article_outline'] ) : array();
    $support_mapping = isset( $_POST['support_mapping'] ) && is_array( $_POST['support_mapping'] ) ? wp_unslash( $_POST['support_mapping'] ) : array();
    $this->save_content_outline->execute( $keyword_id, $outline, $support_mapping );
    add_settings_error( 'amanah_page_blueprint', 'outline_saved', 'Editable Article Outline saved. Potential Support Keywords are mapped and ready for generation.', 'success' );
    return;
   }

   if ( 'generate' === $action ) {
    check_admin_referer( 'amanah_generate_page_blueprint', 'amanah_content_nonce' );
    if ( $keyword_id < 1 ) {
     throw new \InvalidArgumentException( 'Invalid keyword.' );
    }
    $this->generate->execute( $keyword_id );
    add_settings_error( 'amanah_page_blueprint', 'generated', 'Page Blueprint created successfully.', 'success' );
    return;
   }

   if ( 'generate_content' === $action ) {
    check_admin_referer( 'amanah_generate_content', 'amanah_content_nonce' );
    if ( $keyword_id < 1 ) {
     throw new \InvalidArgumentException( 'Invalid keyword.' );
    }
    $this->generate_content->execute( $keyword_id );
    add_settings_error( 'amanah_page_blueprint', 'content_generated', 'Content generated successfully. Quality evaluation is required before approval.', 'success' );
    return;
   }

   if ( 'create_manual_content' === $action ) {
    check_admin_referer( 'amanah_create_manual_content', 'amanah_content_nonce' );
    if ( $keyword_id < 1 || null === $this->create_manual_content ) {
     throw new \InvalidArgumentException( 'Manual content creation service is not available.' );
    }
    $content = $this->create_manual_content->execute( $keyword_id );
    $edit_url = add_query_arg(
     array(
      'page' => 'amanah-seo-engine-content-editor',
      'edit_content_id' => (int) $content->id,
     ),
     admin_url( 'admin.php' )
    );
    wp_safe_redirect( $edit_url );
    exit;
   }

   if ( 'sync_wordpress_content' === $action ) {
    check_admin_referer( 'amanah_sync_wordpress_content', 'amanah_content_nonce' );
    if ( $content_id < 1 || null === $this->sync_wordpress_content ) {
     throw new \InvalidArgumentException( 'WordPress content sync service is not available.' );
    }
    $this->sync_wordpress_content->execute( $content_id );
    add_settings_error( 'amanah_page_blueprint', 'content_synced', 'WordPress content synchronized. Review the current draft before continuing.', 'success' );
    return;
   }

   if ( 'evaluate_quality' === $action ) {
    check_admin_referer( 'amanah_evaluate_content_quality', 'amanah_content_nonce' );
    if ( $content_id < 1 || null === $this->evaluate_quality ) {
     throw new \InvalidArgumentException( 'Content quality service is not available.' );
    }
    $result = $this->evaluate_quality->execute( $content_id );
    add_settings_error( 'amanah_page_blueprint', 'quality_evaluated', 'Quality evaluation completed with status: ' . strtoupper( $result->getStatus() ) . '.', 'success' );
    return;
   }

   if ( 'approve' === $action ) {
    check_admin_referer( 'amanah_review_content', 'amanah_content_nonce' );
    if ( $content_id < 1 || null === $this->review_content ) {
     throw new \InvalidArgumentException( 'Content review service is not available.' );
    }
    $this->review_content->approve( $content_id );
    add_settings_error( 'amanah_page_blueprint', 'content_approved', 'Content manually approved. It is now ready for publishing.', 'success' );
    return;
   }

   if ( 'request_revision' === $action ) {
    check_admin_referer( 'amanah_review_content', 'amanah_content_nonce' );
    if ( $content_id < 1 || null === $this->review_content ) {
     throw new \InvalidArgumentException( 'Content review service is not available.' );
    }
    $this->review_content->requestRevision( $content_id );
    add_settings_error( 'amanah_page_blueprint', 'revision_requested', 'Revision requested. Content was not published.', 'success' );
    return;
   }

   if ( 'publish' === $action ) {
    check_admin_referer( 'amanah_publish_content', 'amanah_content_nonce' );
    if ( $content_id < 1 || null === $this->publish_content ) {
     throw new \InvalidArgumentException( 'Content publishing service is not available.' );
    }
    $post_id = $this->publish_content->execute( $content_id );
    add_settings_error( 'amanah_page_blueprint', 'content_published', 'Content published to WordPress. Post ID: ' . $post_id . '.', 'success' );
    return;
   }

   throw new \InvalidArgumentException( 'Invalid workflow action.' );
  } catch ( \Throwable $e ) {
   add_settings_error( 'amanah_page_blueprint', 'workflow_error', $e->getMessage(), 'error' );
  }
 }

}
