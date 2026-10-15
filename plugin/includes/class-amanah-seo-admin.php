<?php
/** Secure admin UI for the first AMANAH SEO MVP cycle. */
defined( 'ABSPATH' ) || exit;
final class AMANAH_SEO_Admin {
 private static $statuses=array('not_started'=>'Belum dimulai','ready'=>'Siap','in_progress'=>'Berjalan','awaiting_approval'=>'Menunggu persetujuan','blocked'=>'Terhambat','failed'=>'Gagal','completed'=>'Selesai','cancelled'=>'Dibatalkan');
 public static function register() {
  // Register Core navigation last so a legacy/companion plugin cannot replace the AMANAH SEO parent callback.
  add_action('admin_menu',array(__CLASS__,'menu'),9998);
  add_action('admin_menu',array('AMANAH_SEO_Module_Navigation','menu'),30);
  add_action('admin_post_amanah_seo_run_self_test',array(__CLASS__,'run_self_test'));
  add_action('admin_post_amanah_seo_save_workspace',array(__CLASS__,'save_workspace'));
  add_action('admin_post_amanah_seo_create_finding',array(__CLASS__,'create_finding'));
  add_action('admin_post_amanah_seo_import_content_issue',array(__CLASS__,'import_content_issue'));
  add_action('admin_post_amanah_seo_update_finding_status',array(__CLASS__,'update_finding_status'));
  add_action('admin_post_amanah_seo_create_task',array(__CLASS__,'create_task'));
  add_action('admin_post_amanah_seo_update_task_status',array(__CLASS__,'update_task_status'));
  add_action('admin_post_amanah_seo_decide_approval',array(__CLASS__,'decide_approval'));
 }
 public static function menu() {
  if(!current_user_can('manage_amanah_seo')) return;

  // The single AMANAH SEO parent opens the sales landing page.
  add_menu_page('AMANAH SEO','🚀 AMANAH SEO','manage_amanah_seo','amanah-seo',array(__CLASS__,'sales_page'),'dashicons-chart-line',58);

  // Keep the requested submenu order. Sales Page is the parent callback, not a submenu.
  add_submenu_page('amanah-seo','Settings & Access','⚙️ Settings & Access','manage_amanah_seo','amanah-seo-access',array('AMANAH_SEO_Module_Licenses','render'));
  add_submenu_page('amanah-seo','CORE','🧩 CORE','manage_amanah_seo','amanah-seo-core',array('AMANAH_SEO_Module_Navigation','core_dashboard'));
  add_submenu_page('amanah-seo','AMANAH SEO AI','🤖 AMANAH SEO AI','manage_amanah_seo','amanah-seo-ai-tools',array('AMANAH_SEO_Module_Navigation','ai_dashboard'));
  add_submenu_page('amanah-seo','AMANAH SEO ENGINE','🔍 AMANAH SEO ENGINE','manage_amanah_seo','amanah-seo-engine-workflow',array('AMANAH_SEO_Module_Navigation','engine_dashboard'));

  // Feature pages remain registered, but their sidebar entries are hidden by navigation enforcement.
  add_submenu_page('amanah-seo-core','Website & Workspace','Website & Workspace','manage_amanah_seo','amanah-seo-workspace',array(__CLASS__,'workspace_page'));
  add_submenu_page('amanah-seo-core','Temuan','Temuan','manage_amanah_seo','amanah-seo-findings',array(__CLASS__,'findings_page'));
  add_submenu_page('amanah-seo-core','Pekerjaan','Pekerjaan','manage_amanah_seo','amanah-seo-tasks',array(__CLASS__,'tasks_page'));
  add_submenu_page('amanah-seo-core','Riwayat','Riwayat','manage_amanah_seo','amanah-seo-history',array(__CLASS__,'history_page'));
  add_submenu_page('amanah-seo-core','Diagnostik','Diagnostik','manage_amanah_seo','amanah-seo-diagnostics',array(__CLASS__,'diagnostics_page'));
  add_submenu_page(null,'Dashboard','Dashboard','manage_amanah_seo','amanah-seo-dashboard',array(__CLASS__,'dashboard'));
 }
 private static function guard(){if(!current_user_can('manage_amanah_seo')) wp_die(esc_html__('You are not allowed to access this page.','amanah-seo'),'',array('response'=>403));}
 private static function start($title){self::guard();echo '<div class="wrap"><h1>'.esc_html($title).'</h1>';if(isset($_GET['amanah_notice'])){$n=sanitize_key(wp_unslash($_GET['amanah_notice']));$m=array('saved'=>'Data berhasil disimpan.','error'=>'Data gagal disimpan. Periksa input dan coba lagi.','updated'=>'Status pekerjaan diperbarui.','decision'=>'Keputusan persetujuan dicatat.','evidence_required'=>'Pekerjaan selesai wajib memiliki bukti hasil.','approval_pending'=>'Persetujuan masih menunggu keputusan.','approval_rejected'=>'Persetujuan ditolak. Ajukan kembali setelah pekerjaan direvisi.','workspace_saved'=>'Pengaturan workspace berhasil disimpan.','finding_updated'=>'Status temuan diperbarui.','finding_update_error'=>'Status temuan tidak dapat diperbarui. Pastikan catatan penyelesaian diisi.','self_test_complete'=>'Uji runtime selesai. Lihat hasil pemeriksaan di bawah.','dependency_incomplete'=>'Pekerjaan belum dapat diselesaikan karena dependensinya belum selesai.','invalid_due_date'=>'Format tenggat tidak valid.','dependency_not_found'=>'Pekerjaan dependensi tidak ditemukan.','invalid_task_input'=>'Judul, deskripsi, atau kriteria selesai tidak valid. Periksa panjang dan isi; judul tidak boleh berisi label formulir.','task_field_too_long'=>'Satu atau beberapa kolom pekerjaan melebihi batas panjang.','suspicious_task_title'=>'Judul pekerjaan tampak berisi label formulir. Masukkan judul pekerjaan yang ringkas.');if(isset($m[$n]))echo '<div class="notice notice-'.(in_array($n,array('error','evidence_required','approval_pending','approval_rejected','finding_update_error','invalid_task_input','task_field_too_long','suspicious_task_title','invalid_due_date','dependency_not_found'),true)?'error':'success').' is-dismissible"><p>'.esc_html($m[$n]).'</p></div>';}}
 private static function end(){echo '</div>';}
 /**
  * Reject accidental form/table-header text before it becomes a task title.
  * Legacy records are never modified; the UI flags suspicious titles for review.
  */
 public static function task_title_needs_review($title){
  if(!is_string($title)||''===trim($title)) return true;
  $labels=array('Pekerjaan','Temuan','Pemilik','Kriteria selesai','Tenggat','Dependensi','Status / persetujuan','Ubah status / persetujuan');
  $matches=0;
  foreach($labels as $label){if(false!==stripos($title,$label))$matches++;}
  return $matches>=4;
 }
 public static function task_title_cell($title,$id){
  if(self::task_title_needs_review((string)$title)){
   return '<strong>Judul perlu diperiksa — pekerjaan #'.esc_html((string)absint($id)).'</strong><br><small>Judul tersimpan tampak memuat beberapa label formulir. Data tidak diubah; periksa di halaman Pekerjaan.</small>';
  }
  return '<strong>'.esc_html((string)$title).'</strong>';
 }
 public static function normalize_task_input($input){
  if(!is_array($input))return new WP_Error('invalid_task_input','Invalid task input.');
  $title=trim(sanitize_text_field(wp_unslash($input['title']??'')));
  $description=trim(sanitize_textarea_field(wp_unslash($input['description']??'')));
  $owner=trim(sanitize_text_field(wp_unslash($input['owner']??'')));
  $criteria=trim(sanitize_textarea_field(wp_unslash($input['success_criteria']??'')));
  $length=function($value){return function_exists('mb_strlen')?mb_strlen($value,'UTF-8'):strlen($value);};
  if(''===$title||''===$description||''===$criteria)return new WP_Error('invalid_task_input','Title, description, and success criteria are required.');
  if($length($title)>255||$length($owner)>190||$length($description)>5000||$length($criteria)>2000)return new WP_Error('task_field_too_long','One or more task fields exceed the allowed length.');
  if(self::task_title_needs_review($title))return new WP_Error('suspicious_task_title','The task title appears to contain form labels. Enter a concise task title.');
  return array('title'=>$title,'description'=>$description,'owner'=>$owner,'success_criteria'=>$criteria);
 }
 public static function dashboard_summary($findings,$tasks){
  $summary=array('open_findings'=>0,'priority_findings'=>0,'tasks_total'=>count($tasks),'tasks_active'=>0,'tasks_in_progress'=>0,'tasks_waiting_approval'=>0,'tasks_blocked'=>0,'tasks_failed'=>0,'tasks_completed'=>0,'next_action'=>'review_findings');
  foreach($findings as $finding){
   $status=isset($finding->status)?$finding->status:'';
   $priority=isset($finding->priority)?$finding->priority:'';
   if(in_array($status,array('open','in_progress'),true)){
    $summary['open_findings']++;
    if(in_array($priority,array('urgent','high'),true))$summary['priority_findings']++;
   }
  }
  foreach($tasks as $task){
   $status=isset($task->status)?$task->status:'';
   if(!in_array($status,array('completed','cancelled'),true))$summary['tasks_active']++;
   if('in_progress'===$status)$summary['tasks_in_progress']++;
   if('awaiting_approval'===$status)$summary['tasks_waiting_approval']++;
   if('blocked'===$status)$summary['tasks_blocked']++;
   if('failed'===$status)$summary['tasks_failed']++;
   if('completed'===$status)$summary['tasks_completed']++;
  }
  if($summary['tasks_waiting_approval']>0)$summary['next_action']='review_approval';
  elseif($summary['tasks_blocked']>0||$summary['tasks_failed']>0)$summary['next_action']='resolve_blockers';
  elseif($summary['priority_findings']>0)$summary['next_action']='prioritize_finding';
  elseif($summary['tasks_in_progress']>0)$summary['next_action']='continue_task';
  elseif($summary['open_findings']>0)$summary['next_action']='review_findings';
  else $summary['next_action']='scan_website';
  return $summary;
 }
 public static function sales_page(){
  self::start('AMANAH SEO');
  echo '<style>
   .amanah-seo-sales{max-width:1240px}
   .amanah-seo-sales .amanah-sales-hero{background:linear-gradient(125deg,#0b1f3a 0%,#124f78 58%,#087e83 100%);color:#fff;padding:clamp(28px,5vw,58px);border-radius:14px;margin:18px 0 30px}
   .amanah-seo-sales .amanah-sales-eyebrow{margin:0 0 14px;color:#a9e9ed;font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
   .amanah-seo-sales .amanah-sales-hero h1{max-width:900px;font-size:clamp(30px,4vw,46px);line-height:1.13;color:#fff;margin:0 0 20px}
   .amanah-seo-sales .amanah-sales-hero p{max-width:850px;font-size:17px;line-height:1.8;color:#edf7fb;margin:0 0 16px}
   .amanah-seo-sales .amanah-sales-cta{display:inline-block!important;height:auto!important;padding:12px 18px!important;background:#fff!important;border-color:#fff!important;color:#123b59!important;font-weight:700!important}
   .amanah-seo-sales .amanah-sales-section{margin:32px 0}
   .amanah-seo-sales .amanah-sales-section>h2{font-size:clamp(23px,3vw,30px);line-height:1.3;margin:0 0 12px;color:#102b4e}
   .amanah-seo-sales .amanah-sales-lead{max-width:900px;font-size:15px;line-height:1.8}
   .amanah-seo-sales .amanah-sales-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin:22px 0}
   .amanah-seo-sales .amanah-sales-card{box-sizing:border-box;display:flex;flex-direction:column;min-width:0;margin:0;padding:24px;border:1px solid #dcdcde;border-top:4px solid #2271b1;border-radius:9px;background:#fff;box-shadow:0 2px 8px rgba(16,43,78,.05)}
   .amanah-seo-sales .amanah-sales-card h3{font-size:21px;line-height:1.35;margin:0 0 12px}
   .amanah-seo-sales .amanah-sales-card p{line-height:1.75}
   .amanah-seo-sales .amanah-sales-card .amanah-sales-card-bottom{margin-top:auto;padding-top:8px}
   .amanah-seo-sales .amanah-sales-card-engine{border-top-color:#07847f}
   .amanah-seo-sales .amanah-sales-card-core{border-top-color:#68788b}
   .amanah-seo-sales .amanah-sales-note{background:#f0f6fc;border-left:4px solid #2271b1;border-radius:5px;padding:18px 22px;line-height:1.8}
   .amanah-seo-sales .amanah-sales-final{padding:28px;background:#102b4e;color:#fff;border-radius:10px}
   .amanah-seo-sales .amanah-sales-final h2{color:#fff;font-size:26px;margin:0 0 10px}
   .amanah-seo-sales .amanah-sales-final p{color:#edf7fb;line-height:1.8}
   @media(max-width:950px){.amanah-seo-sales .amanah-sales-grid{grid-template-columns:1fr 1fr}}
   @media(max-width:650px){.amanah-seo-sales .amanah-sales-grid{grid-template-columns:1fr}.amanah-seo-sales .amanah-sales-card{padding:20px}}
  </style>';
  echo '<div class="amanah-seo-sales">';
  echo '<section class="amanah-sales-hero" aria-labelledby="amanah-sales-title">';
  echo '<p class="amanah-sales-eyebrow">SEO WORKSPACE FOR WORDPRESS · SIAP MENGHADAPI ERA AI</p>';
  echo '<h1 id="amanah-sales-title">'.esc_html__('AI Sudah Mengubah Cara Dunia Mencari Informasi. Apakah Bisnis Anda Siap Ditemukan?','amanah-seo').'</h1>';
  echo '<p><strong>'.esc_html__('Ketika AI mulai menjadi tempat orang mencari jawaban, apakah bisnis Anda akan ikut ditemukan — atau justru semakin tidak terlihat?','amanah-seo').'</strong></p>';
  echo '<p>'.esc_html__('Orang tidak lagi hanya mengetik kata kunci dan membuka daftar tautan. Mereka juga bertanya kepada AI, mencari rekomendasi, membandingkan pilihan, dan mengandalkan jawaban langsung. Website saja bukan jaminan bisnis Anda akan disebut dalam jawaban AI.','amanah-seo').'</p>';
  echo '<p>'.esc_html__('AMANAH SEO membantu Anda membangun fondasi digital dan mengelola alur kerja SEO secara lebih terstruktur, dalam satu platform WordPress dengan modul AI dan ENGINE.','amanah-seo').'</p>';
  echo '<a class="button button-primary amanah-sales-cta" href="#pilih-modul">'.esc_html__('PERSIAPKAN WEBSITE SAYA UNTUK ERA AI','amanah-seo').' ↓</a>';
  echo '</section>';
  echo '<section class="amanah-sales-section" aria-labelledby="amanah-sales-question">';
  echo '<h2 id="amanah-sales-question">'.esc_html__('Saat Orang Bertanya kepada AI, Apakah Bisnis Anda Punya Peluang untuk Ditemukan?','amanah-seo').'</h2>';
  echo '<p class="amanah-sales-lead">'.esc_html__('Identitas bisnis yang jelas, informasi yang konsisten, konten yang relevan, dan fondasi teknis yang baik merupakan bagian dari upaya membangun kehadiran digital. Karena itu, jangan hanya memikirkan artikel berikutnya. Pikirkan juga bagaimana bisnis Anda dipahami, bagaimana konten direncanakan, dan bagaimana pekerjaan SEO dievaluasi.','amanah-seo').'</p>';
  echo '<div class="amanah-sales-note"><strong>'.esc_html__('Jangan tunggu sampai perubahan AI membuat Anda tertinggal.','amanah-seo').'</strong> '.esc_html__('Mulailah menata fondasi digital dan sistem kerja SEO Anda sekarang — tanpa mengandalkan janji peringkat atau rekomendasi AI yang tidak dapat dijamin.','amanah-seo').'</div>';
  echo '</section>';
  echo '<section id="pilih-modul" class="amanah-sales-section" aria-labelledby="amanah-sales-modules">';
  echo '<h2 id="amanah-sales-modules">'.esc_html__('Satu Platform. Pilih Modul yang Anda Butuhkan.','amanah-seo').'</h2>';
  echo '<p class="amanah-sales-lead">'.esc_html__('Mulai dengan AMANAH SEO Core, lalu pilih modul AI, ENGINE, atau keduanya. Masing-masing modul memiliki lisensi tersendiri yang dikelola melalui Settings & Access.','amanah-seo').'</p>';
  echo '<div class="amanah-sales-grid">';
  echo '<article class="amanah-sales-card amanah-sales-card-core"><p style="margin:0 0 8px;color:#68788b;font-size:12px;font-weight:800;letter-spacing:.08em">01 · CORE</p><h3>'.esc_html__('Mulai dengan AMANAH SEO','amanah-seo').'</h3><p>'.esc_html__('Kelola website dan workspace, tinjau temuan, susun pekerjaan, serta atur tindak lanjut melalui area inti AMANAH SEO. Core dapat dipasang tanpa lisensi Core.','amanah-seo').'</p><div class="amanah-sales-card-bottom"><a class="button button-secondary" href="'.esc_url(admin_url('admin.php?page=amanah-seo-workspace')).'">'.esc_html__('Buka Website & Workspace','amanah-seo').'</a></div></article>';
  echo '<article class="amanah-sales-card"><p style="margin:0 0 8px;color:#2271b1;font-size:12px;font-weight:800;letter-spacing:.08em">02 · MODUL AI</p><h3>'.esc_html__('AMANAH SEO AI','amanah-seo').'</h3><p>'.esc_html__('Untuk alur kerja seputar identitas entitas, otoritas dan penulis, analisis serta optimasi konten, topical authority, internal linking, schema, dan laporan AI.','amanah-seo').'</p><p class="description">'.esc_html__('Akses fitur mengikuti implementasi dan lisensi modul AI.','amanah-seo').'</p><div class="amanah-sales-card-bottom"><a class="button button-primary" href="'.esc_url(admin_url('admin.php?page=amanah-seo-ai-tools')).'">'.esc_html__('Jelajahi AMANAH SEO AI','amanah-seo').' →</a></div></article>';
  echo '<article class="amanah-sales-card amanah-sales-card-engine"><p style="margin:0 0 8px;color:#07847f;font-size:12px;font-weight:800;letter-spacing:.08em">03 · MODUL ENGINE</p><h3>'.esc_html__('AMANAH SEO ENGINE','amanah-seo').'</h3><p>'.esc_html__('Untuk alur kerja riset keyword, kebutuhan konten, strategi, Content Brief & Blueprint, evaluasi, penyempurnaan, human review, dan persiapan publikasi.','amanah-seo').'</p><p class="description">'.esc_html__('Akses fitur mengikuti implementasi dan lisensi modul ENGINE.','amanah-seo').'</p><div class="amanah-sales-card-bottom"><a class="button button-primary" style="background:#087e83;border-color:#087e83" href="'.esc_url(admin_url('admin.php?page=amanah-seo-engine-workflow')).'">'.esc_html__('Jelajahi AMANAH SEO ENGINE','amanah-seo').' →</a></div></article>';
  echo '</div>';
  echo '</section>';
  echo '<section class="amanah-sales-final"><h2>'.esc_html__('AI Mengubah Permainan. Persiapkan Sistem Kerja Anda.','amanah-seo').'</h2><p>'.esc_html__('Anda tidak dapat mengendalikan bagaimana setiap sistem AI memilih dan menyajikan informasi, dan penggunaan plugin tidak menjamin bisnis akan direkomendasikan. Namun, Anda dapat memperkuat fondasi digital, meningkatkan kualitas informasi, dan membangun proses SEO yang lebih terarah.','amanah-seo').'</p><a class="button button-primary amanah-sales-cta" href="'.esc_url(admin_url('admin.php?page=amanah-seo-access')).'">'.esc_html__('KELOLA MODUL & LISENSI','amanah-seo').' →</a></section>';
  echo '<p style="color:#646970;font-size:12px;line-height:1.7;margin-top:16px">'.esc_html__('Catatan: kemampuan aktual mengikuti versi plugin dan status implementasi masing-masing fitur. Modul AI dan ENGINE memerlukan lisensi modul masing-masing; AMANAH SEO Core tidak memerlukan lisensi Core.','amanah-seo').'</p>';
  echo '</div>';
  self::end();
 }
 public static function dashboard(){
  self::start('AMANAH SEO — Dashboard');
  $f=AMANAH_SEO_Store::findings(100);
  $t=AMANAH_SEO_Store::tasks(100);
  $events=AMANAH_SEO_Store::events(5);
  $summary=self::dashboard_summary($f,$t);
  $post_counts=wp_count_posts('post');
  $page_counts=wp_count_posts('page');
  $published_posts=is_object($post_counts)&&isset($post_counts->publish)?(int)$post_counts->publish:0;
  $published_pages=is_object($page_counts)&&isset($page_counts->publish)?(int)$page_counts->publish:0;
  $workspace=get_option('amanah_seo_workspace',array());
  $workspace_name=isset($workspace['name'])&&$workspace['name']!==''?$workspace['name']:get_bloginfo('name');
  $workspace_goal=isset($workspace['goal'])?$workspace['goal']:'';
  echo '<p>Ringkasan kondisi dan pekerjaan berdasarkan data yang tersimpan di WordPress. Ringkasan temuan dan pekerjaan mencakup maksimal 100 catatan terbaru untuk masing-masing daftar.</p>';
  echo '<div class="notice notice-info inline"><p><strong>Workspace:</strong> '.esc_html($workspace_name).' &nbsp; <strong>Website:</strong> <a href="'.esc_url(home_url('/')).'" target="_blank" rel="noopener noreferrer">'.esc_html(home_url('/')).'</a> &nbsp; <strong>Konten terbit:</strong> '.esc_html((string)$published_posts).' artikel dan '.esc_html((string)$published_pages).' halaman</p>';
  if($workspace_goal!=='')echo '<p><strong>Tujuan website:</strong> '.esc_html($workspace_goal).'</p>';
  echo '</div>';
  echo '<div class="notice notice-warning inline"><p><strong>Sumber performa pencarian:</strong> Google Search Console belum terhubung. Dashboard ini tidak menampilkan klik, tayangan, CTR, posisi, atau skor SEO yang belum diverifikasi.</p></div>';
  echo '<h2>Ringkasan kerja</h2><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(165px,1fr));gap:12px;max-width:1100px">';
  $cards=array(
   array('Temuan terbuka',$summary['open_findings'],'amanah-seo-findings'),
   array('Temuan prioritas tinggi / mendesak',$summary['priority_findings'],'amanah-seo-findings'),
   array('Pekerjaan aktif',$summary['tasks_active'],'amanah-seo-tasks'),
   array('Sedang berjalan',$summary['tasks_in_progress'],'amanah-seo-tasks'),
   array('Menunggu persetujuan',$summary['tasks_waiting_approval'],'amanah-seo-tasks'),
   array('Terhambat',$summary['tasks_blocked'],'amanah-seo-tasks'),
   array('Gagal',$summary['tasks_failed'],'amanah-seo-tasks'),
   array('Selesai',$summary['tasks_completed'],'amanah-seo-tasks')
  );
  foreach($cards as $card){
   echo '<div style="background:#fff;border:1px solid #c3c4c7;padding:14px;min-height:78px"><p style="margin:0 0 8px"><a href="'.esc_url(admin_url('admin.php?page='.$card[2])).'">'.esc_html($card[0]).'</a></p><p style="font-size:24px;font-weight:600;margin:0">'.esc_html((string)$card[1]).'</p></div>';
  }
  echo '</div>';
  $next=array(
   'review_approval'=>array('Tindakan berikutnya: tinjau persetujuan','Ada pekerjaan yang menunggu keputusan. Buka daftar pekerjaan dan periksa isi serta bukti sebelum menyetujui atau menolak.','amanah-seo-tasks','Tinjau pekerjaan'),
   'resolve_blockers'=>array('Tindakan berikutnya: tangani hambatan atau kegagalan','Periksa pekerjaan terhambat atau gagal, baca penyebabnya, lalu tentukan tindak lanjut manual.','amanah-seo-tasks','Periksa pekerjaan'),
   'prioritize_finding'=>array('Tindakan berikutnya: tinjau temuan prioritas tinggi','Ada temuan terbuka berprioritas tinggi atau mendesak. Periksa bukti dan ketidakpastiannya sebelum membuat pekerjaan.','amanah-seo-findings','Tinjau temuan'),
   'continue_task'=>array('Tindakan berikutnya: lanjutkan pekerjaan berjalan','Tinjau pekerjaan yang sedang berjalan dan perbarui status hanya berdasarkan kemajuan serta bukti yang tersedia.','amanah-seo-tasks','Lanjutkan pekerjaan'),
   'review_findings'=>array('Tindakan berikutnya: tinjau temuan terbuka','Periksa bukti dan prioritas temuan terbuka, lalu buat pekerjaan untuk tindak lanjut yang memang diperlukan.','amanah-seo-findings','Tinjau temuan'),
   'scan_website'=>array('Tindakan berikutnya: periksa kondisi website','Belum ada temuan terbuka yang terdeteksi dalam cakupan daftar saat ini. Jalankan pemeriksaan website/konten atau catat konteks manual untuk menentukan langkah selanjutnya.','amanah-seo-content-checks','Periksa konten')
  );
  $action=$next[$summary['next_action']];
  echo '<div class="notice notice-info" style="margin:18px 0;padding:8px 12px"><h2 style="margin-top:8px">'.esc_html($action[0]).'</h2><p>'.esc_html($action[1]).'</p><p><a class="button button-primary" href="'.esc_url(admin_url('admin.php?page='.$action[2])).'">'.esc_html($action[3]).'</a></p></div>';
  echo '<h2>Temuan yang perlu diperhatikan</h2>';
  $priority_rank=array('urgent'=>0,'high'=>1,'medium'=>2,'low'=>3);
  $visible_findings=array();
  foreach($f as $finding){if(isset($finding->status)&&in_array($finding->status,array('open','in_progress'),true))$visible_findings[]=$finding;}
  usort($visible_findings,function($a,$b)use($priority_rank){$ap=isset($a->priority)&&isset($priority_rank[$a->priority])?$priority_rank[$a->priority]:4;$bp=isset($b->priority)&&isset($priority_rank[$b->priority])?$priority_rank[$b->priority]:4;if($ap===$bp)return (int)$b->id-(int)$a->id;return $ap-$bp;});
  if(!$visible_findings)echo '<p>Tidak ada temuan terbuka pada 100 catatan terbaru. Ini bukan bukti bahwa website bebas masalah.</p>';
  else{
   echo '<table class="widefat striped"><thead><tr><th>Temuan</th><th>Prioritas</th><th>Ketidakpastian</th><th>Sumber</th><th>Tindakan</th></tr></thead><tbody>';
   foreach(array_slice($visible_findings,0,5) as $finding)echo '<tr><td><strong>'.esc_html($finding->title).'</strong><br>'.esc_html($finding->description).'<br><small>'.esc_html($finding->evidence).'</small></td><td>'.esc_html(isset($finding->priority)?$finding->priority:'belum diketahui').'</td><td>'.esc_html(isset($finding->uncertainty)?$finding->uncertainty:'belum diketahui').'</td><td>'.esc_html(isset($finding->source)?$finding->source:'belum diketahui').'</td><td><a href="'.esc_url(admin_url('admin.php?page=amanah-seo-tasks&finding_id='.(int)$finding->id)).'">Buat pekerjaan</a></td></tr>';
   echo '</tbody></table>';
  }
  echo '<h2>Perkembangan pekerjaan</h2>';
  $visible_tasks=array();
  foreach($t as $task){if(isset($task->status)&&!in_array($task->status,array('completed','cancelled'),true))$visible_tasks[]=$task;}
  if(!$visible_tasks)echo '<p>Tidak ada pekerjaan aktif pada 100 catatan terbaru.</p>';
  else{
   echo '<table class="widefat striped"><thead><tr><th>Pekerjaan</th><th>Status</th><th>Temuan</th><th>Tenggat</th><th>Dependensi</th></tr></thead><tbody>';
   foreach(array_slice($visible_tasks,0,5) as $task)echo '<tr><td>'.self::task_title_cell((string)$task->title,(int)$task->id).'</td><td>'.esc_html(isset(self::$statuses[$task->status])?self::$statuses[$task->status]:$task->status).'</td><td>'.esc_html(!empty($task->finding_id)?'#'.$task->finding_id:'—').'</td><td>'.esc_html(!empty($task->due_date)?$task->due_date:'—').'</td><td>'.esc_html(!empty($task->dependency_task_id)?'#'.$task->dependency_task_id:'—').'</td></tr>';
   echo '</tbody></table>';
  }
  echo '<h2>Riwayat terbaru</h2>';
  if(!$events)echo '<p>Belum ada riwayat yang tercatat.</p>';
  else{
   echo '<table class="widefat striped"><thead><tr><th>Waktu</th><th>Objek</th><th>Peristiwa</th><th>Detail</th></tr></thead><tbody>';
   foreach($events as $event)echo '<tr><td>'.esc_html($event->created_at).'</td><td>'.esc_html($event->object_type).' #'.esc_html((string)$event->object_id).'</td><td>'.esc_html($event->event_type).'</td><td>'.esc_html($event->detail).'</td></tr>';
   echo '</tbody></table><p><a href="'.esc_url(admin_url('admin.php?page=amanah-seo-history')).'">Lihat seluruh riwayat</a></p>';
  }
  echo '<h2>Akses cepat</h2><p><a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-workspace')).'">Website &amp; Workspace</a> <a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-findings')).'">Temuan dan Prioritas</a> <a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-tasks')).'">Pekerjaan</a> <a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-history')).'">Riwayat</a> <a class="button" href="'.esc_url(admin_url('admin.php?page=amanah-seo-diagnostics')).'">Diagnostik</a></p>';
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
    echo '<tr><td>'.esc_html((string)$x->id).'</td><td><strong>'.esc_html($x->title).'</strong><br>'.esc_html($x->description).'<br><small>'.esc_html($x->evidence).'</small><br><a href="'.esc_url(add_query_arg(array('page'=>'amanah-seo-tasks','finding_id'=>(int)$x->id),admin_url('admin.php'))).'">Buat pekerjaan dari temuan ini</a></td><td>'.esc_html($x->priority).'</td><td>'.esc_html($x->uncertainty).'</td><td>'.esc_html($x->source).'</td><td>'.esc_html($x->status).'</td><td>';
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
 public static function validated_finding_selection($requested_id,$findings){$requested_id=(int)$requested_id;if($requested_id<1||!is_array($findings))return 0;foreach($findings as $finding){if(is_object($finding)&&isset($finding->id)&&(int)$finding->id===$requested_id)return $requested_id;}return 0;}
 public static function tasks_page(){self::start('Pekerjaan');$findings=AMANAH_SEO_Store::findings(100);$tasks=AMANAH_SEO_Store::tasks();$requested_finding_id=isset($_GET['finding_id'])?(int)$_GET['finding_id']:0;$selected_finding_id=self::validated_finding_selection($requested_finding_id,$findings);echo '<h2 id="task-form">Buat pekerjaan</h2>';if($selected_finding_id){echo '<div class="notice notice-info inline"><p>Pekerjaan ini akan dikaitkan dengan temuan #'.esc_html((string)$selected_finding_id).'.</p></div>';}echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_create_task">';wp_nonce_field('amanah_seo_create_task');echo '<table class="form-table"><tr><th>Judul</th><td><input class="regular-text" name="title" maxlength="255" required></td></tr><tr><th>Temuan terkait</th><td><select name="finding_id"><option value="0">Belum dipilih</option>';foreach($findings as $f)echo '<option value="'.esc_attr((string)$f->id).'"'.selected($selected_finding_id,(int)$f->id,false).'>'.esc_html('#'.$f->id.' — '.$f->title).'</option>';echo '</select></td></tr><tr><th>Deskripsi</th><td><textarea class="large-text" name="description" rows="2" required></textarea></td></tr><tr><th>Penanggung jawab</th><td><input class="regular-text" name="owner"></td></tr><tr><th>Kriteria selesai</th><td><textarea class="large-text" name="success_criteria" rows="2" required></textarea></td></tr><tr><th>Tenggat</th><td><input type="date" name="due_date"></td></tr><tr><th>Dependensi</th><td><select name="dependency_task_id"><option value="0">Tidak ada</option>';foreach($tasks as $dependency_task){echo '<option value="'.esc_attr((string)$dependency_task->id).'">#'.esc_html((string)$dependency_task->id).' — '.esc_html($dependency_task->title).' ('.esc_html($dependency_task->status).')</option>';}echo '</select><p class="description">Pekerjaan ini tidak dapat ditandai selesai sebelum dependensinya selesai.</p></td></tr><tr><th>Status awal</th><td><select name="status">';foreach(self::$statuses as $k=>$v){if('completed'===$k)continue;echo '<option value="'.esc_attr($k).'"'.selected($k,'not_started',false).'>'.esc_html($v).'</option>';}echo '</select><p class="description">Status persetujuan tidak menjalankan tindakan otomatis.</p></td></tr></table>';submit_button('Simpan pekerjaan');echo '</form><hr><h2>Daftar pekerjaan</h2>';if(!$tasks){echo '<p>Belum ada pekerjaan.</p>';}else{echo '<table class="widefat striped"><thead><tr><th>ID</th><th>Pekerjaan</th><th>Temuan</th><th>Pemilik</th><th>Kriteria selesai</th><th>Tenggat</th><th>Dependensi</th><th>Status / persetujuan</th><th>Ubah status / persetujuan</th></tr></thead><tbody>';foreach($tasks as $x){echo '<tr><td>'.esc_html((string)$x->id).'</td><td>'.self::task_title_cell((string)$x->title,(int)$x->id).'<br>'.esc_html($x->description).'</td><td>'.esc_html($x->finding_id?'#'.$x->finding_id:'—').'</td><td>'.esc_html($x->owner?:'Belum ditetapkan').'</td><td>'.esc_html($x->success_criteria).'</td><td>'.esc_html(!empty($x->due_date)?$x->due_date:'—').'</td><td>'.esc_html(!empty($x->dependency_task_id)?'#'.$x->dependency_task_id:'—').'</td><td>'.esc_html(isset(self::$statuses[$x->status])?self::$statuses[$x->status]:$x->status).'<br>Persetujuan: '.esc_html($x->approval_status).(!empty($x->completion_evidence)?'<br>Bukti: '.esc_html($x->completion_evidence):'').'</td><td><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_update_task_status"><input type="hidden" name="task_id" value="'.esc_attr((string)$x->id).'">';wp_nonce_field('amanah_seo_update_task_status_'.$x->id);echo '<select name="status">';foreach(self::$statuses as $k=>$v)echo '<option value="'.esc_attr($k).'"'.selected($k,$x->status,false).'>'.esc_html($v).'</option>';echo '</select><br><textarea name="completion_evidence" rows="2" placeholder="Bukti wajib saat status Selesai"></textarea><br><button class="button" type="submit">Perbarui</button></form>';if('pending'===$x->approval_status){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_decide_approval"><input type="hidden" name="task_id" value="'.esc_attr((string)$x->id).'">';wp_nonce_field('amanah_seo_decide_approval_'.$x->id);echo '<input type="text" name="decision_note" placeholder="Catatan keputusan (opsional)"><br><button class="button button-primary" name="decision" value="approved">Setujui</button> <button class="button" name="decision" value="rejected">Tolak</button></form>';}echo '</td></tr>';}echo '</tbody></table>';}self::end();}
 public static function diagnostics_page(){
  self::start('Diagnostik Sistem');
  global $wpdb;
  $table_names=array('Temuan'=>$wpdb->prefix.'amanah_seo_findings','Pekerjaan'=>$wpdb->prefix.'amanah_seo_tasks','Riwayat'=>$wpdb->prefix.'amanah_seo_events');
  $table_rows=array();
  foreach($table_names as $label=>$table_name){
   $found=$wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$table_name));
   $exists=($found===$table_name);
   $table_rows[]=array($label,$table_name,$exists?'PASS':'GAGAL');
  }
  $schema=get_option('amanah_seo_schema_version','belum diset');
  $schema_ok=('1.2.0'===$schema);
  $wp_version=get_bloginfo('version');
  $db_version=method_exists($wpdb,'db_version')?$wpdb->db_version():'Tidak tersedia';
  $counts=wp_count_posts('post');
  $published=is_object($counts)&&isset($counts->publish)?(int)$counts->publish:0;
  echo '<p>Diagnostik ini hanya membaca status dasar AMANAH SEO. Tidak menampilkan API key, lisensi rahasia, konfigurasi sensitif, atau isi kredensial.</p>';
  echo '<h2>Lingkungan</h2><table class="widefat striped"><tbody>';
  echo '<tr><th>Plugin</th><td>AMANAH SEO '.esc_html(AMANAH_SEO_VERSION).'</td></tr>';
  echo '<tr><th>WordPress</th><td>'.esc_html((string)$wp_version).'</td></tr>';
  echo '<tr><th>PHP</th><td>'.esc_html(PHP_VERSION).'</td></tr>';
  echo '<tr><th>Database</th><td>'.esc_html((string)$db_version).'</td></tr>';
  echo '<tr><th>Website</th><td>'.esc_html(home_url('/')).'</td></tr>';
  echo '<tr><th>Konten terbit</th><td>'.esc_html((string)$published).'</td></tr>';
  echo '<tr><th>Schema Core</th><td>'.esc_html((string)$schema).' — '.($schema_ok?'PASS':'PERLU PEMBARUAN').'</td></tr>';
  echo '</tbody></table><h2>Tabel milik AMANAH SEO</h2><table class="widefat striped"><thead><tr><th>Komponen</th><th>Tabel</th><th>Status</th></tr></thead><tbody>';
  foreach($table_rows as $row){echo '<tr><td>'.esc_html($row[0]).'</td><td><code>'.esc_html($row[1]).'</code></td><td>'.esc_html($row[2]).'</td></tr>';}
  echo '</tbody></table><h2>Integrasi dan modul</h2><table class="widefat striped"><tbody>';
  echo '<tr><th>WordPress internal</th><td>Terbaca pada website tempat plugin ini dipasang; jumlah konten terbit ditampilkan di atas.</td></tr>';
  echo '<tr><th>Google Search Console</th><td>BELUM DIHUBUNGKAN — metrik pencarian tidak tersedia dan tidak dibuat-buat.</td></tr>';
  echo '<tr><th>AMANAH SEO AI</th><td>BELUM DIMIGRASIKAN sebagai modul internal pada paket ini.</td></tr>';
  echo '<tr><th>AMANAH SEO ENGINE</th><td>BELUM DIMIGRASIKAN sebagai modul internal pada paket ini.</td></tr>';
  echo '<tr><th>Lisensi modul</th><td>Integrasi server/entitlement belum tersedia; status lisensi tidak diklaim aktif.</td></tr>';
  echo '<tr><th>Perubahan website otomatis</th><td>Tidak dijalankan. Perubahan tetap memerlukan tindakan dan persetujuan manusia.</td></tr>';
  echo '</tbody></table>';
  echo '<h2>Uji Runtime Core</h2><p>Uji ini membuat catatan sementara untuk memeriksa penyimpanan temuan, status penyelesaian, dependensi, dan persetujuan. Catatan uji serta riwayat terkait dibersihkan setelah pengujian; tidak ada konten website yang diubah.</p>';
  echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="amanah_seo_run_self_test">';
  wp_nonce_field('amanah_seo_run_self_test');
  submit_button('Jalankan uji runtime Core','secondary');
  echo '</form>';
  $test_result=get_transient('amanah_seo_self_test_'.get_current_user_id());
  if(is_array($test_result)){
   $test_failed=isset($test_result['failed'])?(int)$test_result['failed']:1;
   echo '<h3>Hasil uji terakhir</h3><p><strong>Status:</strong> '.($test_failed===0?'PASS':'FAIL').' &nbsp; <strong>Lulus:</strong> '.esc_html((string)$test_result['passed']).' &nbsp; <strong>Gagal:</strong> '.esc_html((string)$test_failed).' &nbsp; <strong>Waktu:</strong> '.esc_html($test_result['time']).'</p>';
   echo '<table class="widefat striped"><thead><tr><th>Status</th><th>Pemeriksaan</th></tr></thead><tbody>';
   foreach($test_result['results'] as $item){echo '<tr><td>'.esc_html($item['status']).'</td><td>'.esc_html($item['label']).'</td></tr>';}
   echo '</tbody></table>';
  }
  self::end();
 }
 public static function run_self_test(){
  self::guard();
  check_admin_referer('amanah_seo_run_self_test');
  global $wpdb;
  $results=array();$passed=0;$failed=0;$finding_id=0;$task_ids=array();
  $check=function($condition,$label) use (&$results,&$passed,&$failed){
   $status=$condition?'PASS':'FAIL';
   $results[]=array('status'=>$status,'label'=>$label);
   if($condition){$passed++;return true;}
   $failed++;
   throw new RuntimeException($label);
  };
  $token=substr(str_replace('-','',wp_generate_uuid4()),0,12);
  try{
   $finding=AMANAH_SEO_Store::create_finding(array('title'=>'[AMANAH SEO SELF TEST '.$token.'] Finding','description'=>'Temporary runtime test record.','source'=>'manual','evidence'=>'Self-test only; not a real SEO finding.','priority'=>'low','uncertainty'=>'low'));
   $check(!is_wp_error($finding)&&is_numeric($finding),'Core can save a finding');
   $finding_id=(int)$finding;
   $finding_status=AMANAH_SEO_Store::update_finding_status($finding_id,'resolved','Temporary self-test resolution.');
   $check(true===$finding_status,'Finding status and resolution note can be saved');

   $expected_due_date=gmdate('Y-m-d');
   $parent=AMANAH_SEO_Store::create_task(array('finding_id'=>$finding_id,'title'=>'[AMANAH SEO SELF TEST '.$token.'] Dependency parent','description'=>'Temporary runtime test record.','owner'=>'Runtime self-test','success_criteria'=>'Self-test only.','due_date'=>$expected_due_date,'dependency_task_id'=>0,'status'=>'in_progress'));
   $check(!is_wp_error($parent)&&is_numeric($parent),'Core can save a task with a deadline');
   $parent_id=(int)$parent;$task_ids[]=$parent_id;
   $stored_due_date=$wpdb->get_var($wpdb->prepare('SELECT due_date FROM '.$wpdb->prefix.'amanah_seo_tasks WHERE id = %d',$parent_id));
   $check($stored_due_date===$expected_due_date,'Task deadline is saved and can be read back exactly');

   $child=AMANAH_SEO_Store::create_task(array('finding_id'=>$finding_id,'title'=>'[AMANAH SEO SELF TEST '.$token.'] Dependency child','description'=>'Temporary runtime test record.','owner'=>'Runtime self-test','success_criteria'=>'Self-test only.','due_date'=>'','dependency_task_id'=>$parent_id,'status'=>'in_progress'));
   $check(!is_wp_error($child)&&is_numeric($child),'Core can save a task dependency');
   $child_id=(int)$child;$task_ids[]=$child_id;

   $blocked=AMANAH_SEO_Store::update_task_status($child_id,'completed','Temporary self-test evidence.');
   $check(is_wp_error($blocked)&&'dependency_incomplete'===$blocked->get_error_code(),'Incomplete dependency blocks task completion');
   $parent_done=AMANAH_SEO_Store::update_task_status($parent_id,'completed','Temporary self-test evidence.');
   $check(true===$parent_done,'Task can complete when evidence is recorded');
   $child_done=AMANAH_SEO_Store::update_task_status($child_id,'completed','Temporary self-test evidence.');
   $check(true===$child_done,'Dependent task completes after its prerequisite');

   $approval=AMANAH_SEO_Store::create_task(array('finding_id'=>$finding_id,'title'=>'[AMANAH SEO SELF TEST '.$token.'] Approval','description'=>'Temporary runtime test record.','owner'=>'Runtime self-test','success_criteria'=>'Self-test only.','due_date'=>'','dependency_task_id'=>0,'status'=>'awaiting_approval'));
   $check(!is_wp_error($approval)&&is_numeric($approval),'Core can create a pending approval request');
   $approval_id=(int)$approval;$task_ids[]=$approval_id;
   $approved=AMANAH_SEO_Store::decide_approval($approval_id,'approved','Temporary self-test approval.');
   $check(true===$approved,'Approval decision is recorded');
   $approval_done=AMANAH_SEO_Store::update_task_status($approval_id,'completed','Temporary self-test evidence.');
   $check(true===$approval_done,'Approved task can complete with evidence');

   $rejected_task=AMANAH_SEO_Store::create_task(array('finding_id'=>$finding_id,'title'=>'[AMANAH SEO SELF TEST '.$token.'] Rejected approval','description'=>'Temporary runtime test record.','owner'=>'Runtime self-test','success_criteria'=>'Self-test only.','due_date'=>'','dependency_task_id'=>0,'status'=>'awaiting_approval'));
   $check(!is_wp_error($rejected_task)&&is_numeric($rejected_task),'Core can create a second approval request');
   $rejected_id=(int)$rejected_task;$task_ids[]=$rejected_id;
   $rejection=AMANAH_SEO_Store::decide_approval($rejected_id,'rejected','Temporary self-test rejection.');
   $check(true===$rejection,'Rejected approval decision is recorded');
   $rejected_done=AMANAH_SEO_Store::update_task_status($rejected_id,'completed','Temporary self-test evidence.');
   $check(is_wp_error($rejected_done)&&'approval_rejected'===$rejected_done->get_error_code(),'Rejected approval prevents task completion');
  }catch(Throwable $error){
   if(0===$failed){$failed++;$results[]=array('status'=>'FAIL','label'=>'Uji runtime terhenti; detail teknis tidak ditampilkan pada halaman.');}
  }
  $cleanup_ok=true;
  $task_table=$wpdb->prefix.'amanah_seo_tasks';
  $event_table=$wpdb->prefix.'amanah_seo_events';
  foreach($task_ids as $task_id){
   $event_delete=$wpdb->delete($event_table,array('object_type'=>'task','object_id'=>$task_id),array('%s','%d'));
   $task_delete=$wpdb->delete($task_table,array('id'=>$task_id),array('%d'));
   if(false===$event_delete||false===$task_delete)$cleanup_ok=false;
   $remaining_task=(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$task_table} WHERE id = %d",$task_id));
   $remaining_events=(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$event_table} WHERE object_type = %s AND object_id = %d",'task',$task_id));
   if($remaining_task!==0||$remaining_events!==0)$cleanup_ok=false;
  }
  if($finding_id>0){
   $event_delete=$wpdb->delete($event_table,array('object_type'=>'finding','object_id'=>$finding_id),array('%s','%d'));
   $finding_delete=$wpdb->delete($wpdb->prefix.'amanah_seo_findings',array('id'=>$finding_id),array('%d'));
   if(false===$event_delete||false===$finding_delete)$cleanup_ok=false;
   $remaining_finding=(int)$wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM '.$wpdb->prefix.'amanah_seo_findings WHERE id = %d',$finding_id));
   $remaining_events=(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$event_table} WHERE object_type = %s AND object_id = %d",'finding',$finding_id));
   if($remaining_finding!==0||$remaining_events!==0)$cleanup_ok=false;
  }
  if($cleanup_ok){$passed++;$results[]=array('status'=>'PASS','label'=>'Catatan uji dan riwayat sementara dihapus dan dipastikan tidak tersisa');}
  else{$failed++;$results[]=array('status'=>'FAIL','label'=>'Pembersihan catatan uji tidak tuntas atau masih ada catatan tersisa; periksa tabel Core sebelum mengulang');}
  set_transient('amanah_seo_self_test_'.get_current_user_id(),array('passed'=>$passed,'failed'=>$failed,'results'=>$results,'time'=>current_time('mysql')),10*MINUTE_IN_SECONDS);
  self::redirect('self_test_complete','amanah-seo-diagnostics');
 }
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
 /**
  * Match a duplicate only when it refers to the same issue code and WordPress post.
  * Different posts can legitimately have the same title and must remain distinct findings.
  */
 public static function is_duplicate_content_issue($existing,$finding_title,$post_id,$code){
  if(!is_object($existing)||!isset($existing->source,$existing->title,$existing->status,$existing->evidence)) return false;
  if($existing->source!=='wordpress'||$existing->title!==$finding_title||!in_array($existing->status,array('open','in_progress'),true)) return false;
  $identity='Pemeriksaan: '.$code.'; konten ID '.(int)$post_id.';';
  return strpos((string)$existing->evidence,$identity)!==false;
 }
 public static function import_content_issue(){
  if(!current_user_can('manage_amanah_seo')) wp_die(esc_html__('You are not allowed to access this page.','amanah-seo'),'',array('response'=>403));
  $post_id=absint($_POST['post_id']??0);
  $code=sanitize_key(wp_unslash($_POST['issue_code']??''));
  check_admin_referer('amanah_seo_import_content_issue_'.$post_id.'_'.$code);
  if(!class_exists('AMANAH_SEO_Content_Checks')||$post_id<1||!in_array($code,array('title_missing','content_empty','title_duplicate'),true)){
   self::redirect('error','amanah-seo-content-checks');
  }
  $scan=AMANAH_SEO_Content_Checks::scan_data();
  $issue=null;
  foreach($scan['issues'] as $candidate){
   if((int)$candidate['post_id']===$post_id&&$candidate['code']===$code){$issue=$candidate;break;}
  }
  if(!$issue){
   self::redirect('content_issue_stale','amanah-seo-content-checks');
  }
  $finding_title='['.$issue['code'].'] '.$issue['title'];
  if(function_exists('mb_substr')) $finding_title=mb_substr($finding_title,0,255,'UTF-8'); else $finding_title=substr($finding_title,0,255);
  foreach(AMANAH_SEO_Store::findings(100) as $existing){
   if(self::is_duplicate_content_issue($existing,$finding_title,$post_id,$code)){
    self::redirect('content_issue_exists','amanah-seo-content-checks');
   }
  }
  $description=$issue['label'].'. '.$issue['suggestion'];
  $evidence='Pemeriksaan: '.$issue['code'].'; konten ID '.$post_id.'; URL '.$issue['url'].'; bukti: '.$issue['evidence'];
  $result=AMANAH_SEO_Store::create_finding(array(
   'title'=>$finding_title,
   'description'=>$description,
   'source'=>'wordpress',
   'evidence'=>$evidence,
   'priority'=>'medium',
   'uncertainty'=>'medium',
  ));
  self::redirect(is_wp_error($result)?'error':'content_issue_imported','amanah-seo-content-checks');
 }
 public static function create_finding(){self::guard();check_admin_referer('amanah_seo_create_finding');$d=array('title'=>sanitize_text_field(wp_unslash($_POST['title']??'')),'description'=>sanitize_textarea_field(wp_unslash($_POST['description']??'')),'source'=>sanitize_key(wp_unslash($_POST['source']??'manual')),'evidence'=>sanitize_textarea_field(wp_unslash($_POST['evidence']??'')),'priority'=>sanitize_key(wp_unslash($_POST['priority']??'medium')),'uncertainty'=>sanitize_key(wp_unslash($_POST['uncertainty']??'medium')));$d['source']=in_array($d['source'],array('manual','wordpress','gsc_unverified'),true)?$d['source']:'manual';$d['priority']=in_array($d['priority'],array('low','medium','high','urgent'),true)?$d['priority']:'medium';$d['uncertainty']=in_array($d['uncertainty'],array('low','medium','high','unknown'),true)?$d['uncertainty']:'medium';$r=($d['title']&&$d['description']&&$d['evidence'])?AMANAH_SEO_Store::create_finding($d):new WP_Error('invalid','Required fields missing');self::redirect(is_wp_error($r)?'error':'saved','amanah-seo-findings');}
 public static function create_task(){self::guard();check_admin_referer('amanah_seo_create_task');$normalized=self::normalize_task_input($_POST);if(is_wp_error($normalized))self::redirect(in_array($normalized->get_error_code(),array('task_field_too_long','suspicious_task_title'),true)?$normalized->get_error_code():'invalid_task_input','amanah-seo-tasks');$d=$normalized;$d['finding_id']=absint($_POST['finding_id']??0);$d['due_date']=sanitize_text_field(wp_unslash($_POST['due_date']??''));$d['dependency_task_id']=absint($_POST['dependency_task_id']??0);$d['status']=sanitize_key(wp_unslash($_POST['status']??'not_started'));if(!array_key_exists($d['status'],self::$statuses))$d['status']='not_started';if($d['finding_id']&&!self::finding_exists($d['finding_id']))$d['finding_id']=0;$r=AMANAH_SEO_Store::create_task($d);$notice=is_wp_error($r)?(in_array($r->get_error_code(),array('invalid_due_date','dependency_not_found'),true)?$r->get_error_code():'error'):'saved';self::redirect($notice,'amanah-seo-tasks');}
 private static function finding_exists($id){global $wpdb;$table=$wpdb->prefix.'amanah_seo_findings';return (bool)$wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE id = %d",$id));}
 public static function update_task_status(){self::guard();$id=absint($_POST['task_id']??0);check_admin_referer('amanah_seo_update_task_status_'.$id);$status=sanitize_key(wp_unslash($_POST['status']??''));$evidence=sanitize_textarea_field(wp_unslash($_POST['completion_evidence']??''));$r=AMANAH_SEO_Store::update_task_status($id,$status,$evidence);$notice=is_wp_error($r)?('completion_evidence_required'===$r->get_error_code()?'evidence_required':('approval_pending'===$r->get_error_code()?'approval_pending':('approval_rejected'===$r->get_error_code()?'approval_rejected':('dependency_incomplete'===$r->get_error_code()?'dependency_incomplete':'error')))):'updated';self::redirect($notice,'amanah-seo-tasks');}
 public static function decide_approval(){self::guard();$id=absint($_POST['task_id']??0);check_admin_referer('amanah_seo_decide_approval_'.$id);$decision=sanitize_key(wp_unslash($_POST['decision']??''));$note=sanitize_textarea_field(wp_unslash($_POST['decision_note']??''));$r=AMANAH_SEO_Store::decide_approval($id,$decision,$note);self::redirect(is_wp_error($r)?'error':'decision','amanah-seo-tasks');}
 private static function redirect($notice,$page){wp_safe_redirect(add_query_arg(array('page'=>$page,'amanah_notice'=>$notice),admin_url('admin.php')));exit;}
}


/**
 * AMANAH SEO module navigation shell.
 *
 * Navigation and truthful placeholders only. No AI provider calls, license
 * activation, entity persistence, Engine execution, or publishing are performed.
 */
final class AMANAH_SEO_Module_Navigation {
 private const CAPABILITY='manage_amanah_seo';
 public static function menu(){
  if(!current_user_can(self::CAPABILITY)) return;
  // AI and ENGINE dashboards are registered as submenus of the AMANAH SEO parent.

  add_submenu_page('amanah-seo-core','SEO Intelligence','SEO Intelligence',self::CAPABILITY,'amanah-seo-intelligence',array(__CLASS__,'core_placeholder'));
  add_submenu_page('amanah-seo-core','Execution & Approval','Execution & Approval',self::CAPABILITY,'amanah-seo-execution',array(__CLASS__,'core_placeholder'));
  add_submenu_page('amanah-seo-core','Monitoring & Results','Monitoring & Results',self::CAPABILITY,'amanah-seo-monitoring',array(__CLASS__,'core_placeholder'));
  add_submenu_page('amanah-seo-core','Reports','Reports',self::CAPABILITY,'amanah-seo-reports',array(__CLASS__,'core_placeholder'));
  $routes=array(
   'amanah-entity-profile'=>'Entity Profile','amanah-seo-ai'=>'Entity Profile','amanah-authority'=>'Authority & Author','amanah-seo-ai-audit'=>'AI Audit',
   'amanah-content-analyzer'=>'Content Analyzer','amanah-seo-ai-brief'=>'Content Brief','amanah-seo-ai-article-generation'=>'Article Generation',
   'amanah-seo-ai-faq-generator'=>'FAQ Generator','amanah-seo-ai-content-optimizer'=>'Content Optimizer',
   'amanah-seo-ai-topic-authority'=>'Topic Authority','amanah-seo-ai-internal-links'=>'Internal Link AI',
   'amanah-seo-ai-schema-generator'=>'Schema Generator','amanah-seo-ai-schema-validation'=>'Schema Validation',
   'amanah-seo-ai-reports'=>'AI Reports',
   'amanah-seo-engine-keywords'=>'Keyword & Search Intelligence','amanah-seo-engine-requirements'=>'Requirements',
   'amanah-seo-engine-strategy'=>'Strategy & Planning','amanah-seo-engine-brief'=>'Content Brief & Blueprint',
   'amanah-seo-engine-generation'=>'Generation & Evaluation','amanah-seo-engine-refinement'=>'Refinement & Maturity',
   'amanah-seo-engine-review'=>'Human Review & Approval','amanah-seo-engine-publishing'=>'Publishing Preparation'
  );
  foreach($routes as $slug=>$title) add_submenu_page(null,$title,$title,self::CAPABILITY,$slug,array(__CLASS__,'module_placeholder'));
 }
 private static function start($title){
  if(!current_user_can(self::CAPABILITY)) wp_die(esc_html__('Anda tidak memiliki izin untuk mengakses halaman ini.','amanah-seo'),esc_html__('Akses ditolak','amanah-seo'),array('response'=>403));
  echo '<div class="wrap"><h1>'.esc_html($title).'</h1>';
 }
 private static function end(){echo '</div>';}
 public static function core_placeholder(){
  $page=isset($_GET['page'])&&is_string($_GET['page'])?sanitize_key(wp_unslash($_GET['page'])):'';
  $titles=array('amanah-seo-intelligence'=>'SEO Intelligence','amanah-seo-execution'=>'Execution & Approval','amanah-seo-monitoring'=>'Monitoring & Results','amanah-seo-reports'=>'Reports','amanah-seo-settings-access'=>'Settings & Access');
  self::start($titles[$page]??'AMANAH SEO');
  echo '<div class="notice notice-info inline"><p>'.esc_html__('Bagian ini belum diimplementasikan. Tidak ada data, integrasi, lisensi, atau hasil yang disimulasikan.','amanah-seo').'</p></div>';
  self::end();
 }
 public static function module_placeholder(){
  $page=isset($_GET['page'])&&is_string($_GET['page'])?sanitize_key(wp_unslash($_GET['page'])):'';
  $titles=array('amanah-entity-profile'=>'Entity Profile','amanah-seo-ai'=>'Entity Profile','amanah-authority'=>'Authority & Author','amanah-seo-ai-audit'=>'AI Audit','amanah-content-analyzer'=>'Content Analyzer','amanah-seo-ai-brief'=>'Content Brief','amanah-seo-ai-article-generation'=>'Article Generation','amanah-seo-ai-faq-generator'=>'FAQ Generator','amanah-seo-ai-content-optimizer'=>'Content Optimizer','amanah-seo-ai-topic-authority'=>'Topic Authority','amanah-seo-ai-internal-links'=>'Internal Link AI','amanah-seo-ai-schema-generator'=>'Schema Generator','amanah-seo-ai-schema-validation'=>'Schema Validation','amanah-seo-ai-reports'=>'AI Reports','amanah-seo-engine-keywords'=>'Keyword & Search Intelligence','amanah-seo-engine-requirements'=>'Requirements','amanah-seo-engine-strategy'=>'Strategy & Planning','amanah-seo-engine-brief'=>'Content Brief & Blueprint','amanah-seo-engine-generation'=>'Generation & Evaluation','amanah-seo-engine-refinement'=>'Refinement & Maturity','amanah-seo-engine-review'=>'Human Review & Approval','amanah-seo-engine-publishing'=>'Publishing Preparation');
  self::start($titles[$page]??'AMANAH SEO AI');
  if(in_array($page,array('amanah-entity-profile','amanah-seo-ai'),true)){
   echo '<div class="notice notice-info inline"><p>'.esc_html__('Rute Entity Profile sekarang ditangani oleh AMANAH SEO Core. Integrasi layar dan penyimpanan entitas lama belum dipindahkan; data yang sudah ada tidak dihapus atau diubah.','amanah-seo').'</p></div>';
   if(isset($_GET['entity_id'])&&is_scalar($_GET['entity_id'])) echo '<p>'.esc_html(sprintf(__('Permintaan profil entitas ID %d diterima oleh rute AMANAH SEO.','amanah-seo'),absint($_GET['entity_id']))).'</p>';
  }else echo '<div class="notice notice-info inline"><p>'.esc_html__('Halaman ini merupakan placeholder. Integrasi fitur belum tersedia dan belum ada proses AI yang dijalankan.','amanah-seo').'</p></div>';
  self::end();
 }
 public static function core_dashboard(){
  self::start('CORE');
  echo '<p class="description" style="max-width:900px">Kelola workspace, temuan, pekerjaan, dan pengaturan inti AMANAH SEO dari satu area.</p>';
  $cards=array(
   array('group'=>'Overview','slug'=>'amanah-seo-dashboard','title'=>'Dashboard','description'=>'Ringkasan temuan, pekerjaan, dan tindakan berikutnya.','status'=>'Area tersedia'),
   array('group'=>'Workspace','slug'=>'amanah-seo-workspace','title'=>'Website & Workspace','description'=>'Identitas website dan konteks kerja utama.','status'=>'Area tersedia'),
   array('group'=>'SEO Intelligence','slug'=>'amanah-seo-intelligence','title'=>'SEO Intelligence','description'=>'Tempat untuk ringkasan intelijen dan peluang SEO.','status'=>'Belum diimplementasikan'),
   array('group'=>'Tasks & Workflow','slug'=>'amanah-seo-tasks','title'=>'Tasks & Workflow','description'=>'Kelola pekerjaan dan tindak lanjut yang tercatat.','status'=>'Area tersedia'),
   array('group'=>'Execution & Approval','slug'=>'amanah-seo-execution','title'=>'Execution & Approval','description'=>'Area untuk proses pelaksanaan dan persetujuan.','status'=>'Belum diimplementasikan'),
   array('group'=>'Monitoring','slug'=>'amanah-seo-monitoring','title'=>'Monitoring & Results','description'=>'Area pemantauan dan hasil yang terverifikasi.','status'=>'Belum diimplementasikan'),
   array('group'=>'Reports','slug'=>'amanah-seo-reports','title'=>'Reports','description'=>'Area laporan dan ringkasan hasil.','status'=>'Belum diimplementasikan')
  );
  echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr));gap:12px;max-width:1180px;align-items:stretch">';
  foreach($cards as $card){
   echo '<article class="postbox" style="margin:0;padding:16px;display:flex;flex-direction:column;min-width:0">';
   echo '<p style="margin:0 0 6px;color:#646970;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em">'.esc_html($card['group']).'</p>';
   echo '<h3 style="margin:0 0 8px;font-size:15px">'.esc_html($card['title']).'</h3>';
   echo '<p style="margin:0 0 12px;flex:1">'.esc_html($card['description']).'</p>';
   echo '<p style="margin:0 0 10px"><span class="description">'.esc_html($card['status']).'</span></p>';
   echo '<p style="margin:0"><a class="button button-secondary" href="'.esc_url(admin_url('admin.php?page='.$card['slug'])).'">'.esc_html__('Buka area','amanah-seo').'</a></p>';
   echo '</article>';
  }
  echo '</div>';
  self::end();
 }
 public static function ai_dashboard(){
  self::start('AMANAH SEO AI');
  echo '<style>
   .amanah-seo-ai-dashboard{width:100%;max-width:none;box-sizing:border-box}
   .amanah-seo-ai-hero{box-sizing:border-box;width:100%;margin:16px 0 24px;padding:28px 32px;background:#eaf4ff;border:1px solid #b8d8f5;border-left:5px solid #2271b1;border-radius:8px;line-height:1.7}
   .amanah-seo-ai-hero h2{font-size:clamp(22px,2vw,30px);line-height:1.25;margin:0 0 14px;color:#123b61}
   .amanah-seo-ai-hero p{font-size:15px;line-height:1.75;margin:0 0 12px}
   .amanah-seo-ai-hero p:last-child{margin-bottom:0}
   .amanah-seo-ai-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;width:100%;align-items:stretch}
   .amanah-seo-ai-card{box-sizing:border-box;min-width:0;margin:0;padding:18px;display:flex;flex-direction:column;height:100%;border:1px solid #dcdcde;border-top:3px solid #2271b1;border-radius:6px;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.04)}
   .amanah-seo-ai-card .amanah-seo-ai-card-description{flex:1;margin:0 0 16px;overflow-wrap:anywhere}
   .amanah-seo-ai-card .amanah-seo-ai-card-status{margin:0 0 14px;font-size:12px;color:#646970}
   .amanah-seo-ai-card .amanah-seo-ai-card-actions{margin:0}
   @media(max-width:1500px){.amanah-seo-ai-grid{grid-template-columns:repeat(4,minmax(0,1fr))}}
   @media(max-width:1200px){.amanah-seo-ai-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
   @media(max-width:900px){.amanah-seo-ai-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
   @media(max-width:782px){.amanah-seo-ai-hero{padding:22px 20px}.amanah-seo-ai-grid{grid-template-columns:minmax(0,1fr)}}
  </style>';
  echo '<div class="amanah-seo-ai-dashboard">';
  echo '<section class="amanah-seo-ai-hero" aria-labelledby="amanah-seo-ai-hero-title">';
  echo '<h2 id="amanah-seo-ai-hero-title">'.esc_html__('Jika AI belum mengenal bisnis Anda, bagaimana calon pelanggan akan menemukannya?','amanah-seo').'</h2>';
  echo '<p>'.esc_html__('Pencarian berkembang melampaui daftar tautan. Identitas bisnis yang jelas, otoritas yang dapat dipercaya, dan konten yang relevan membantu mesin pencari serta sistem AI memahami siapa Anda, apa keahlian Anda, dan mengapa bisnis Anda layak dipertimbangkan.','amanah-seo').'</p>';
  echo '<p><strong>'.esc_html__('AMANAH SEO AI dirancang untuk membantu membangun fondasi tersebut sebagai bagian dari satu sistem SEO terpadu — bukan sekadar menghasilkan konten, tetapi mendukung upaya agar bisnis Anda lebih mudah dipahami dan berpeluang ditemukan dalam ekosistem pencarian yang terus berkembang.','amanah-seo').'</strong></p>';
  echo '</section>';
  echo '<section aria-labelledby="amanah-seo-ai-capabilities-title">';
  echo '<h2 id="amanah-seo-ai-capabilities-title" style="margin:0 0 6px">'.esc_html__('Kemampuan AI','amanah-seo').'</h2>';
  echo '<p class="description" style="margin:0 0 18px">'.esc_html__('Pilih area kerja yang dibutuhkan. Halaman yang belum terintegrasi akan menampilkan status placeholder dengan jelas.','amanah-seo').'</p>';
  $cards=array(
   array('group'=>'Entity & Authority','slug'=>'amanah-entity-profile','title'=>'Entity Profile','description'=>'Identitas dan konteks utama bisnis.'),
   array('group'=>'Entity & Authority','slug'=>'amanah-authority','title'=>'Authority & Author','description'=>'Kredibilitas penulis dan keahlian.'),
   array('group'=>'Intelligence','slug'=>'amanah-seo-ai-audit','title'=>'AI Audit','description'=>'Area audit SEO berbantuan AI.'),
   array('group'=>'Content AI','slug'=>'amanah-content-analyzer','title'=>'Content Analyzer','description'=>'Analisis kualitas dan konteks konten.'),
   array('group'=>'Content AI','slug'=>'amanah-seo-ai-brief','title'=>'Content Brief','description'=>'Susun kerangka dan kebutuhan konten sebelum penulisan.'),
   array('group'=>'Content AI','slug'=>'amanah-seo-ai-article-generation','title'=>'Article Generation','description'=>'Area persiapan dan pembuatan artikel dalam alur kerja AI.'),
   array('group'=>'Content AI','slug'=>'amanah-seo-ai-faq-generator','title'=>'FAQ Generator','description'=>'Perencanaan pertanyaan dan jawaban.'),
   array('group'=>'Content AI','slug'=>'amanah-seo-ai-content-optimizer','title'=>'Content Optimizer','description'=>'Penyempurnaan konten yang sudah ada.'),
   array('group'=>'Authority','slug'=>'amanah-seo-ai-topic-authority','title'=>'Topic Authority','description'=>'Cakupan topik dan kedalaman keahlian.'),
   array('group'=>'Technical SEO','slug'=>'amanah-seo-ai-internal-links','title'=>'Internal Link AI','description'=>'Dukungan perencanaan tautan internal.'),
   array('group'=>'Technical SEO','slug'=>'amanah-seo-ai-schema-generator','title'=>'Schema Generator','description'=>'Pembuatan structured data schema.'),
   array('group'=>'Technical SEO','slug'=>'amanah-seo-ai-schema-validation','title'=>'Schema Validation','description'=>'Pemeriksaan validitas structured data schema.'),
   array('group'=>'Reporting','slug'=>'amanah-seo-ai-reports','title'=>'AI Reports','description'=>'Area laporan terkait analisis AI.')
  );
  echo '<div class="amanah-seo-ai-grid">';
  foreach($cards as $card){
   echo '<article class="amanah-seo-ai-card">';
   echo '<p style="margin:0 0 8px;color:#646970;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em">'.esc_html($card['group']).'</p>';
   echo '<h3 style="margin:0 0 10px;font-size:16px;line-height:1.4">'.esc_html($card['title']).'</h3>';
   echo '<p class="amanah-seo-ai-card-description">'.esc_html($card['description']).'</p>';
   echo '<p class="amanah-seo-ai-card-status">'.esc_html__('Belum diimplementasikan','amanah-seo').'</p>';
   echo '<p class="amanah-seo-ai-card-actions"><a class="button button-secondary" href="'.esc_url(admin_url('admin.php?page='.$card['slug'])).'">'.esc_html__('Buka area','amanah-seo').'</a>';
   if('amanah-seo-ai-schema-generator'===$card['slug']) echo ' <a class="button button-link" href="'.esc_url(admin_url('admin.php?page=amanah-seo-ai-schema-validation')).'">'.esc_html__('Validasi','amanah-seo').'</a>';
   echo '</p></article>';
  }
  echo '</div></section></div>';
  self::end();
 }
 public static function engine_dashboard(){
  self::start('AMANAH SEO ENGINE');
  echo '<p class="description" style="max-width:900px">Alur kerja SEO terstruktur dari riset hingga persiapan publikasi. Area yang belum terintegrasi ditandai dengan jelas sebagai placeholder.</p>';
  $cards=array(
   array('group'=>'Keyword Intelligence','slug'=>'amanah-seo-engine-keywords','title'=>'Keyword & Search Intelligence','description'=>'Riset keyword, search intent, dan pengelompokan peluang.'),
   array('group'=>'Requirements','slug'=>'amanah-seo-engine-requirements','title'=>'Requirements','description'=>'Kebutuhan dan batasan sebelum strategi disusun.'),
   array('group'=>'Strategy & Planning','slug'=>'amanah-seo-engine-strategy','title'=>'Strategy & Planning','description'=>'Rencana SEO dan prioritas kerja yang terstruktur.'),
   array('group'=>'Content Workflow','slug'=>'amanah-seo-engine-brief','title'=>'Content Brief & Blueprint','description'=>'Kerangka konten sebelum proses produksi.'),
   array('group'=>'Content Workflow','slug'=>'amanah-seo-engine-generation','title'=>'Generation & Evaluation','description'=>'Tahap produksi dan evaluasi konten.'),
   array('group'=>'Content Workflow','slug'=>'amanah-seo-engine-refinement','title'=>'Refinement & Maturity','description'=>'Penyempurnaan dan penilaian kesiapan konten.'),
   array('group'=>'Review & Approval','slug'=>'amanah-seo-engine-review','title'=>'Human Review & Approval','description'=>'Pemeriksaan manusia dan persetujuan sebelum publikasi.'),
   array('group'=>'Publishing Preparation','slug'=>'amanah-seo-engine-publishing','title'=>'Publishing Preparation','description'=>'Persiapan publikasi tanpa menerbitkan otomatis.')
  );
  echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr));gap:12px;max-width:1180px;align-items:stretch">';
  foreach($cards as $card){
   echo '<article class="postbox" style="margin:0;padding:16px;display:flex;flex-direction:column;min-width:0">';
   echo '<p style="margin:0 0 6px;color:#646970;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.04em">'.esc_html($card['group']).'</p>';
   echo '<h3 style="margin:0 0 8px;font-size:15px">'.esc_html($card['title']).'</h3>';
   echo '<p style="margin:0 0 12px;flex:1">'.esc_html($card['description']).'</p>';
   echo '<p style="margin:0 0 10px"><span class="description">'.esc_html__('Belum diimplementasikan','amanah-seo').'</span></p>';
   echo '<p style="margin:0"><a class="button button-secondary" href="'.esc_url(admin_url('admin.php?page='.$card['slug'])).'">'.esc_html__('Buka area','amanah-seo').'</a></p>';
   echo '</article>';
  }
  echo '</div>';
  self::end();
 }
}