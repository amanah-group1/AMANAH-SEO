<?php
/** Secure admin UI for the first AMANAH SEO MVP cycle. */
defined( 'ABSPATH' ) || exit;
final class AMANAH_SEO_Admin {
 private static $statuses=array('not_started'=>'Belum dimulai','ready'=>'Siap','in_progress'=>'Berjalan','awaiting_approval'=>'Menunggu persetujuan','blocked'=>'Terhambat','failed'=>'Gagal','completed'=>'Selesai','cancelled'=>'Dibatalkan');
 public static function register() {
  add_action('admin_menu',array(__CLASS__,'menu'));
  add_action('admin_post_amanah_seo_save_workspace',array(__CLASS__,'save_workspace'));
  add_action('admin_post_amanah_seo_create_finding',array(__CLASS__,'create_finding'));
  add_action('admin_post_amanah_seo_update_finding_status',array(__CLASS__,'update_finding_status'));
  add_action('admin_post_amanah_seo_create_task',array(__CLASS__,'create_task'));
  add_action('admin_post_amanah_seo_update_task_status',array(__CLASS__,'update_task_status'));
  add_action('admin_post_amanah_seo_decide_approval',array(__CLASS__,'decide_approval'));
 }
 public static function menu() {
  if(!current_user_can('manage_options')) return;
  add_menu_page('AMANAH SEO','AMANAH SEO','manage_options','amanah-seo',array(__CLASS__,'dashboard'),'dashicons-chart-area',58);
  add_submenu_page('amanah-seo','Website & Workspace','Website & Workspace','manage_options','amanah-seo-workspace',array(__CLASS__,'workspace_page'));
  add_submenu_page('amanah-seo','Temuan','Temuan','manage_options','amanah-seo-findings',array(__CLASS__,'findings_page'));
  add_submenu_page('amanah-seo','Pekerjaan','Pekerjaan','manage_options','amanah-seo-tasks',array(__CLASS__,'tasks_page'));
  add_submenu_page('amanah-seo','Riwayat','Riwayat','manage_options','amanah-seo-history',array(__CLASS__,'history_page'));
 }
 private static function guard(){if(!current_user_can('manage_options')) wp_die(esc_html__('You are not allowed to access this page.','amanah-seo'),'',array('response'=>403));}
 private static function start($title){self::guard();echo '<div class="wrap"><h1>'.esc_html($title).'</h1>';if(isset($_GET['amanah_notice'])){$n=sanitize_key(wp_unslash($_GET['amanah_notice']));$m=array('saved'=>'Data berhasil disimpan.','error'=>'Data gagal disimpan. Periksa input dan coba lagi.','updated'=>'Status pekerjaan diperbarui.','decision'=>'Keputusan persetujuan dicatat.','evidence_required'=>'Pekerjaan selesai wajib memiliki bukti hasil.','approval_pending'=>'Persetujuan masih menunggu keputusan.','workspace_saved'=>'Pengaturan workspace berhasil disimpan.','finding_updated'=>'Status temuan diperbarui.','finding_update_error'=>'Status temuan tidak dapat diperbarui. Pastikan catatan penyelesaian diisi.');if(isset($m[$n]))echo '<div class="notice notice-'.(in_array($n,array('error','evidence_required','approval_pending','finding_update_error'),true)?'error':'success').' is-dismissible"><p>'.esc_html($m[$n]).'</p></div>';}}
 private static function end(){echo '</div>';}
 public static function dashboard(){
  self::start('AMANAH SEO — Dashboard');
  $f=AMANAH_SEO_Store::findings(100);
  $t=AMANAH_SEO_Store::tasks(100);
  $counts=wp_count_posts('post');
  $published=is_object($counts)&&isset($counts->publish)?(int)$counts->publish:0;
  $workspace=get_option('amanah_seo_workspace',array());
  $workspace_name=isset($workspace['name'])&&$workspace['name']!==''?$workspace['name']:get_bloginfo('name');
  $workspace_goal=isset($workspace['goal'])?$workspace['goal']:'';
  $open_findings=0;
  foreach($f as $finding){if(in_array($finding->status,array('open','in_progress'),true))$open_findings++;}
  $waiting=0;$blocked=0;$failed=0;
  foreach($t as $task){if('awaiting_approval'===$task->status)$waiting++;if('blocked'===$task->status)$blocked++;if('failed'===$task->status)$failed++;}
  echo '<p>Ringkasan data WordPress lokal dan catatan manual. GSC, AI, dan ENGINE belum diklaim aktif.</p>';
  echo '<div class="notice notice-info inline"><p><strong>Workspace:</strong> '.esc_html($workspace_name).' &nbsp; <strong>Website:</strong> '.esc_html(home_url('/')).' &nbsp; <strong>Konten terbit:</strong> '.esc_html((string)$published).'</p>';
  if($workspace_goal!=='')echo '<p><strong>Tujuan website:</strong> '.esc_html($workspace_goal).'</p>';
  echo '<p><strong>Temuan terbuka:</strong> '.esc_html((string)$open_findings).' &nbsp; <strong>Persetujuan menunggu:</strong> '.esc_html((string)$waiting).' &nbsp; <strong>Terhambat:</strong> '.esc_html((string)$blocked).' &nbsp; <strong>Gagal:</strong> '.esc_html((string)$failed).'</p></div>';
  echo '<h2>Siklus MVP</h2><ol><li>Catat temuan beserta sumber, bukti, prioritas, dan ketidakpastian.</li><li>Ubah status temuan saat pemeriksaan berkembang atau selesai.</li><li>Buat pekerjaan dengan penanggung jawab dan kriteria selesai.</li><li>Catat keputusan persetujuan; tidak ada perubahan website otomatis.</li><li>Tinjau riwayat dan bukti hasil sebelum menentukan langkah berikutnya.</li></ol>';
  echo '<p><a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-workspace')).'">Website & Workspace</a> <a class="button button-primary" href="'.esc_url(admin_url('admin.php?page=amanah-seo-findings')).'">Kelola temuan</a> <a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-tasks')).'">Kelola pekerjaan</a> <a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-history')).'">Lihat riwayat</a></p>';
  self::end();
 }
 public static function workspace_page(){
  self::start('Website & Workspace');
  $workspace=get_option('amanah_seo_workspace',array());
  $name=isset($workspace['name'])&&$workspace['name']!==''?$workspace['name']:get_bloginfo('name');
  $goal=isset($workspace['goal'])?$workspace['goal']:'';
  echo '<p>MVP mengelola satu website WordPress yang sedang dipasangi AMANAH SEO. URL sumber berasal dari konfigurasi WordPress dan tidak dapat diubah dari halaman ini.</p>';
  echo '<table class="form-table"><tr><th>URL website</th><td><code>'.esc_html(home_url('/')).'</code></td></tr><tr><th>Alamat admin</th><td><code>'.esc_html(admin_url()).'</code></td></tr></table>';
  echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_save_workspace">';
  wp_nonce_field('amanah_seo_save_workspace');
  echo '<table class="form-table"><tr><th><label for="amanah_workspace_name">Nama workspace</label></th><td><input id="amanah_workspace_name" class="regular-text" name="workspace_name" maxlength="190" value="'.esc_attr($name).'" required></td></tr><tr><th><label for="amanah_workspace_goal">Tujuan website</label></th><td><textarea id="amanah_workspace_goal" class="large-text" name="workspace_goal" rows="3" maxlength="2000">'.esc_textarea($goal).'</textarea><p class="description">Tujuan membantu pengguna menilai alasan prioritas. Jangan masukkan kata sandi atau API key.</p></td></tr></table>';
  submit_button('Simpan workspace');
  echo '</form><div class="notice notice-info inline"><p>Integrasi Google Search Console belum dikonfigurasi dan tidak ditampilkan sebagai aktif.</p></div>';
  self::end();
 }
 public static function findings_page(){
  self::start('Temuan dan Prioritas');
  $items=AMANAH_SEO_Store::findings();
  echo '<h2>Catat temuan</h2><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_create_finding">';
  wp_nonce_field('amanah_seo_create_finding');
  echo '<table class="form-table"><tr><th>Judul</th><td><input class="regular-text" name="title" maxlength="255" required></td></tr><tr><th>Deskripsi</th><td><textarea class="large-text" name="description" rows="2" required></textarea></td></tr><tr><th>Sumber</th><td><select name="source"><option value="manual">Masukan manual</option><option value="wordpress">WordPress lokal</option><option value="gsc_unverified">GSC belum diverifikasi</option></select></td></tr><tr><th>Bukti / alasan</th><td><textarea class="large-text" name="evidence" rows="2" required></textarea></td></tr><tr><th>Prioritas</th><td><select name="priority"><option value="low">Rendah</option><option value="medium" selected>Sedang</option><option value="high">Tinggi</option><option value="urgent">Mendesak</option></select></td></tr><tr><th>Ketidakpastian</th><td><select name="uncertainty"><option value="low">Rendah</option><option value="medium" selected>Sedang</option><option value="high">Tinggi</option><option value="unknown">Belum diketahui</option></select></td></tr></table>';
  submit_button('Simpan temuan');
  echo '</form><hr><h2>Daftar temuan</h2>';
  if(!$items){echo '<p>Belum ada temuan tercatat.</p>';}else{
   echo '<table class="widefat striped"><thead><tr><th>ID</th><th>Temuan / bukti</th><th>Prioritas</th><th>Ketidakpastian</th><th>Sumber</th><th>Status</th><th>Perbarui status</th></tr></thead><tbody>';
   foreach($items as $x){
    echo '<tr><td>'.esc_html((string)$x->id).'</td><td><strong>'.esc_html($x->title).'</strong><br>'.esc_html($x->description).'<br><small>'.esc_html($x->evidence).'</small></td><td>'.esc_html($x->priority).'</td><td>'.esc_html($x->uncertainty).'</td><td>'.esc_html($x->source).'</td><td>'.esc_html($x->status).'</td><td>';
    echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_update_finding_status"><input type="hidden" name="finding_id" value="'.esc_attr((string)$x->id).'">';
    wp_nonce_field('amanah_seo_update_finding_status_'.$x->id);
    echo '<select name="status">';
    foreach(array('open'=>'Terbuka','in_progress'=>'Sedang diperiksa','resolved'=>'Selesai','dismissed'=>'Dikesampingkan') as $status_key=>$status_label){echo '<option value="'.esc_attr($status_key).'"'.selected($status_key,$x->status,false).'>'.esc_html($status_label).'</option>';}
    echo '</select><br><input type="text" name="status_note" maxlength="500" placeholder="Catatan hasil (wajib untuk selesai/dikesampingkan)"><br><button class="button" type="submit">Simpan status</button></form></td></tr>';
   }
   echo '</tbody></table>';
  }
  self::end();
 }
 public static function tasks_page(){self::start('Pekerjaan');$findings=AMANAH_SEO_Store::findings(100);$tasks=AMANAH_SEO_Store::tasks();echo '<h2>Buat pekerjaan</h2><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_create_task">';wp_nonce_field('amanah_seo_create_task');echo '<table class="form-table"><tr><th>Judul</th><td><input class="regular-text" name="title" maxlength="255" required></td></tr><tr><th>Temuan terkait</th><td><select name="finding_id"><option value="0">Belum dipilih</option>';foreach($findings as $f)echo '<option value="'.esc_attr((string)$f->id).'">'.esc_html('#'.$f->id.' — '.$f->title).'</option>';echo '</select></td></tr><tr><th>Deskripsi</th><td><textarea class="large-text" name="description" rows="2" required></textarea></td></tr><tr><th>Penanggung jawab</th><td><input class="regular-text" name="owner"></td></tr><tr><th>Kriteria selesai</th><td><textarea class="large-text" name="success_criteria" rows="2" required></textarea></td></tr><tr><th>Status awal</th><td><select name="status">';foreach(self::$statuses as $k=>$v){if('completed'===$k)continue;echo '<option value="'.esc_attr($k).'"'.selected($k,'not_started',false).'>'.esc_html($v).'</option>';}echo '</select><p class="description">Status persetujuan tidak menjalankan tindakan otomatis.</p></td></tr></table>';submit_button('Simpan pekerjaan');echo '</form><hr><h2>Daftar pekerjaan</h2>';if(!$tasks){echo '<p>Belum ada pekerjaan.</p>';}else{echo '<table class="widefat striped"><thead><tr><th>ID</th><th>Pekerjaan</th><th>Temuan</th><th>Pemilik</th><th>Kriteria selesai</th><th>Status / persetujuan</th><th>Ubah status / persetujuan</th></tr></thead><tbody>';foreach($tasks as $x){echo '<tr><td>'.esc_html((string)$x->id).'</td><td><strong>'.esc_html($x->title).'</strong><br>'.esc_html($x->description).'</td><td>'.esc_html($x->finding_id?'#'.$x->finding_id:'—').'</td><td>'.esc_html($x->owner?:'Belum ditetapkan').'</td><td>'.esc_html($x->success_criteria).'</td><td>'.esc_html(isset(self::$statuses[$x->status])?self::$statuses[$x->status]:$x->status).'<br>Persetujuan: '.esc_html($x->approval_status).(!empty($x->completion_evidence)?'<br>Bukti: '.esc_html($x->completion_evidence):'').'</td><td><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_update_task_status"><input type="hidden" name="task_id" value="'.esc_attr((string)$x->id).'">';wp_nonce_field('amanah_seo_update_task_status_'.$x->id);echo '<select name="status">';foreach(self::$statuses as $k=>$v)echo '<option value="'.esc_attr($k).'"'.selected($k,$x->status,false).'>'.esc_html($v).'</option>';echo '</select><br><textarea name="completion_evidence" rows="2" placeholder="Bukti wajib saat status Selesai"></textarea><br><button class="button" type="submit">Perbarui</button></form>';if('pending'===$x->approval_status){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_decide_approval"><input type="hidden" name="task_id" value="'.esc_attr((string)$x->id).'">';wp_nonce_field('amanah_seo_decide_approval_'.$x->id);echo '<input type="text" name="decision_note" placeholder="Catatan keputusan (opsional)"><br><button class="button button-primary" name="decision" value="approved">Setujui</button> <button class="button" name="decision" value="rejected">Tolak</button></form>';}echo '</td></tr>';}echo '</tbody></table>';}self::end();}
 public static function history_page(){self::start('Riwayat Perubahan');$items=AMANAH_SEO_Store::events();if(!$items){echo '<p>Belum ada peristiwa tercatat.</p>';}else{echo '<table class="widefat striped"><thead><tr><th>Waktu</th><th>Objek</th><th>ID</th><th>Peristiwa</th><th>Detail</th><th>Pengguna</th></tr></thead><tbody>';foreach($items as $x)echo '<tr><td>'.esc_html($x->created_at).'</td><td>'.esc_html($x->object_type).'</td><td>'.esc_html((string)$x->object_id).'</td><td>'.esc_html($x->event_type).'</td><td>'.esc_html($x->detail).'</td><td>'.esc_html((string)$x->user_id).'</td></tr>';echo '</tbody></table>';}self::end();}
 public static function save_workspace(){
  self::guard();
  check_admin_referer('amanah_seo_save_workspace');
  $name=sanitize_text_field(wp_unslash($_POST['workspace_name']??''));
  $goal=sanitize_textarea_field(wp_unslash($_POST['workspace_goal']??''));
  if(''===$name)$name=get_bloginfo('name');
  update_option('amanah_seo_workspace',array('name'=>$name,'goal'=>$goal),false);
  self::redirect('workspace_saved','amanah-seo-workspace');
 }
 public static function update_finding_status(){
  self::guard();
  $id=absint($_POST['finding_id']??0);
  check_admin_referer('amanah_seo_update_finding_status_'.$id);
  $status=sanitize_key(wp_unslash($_POST['status']??''));
  $note=sanitize_textarea_field(wp_unslash($_POST['status_note']??''));
  $result=AMANAH_SEO_Store::update_finding_status($id,$status,$note);
  $notice=is_wp_error($result)?'finding_update_error':'finding_updated';
  self::redirect($notice,'amanah-seo-findings');
 }
 public static function create_finding(){self::guard();check_admin_referer('amanah_seo_create_finding');$d=array('title'=>sanitize_text_field(wp_unslash($_POST['title']??'')),'description'=>sanitize_textarea_field(wp_unslash($_POST['description']??'')),'source'=>sanitize_key(wp_unslash($_POST['source']??'manual')),'evidence'=>sanitize_textarea_field(wp_unslash($_POST['evidence']??'')),'priority'=>sanitize_key(wp_unslash($_POST['priority']??'medium')),'uncertainty'=>sanitize_key(wp_unslash($_POST['uncertainty']??'medium')));$d['source']=in_array($d['source'],array('manual','wordpress','gsc_unverified'),true)?$d['source']:'manual';$d['priority']=in_array($d['priority'],array('low','medium','high','urgent'),true)?$d['priority']:'medium';$d['uncertainty']=in_array($d['uncertainty'],array('low','medium','high','unknown'),true)?$d['uncertainty']:'medium';$r=($d['title']&&$d['description']&&$d['evidence'])?AMANAH_SEO_Store::create_finding($d):new WP_Error('invalid','Required fields missing');self::redirect(is_wp_error($r)?'error':'saved','amanah-seo-findings');}
 public static function create_task(){self::guard();check_admin_referer('amanah_seo_create_task');$d=array('finding_id'=>absint($_POST['finding_id']??0),'title'=>sanitize_text_field(wp_unslash($_POST['title']??'')),'description'=>sanitize_textarea_field(wp_unslash($_POST['description']??'')),'owner'=>sanitize_text_field(wp_unslash($_POST['owner']??'')),'success_criteria'=>sanitize_textarea_field(wp_unslash($_POST['success_criteria']??'')),'status'=>sanitize_key(wp_unslash($_POST['status']??'not_started')));if(!array_key_exists($d['status'],self::$statuses))$d['status']='not_started';if($d['finding_id']&&!self::finding_exists($d['finding_id']))$d['finding_id']=0;$r=($d['title']&&$d['description']&&$d['success_criteria'])?AMANAH_SEO_Store::create_task($d):new WP_Error('invalid','Required fields missing');self::redirect(is_wp_error($r)?'error':'saved','amanah-seo-tasks');}
 private static function finding_exists($id){global $wpdb;$table=$wpdb->prefix.'amanah_seo_findings';return (bool)$wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE id = %d",$id));}
 public static function update_task_status(){self::guard();$id=absint($_POST['task_id']??0);check_admin_referer('amanah_seo_update_task_status_'.$id);$status=sanitize_key(wp_unslash($_POST['status']??''));$evidence=sanitize_textarea_field(wp_unslash($_POST['completion_evidence']??''));$r=AMANAH_SEO_Store::update_task_status($id,$status,$evidence);$notice=is_wp_error($r)?('completion_evidence_required'===$r->get_error_code()?'evidence_required':('approval_pending'===$r->get_error_code()?'approval_pending':'error')):'updated';self::redirect($notice,'amanah-seo-tasks');}
 public static function decide_approval(){self::guard();$id=absint($_POST['task_id']??0);check_admin_referer('amanah_seo_decide_approval_'.$id);$decision=sanitize_key(wp_unslash($_POST['decision']??''));$note=sanitize_textarea_field(wp_unslash($_POST['decision_note']??''));$r=AMANAH_SEO_Store::decide_approval($id,$decision,$note);self::redirect(is_wp_error($r)?'error':'decision','amanah-seo-tasks');}
 private static function redirect($notice,$page){wp_safe_redirect(add_query_arg(array('page'=>$page,'amanah_notice'=>$notice),admin_url('admin.php')));exit;}
}
