<?php
/**
 * Plugin Name: AMANAH SEO
 * Description: Kerangka navigasi dan alur kerja admin AMANAH SEO. Halaman yang belum tersedia ditampilkan sebagai placeholder secara eksplisit.
 * Version: 0.1.0
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * Author: AMANAH GROUP
 * License: GPL-2.0-or-later
 * Text Domain: amanah-seo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the AMANAH SEO navigation shell.
 *
 * This initial package intentionally provides navigation and honest placeholder
 * pages only. It does not scan websites, change SEO settings, call AI/ENGINE,
 * write business data, or publish content.
 */
final class AMANAH_SEO_Menu_Shell {

	/**
	 * Page slug => page title and explanation.
	 *
	 * @var array<string, array{title:string, description:string}>
	 */
	private const PAGES = array(
		'amanah-seo-dashboard' => array(
			'title' => 'Overview',
			'description' => 'Ringkasan kondisi website, prioritas, pekerjaan tertunda, persetujuan, dan hasil akan ditampilkan di sini setelah fungsi terkait diimplementasikan.',
		),
		'amanah-seo-websites' => array(
			'title' => 'Website & Workspace',
			'description' => 'Pengelolaan website dan ruang kerja belum diimplementasikan.',
		),
		'amanah-seo-intelligence' => array(
			'title' => 'SEO Intelligence',
			'description' => 'Status SEO, pemeriksaan website, temuan, peluang pertumbuhan, sumber data, dan tingkat keyakinan belum diimplementasikan.',
		),
		'amanah-seo-priorities' => array(
			'title' => 'Priorities & Tasks',
			'description' => 'Prioritas, alasan rekomendasi, tugas, penanggung jawab, status, tenggat, dan dependensi belum diimplementasikan.',
		),
		'amanah-seo-execution' => array(
			'title' => 'Execution & Approval',
			'description' => 'Rencana pelaksanaan, antrean persetujuan, riwayat keputusan, dan log perubahan/hasil belum diimplementasikan.',
		),
		'amanah-seo-monitoring' => array(
			'title' => 'Monitoring & Results',
			'description' => 'Pemantauan pekerjaan, perubahan SEO, perbandingan sebelum/sesudah, dan siklus evaluasi berikutnya belum diimplementasikan.',
		),
		'amanah-seo-reports' => array(
			'title' => 'Reports',
			'description' => 'Laporan belum diimplementasikan.',
		),
		'amanah-seo-ai-tools' => array(
			'title' => 'AMANAH SEO AI',
			'description' => 'Pusat navigasi untuk semua fitur AMANAH SEO AI.',
		),
		'amanah-seo-engine-workflow' => array(
			'title' => 'ENGINE Workflow',
			'description' => 'Pusat navigasi untuk delapan tahap AMANAH SEO ENGINE V2.',
		),
		'amanah-seo-ai-settings' => array(
			'title' => 'AI Settings',
			'description' => 'Pengaturan AMANAH SEO AI.',
		),
		'amanah-content-analyzer' => array(
			'title' => 'Content Analyzer',
			'description' => 'Content Analyzer merupakan bagian produk AMANAH SEO AI.',
		),
		'amanah-entity-profile' => array(
			'title' => 'Entity Profile',
			'description' => 'Entity Profile merupakan bagian produk AMANAH SEO AI.',
		),
		'amanah-authority' => array(
			'title' => 'Authority & Author',
			'description' => 'Authority & Author merupakan bagian produk AMANAH SEO AI.',
		),
		'amanah-content-generation' => array(
			'title' => 'Article Generation',
			'description' => 'Article Generation merupakan bagian produk AMANAH SEO AI.',
		),
		'amanah-seo-ai-audit' => array(
			'title' => 'AI Audit',
			'description' => 'Halaman AI Audit belum diintegrasikan; belum ada audit yang dijalankan.',
		),
		'amanah-seo-ai-topic-authority' => array(
			'title' => 'Topic Authority',
			'description' => 'Halaman Topic Authority belum diintegrasikan.',
		),
		'amanah-seo-ai-content-brief' => array(
			'title' => 'Content Brief',
			'description' => 'Halaman Content Brief belum diintegrasikan.',
		),
		'amanah-seo-ai-faq-generator' => array(
			'title' => 'FAQ Generator',
			'description' => 'Halaman FAQ Generator belum diintegrasikan.',
		),
		'amanah-seo-ai-content-optimizer' => array(
			'title' => 'Content Optimizer',
			'description' => 'Halaman Content Optimizer belum diintegrasikan.',
		),
		'amanah-seo-ai-internal-links' => array(
			'title' => 'Internal Link AI',
			'description' => 'Halaman Internal Link AI belum diintegrasikan.',
		),
		'amanah-seo-ai-schema-generator' => array(
			'title' => 'Schema Generator',
			'description' => 'Halaman Schema Generator belum diintegrasikan.',
		),
		'amanah-seo-ai-schema-validation' => array(
			'title' => 'Schema Validation',
			'description' => 'Halaman Schema Validation belum diintegrasikan.',
		),
		'amanah-seo-ai-reports' => array(
			'title' => 'AI Reports',
			'description' => 'Halaman AI Reports belum diintegrasikan.',
		),
		'amanah-seo-ai-license' => array(
			'title' => 'License & Entitlement AI',
			'description' => 'Halaman License & Entitlement AI belum diintegrasikan.',
		),
		'amanah-seo-engine-keyword-research' => array(
			'title' => 'Keyword Research',
			'description' => 'Tahap Keyword Research pada AMANAH SEO ENGINE V2 belum diintegrasikan.',
		),
		'amanah-seo-engine-intelligence' => array(
			'title' => 'Intelligence',
			'description' => 'Tahap Intelligence pada AMANAH SEO ENGINE V2 belum diintegrasikan.',
		),
		'amanah-seo-engine-requirements' => array(
			'title' => 'Requirements',
			'description' => 'Tahap Requirements pada AMANAH SEO ENGINE V2 belum diintegrasikan.',
		),
		'amanah-seo-engine-strategy-plan' => array(
			'title' => 'Strategy & Plan',
			'description' => 'Tahap Strategy & Plan pada AMANAH SEO ENGINE V2 belum diintegrasikan.',
		),
		'amanah-seo-engine-brief-blueprint' => array(
			'title' => 'Brief & Blueprint',
			'description' => 'Tahap Brief & Blueprint pada AMANAH SEO ENGINE V2 belum diintegrasikan.',
		),
		'amanah-seo-engine-generation-evaluation' => array(
			'title' => 'Generation & Evaluation',
			'description' => 'Tahap Generation & Evaluation pada AMANAH SEO ENGINE V2 belum diintegrasikan.',
		),
		'amanah-seo-engine-refinement-maturity' => array(
			'title' => 'Refinement & Maturity',
			'description' => 'Tahap Refinement & Maturity pada AMANAH SEO ENGINE V2 belum diintegrasikan.',
		),
		'amanah-seo-engine-review-publishing' => array(
			'title' => 'Review & Publishing',
			'description' => 'Tahap Review & Publishing pada AMANAH SEO ENGINE V2 belum diintegrasikan; tidak ada konten yang dipublikasikan.',
		),
		'amanah-seo-settings' => array(
			'title' => 'Settings & Access',
			'description' => 'Pengaturan workspace, pengguna/hak akses khusus, integrasi/sumber data, dan preferensi produk belum diimplementasikan.',
		),
	);

	/**
	 * Hook menu registration.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_menu_page(
			'AMANAH SEO',
			'AMANAH SEO',
			'manage_options',
			'amanah-seo-dashboard',
			array( self::class, 'render_page' ),
			'dashicons-chart-area',
			58
		);


		$hidden_ai_pages = array(
			'amanah-seo-ai-settings',
			'amanah-content-analyzer',
			'amanah-entity-profile',
			'amanah-authority',
			'amanah-content-generation',
			'amanah-seo-ai-audit',
			'amanah-seo-ai-topic-authority',
			'amanah-seo-ai-content-brief',
			'amanah-seo-ai-faq-generator',
			'amanah-seo-ai-content-optimizer',
			'amanah-seo-ai-internal-links',
			'amanah-seo-ai-schema-generator',
			'amanah-seo-ai-schema-validation',
			'amanah-seo-ai-reports',
			'amanah-seo-ai-license',
		);
		$companion_ai_pages = array(
			'amanah-seo-ai-settings',
			'amanah-content-analyzer',
			'amanah-entity-profile',
			'amanah-authority',
			'amanah-content-generation',
		);
		$hidden_engine_pages = array(
			'amanah-seo-engine-keyword-research',
			'amanah-seo-engine-intelligence',
			'amanah-seo-engine-requirements',
			'amanah-seo-engine-strategy-plan',
			'amanah-seo-engine-brief-blueprint',
			'amanah-seo-engine-generation-evaluation',
			'amanah-seo-engine-refinement-maturity',
			'amanah-seo-engine-review-publishing',
		);

		foreach ( self::PAGES as $slug => $page ) {
			if ( 'amanah-seo-dashboard' === $slug ) {
				continue;
			}

			if ( in_array( $slug, $companion_ai_pages, true ) && class_exists( 'AMANAH_SEO_AI_Admin_Menu' ) ) {
				// The AI companion registers its own protected, hidden screens.
				continue;
			}

			if ( in_array( $slug, $hidden_ai_pages, true ) || in_array( $slug, $hidden_engine_pages, true ) ) {
				// Keep feature routes available without cluttering the sidebar.
				add_submenu_page( '', $page['title'], $page['title'], 'manage_options', $slug, array( self::class, 'render_page' ) );
				continue;
			}

			add_submenu_page(
				'amanah-seo-dashboard',
				$page['title'] . ' — AMANAH SEO',
				$page['title'],
				'manage_options',
				$slug,
				array( self::class, 'render_page' )
			);
		}
	}

	/**
	 * Render a safe, static placeholder page.
	 *
	 * @return void
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'Anda tidak memiliki izin untuk mengakses halaman ini.', 'amanah-seo' ),
				esc_html__( 'Akses ditolak', 'amanah-seo' ),
				array( 'response' => 403 )
			);
		}

		$requested_page = isset( $_GET['page'] ) && is_string( $_GET['page'] )
			? sanitize_key( wp_unslash( $_GET['page'] ) )
			: 'amanah-seo-dashboard';

		if ( ! isset( self::PAGES[ $requested_page ] ) ) {
			wp_die(
				esc_html__( 'Halaman AMANAH SEO tidak ditemukan.', 'amanah-seo' ),
				esc_html__( 'Halaman tidak ditemukan', 'amanah-seo' ),
				array( 'response' => 404 )
			);
		}

		$page = self::PAGES[ $requested_page ];

		if ( 'amanah-seo-ai-tools' === $requested_page ) {
			$ai_pages = array(
				'amanah-entity-profile' => array(
					'title' => 'Entity Profile',
					'description' => 'Mengelola profil entitas, informasi bisnis, produk, layanan, serta konteks yang menjadi dasar AI dalam memahami apa bisnis Anda, apa yang Anda tawarkan, dan bidang apa yang Anda kuasai.',
					'action' => 'Kelola Profil Entitas',
				),
				'amanah-authority' => array(
					'title' => 'Authority & Author',
					'description' => 'Mengelola informasi penulis dan elemen otoritas untuk memperjelas keahlian, kredibilitas, serta keterkaitan penulis dengan topik yang dibahas.',
					'action' => 'Kelola Otoritas & Penulis',
				),
				'amanah-content-analyzer' => array(
					'title' => 'Content Analyzer',
					'description' => 'Menganalisis judul, isi artikel, keyword, dan entitas untuk membantu menemukan masalah serta peluang perbaikan konten SEO.',
					'action' => 'Analisis Konten',
				),
				'amanah-content-generation' => array(
					'title' => 'Article Generation',
					'description' => 'Membantu menyusun draf artikel berdasarkan instruksi dan konteks yang didukung fitur. Draf dapat ditinjau dan disunting sebelum diterbitkan.',
					'action' => 'Buat Artikel',
				),
				'amanah-seo-ai-audit' => array(
					'title' => 'AI Audit',
					'description' => 'Meninjau kebutuhan pemeriksaan dan peluang perbaikan SEO berbantuan AI.',
					'action' => 'Buka AI Audit',
				),
				'amanah-seo-ai-topic-authority' => array(
					'title' => 'Topic Authority',
					'description' => 'Membangun konteks topik dan hubungan antar-konten untuk memperjelas bidang keahlian.',
					'action' => 'Buka Topic Authority',
				),
				'amanah-seo-ai-content-brief' => array(
					'title' => 'Content Brief',
					'description' => 'Menyusun arahan, tujuan, struktur, dan kebutuhan konten sebelum penulisan.',
					'action' => 'Buka Content Brief',
				),
				'amanah-seo-ai-faq-generator' => array(
					'title' => 'FAQ Generator',
					'description' => 'Membantu menyusun pertanyaan dan jawaban yang relevan dengan kebutuhan audiens.',
					'action' => 'Buka FAQ Generator',
				),
				'amanah-seo-ai-content-optimizer' => array(
					'title' => 'Content Optimizer',
					'description' => 'Menemukan peluang perbaikan pada konten yang sudah ada.',
					'action' => 'Buka Content Optimizer',
				),
				'amanah-seo-ai-internal-links' => array(
					'title' => 'Internal Link AI',
					'description' => 'Mendukung evaluasi dan perencanaan hubungan tautan internal antarhalaman.',
					'action' => 'Buka Internal Link AI',
				),
				'amanah-seo-ai-schema-generator' => array(
					'title' => 'Schema Generator',
					'description' => 'Mendukung penyusunan structured data sesuai kebutuhan halaman.',
					'action' => 'Buka Schema Generator',
				),
				'amanah-seo-ai-schema-validation' => array(
					'title' => 'Schema Validation',
					'description' => 'Memeriksa kelayakan dan konsistensi structured data.',
					'action' => 'Buka Schema Validation',
				),
				'amanah-seo-ai-reports' => array(
					'title' => 'AI Reports',
					'description' => 'Menyajikan ringkasan dan hasil dari kemampuan AI setelah integrasi tersedia.',
					'action' => 'Buka AI Reports',
				),
				'amanah-seo-ai-license' => array(
					'title' => 'License & Entitlement AI',
					'description' => 'Menampilkan pengelolaan lisensi dan hak penggunaan fitur AI.',
					'action' => 'Buka License & Entitlement AI',
				),
				'amanah-seo-ai-settings' => array(
					'title' => 'AI Settings',
					'description' => 'Mengelola penyedia AI, kredensial API, dan pilihan model yang tersedia sesuai konfigurasi agar fitur AI dapat menjalankan permintaan.',
					'action' => 'Atur AI',
				),
			);
			$ai_groups = array(
				'Identity & Authority' => array(
					'amanah-entity-profile',
					'amanah-authority',
					'amanah-seo-ai-topic-authority',
				),
				'Content Intelligence & Creation' => array(
					'amanah-content-analyzer',
					'amanah-seo-ai-content-brief',
					'amanah-seo-ai-faq-generator',
					'amanah-seo-ai-content-optimizer',
				),
				'Technical SEO & Validation' => array(
					'amanah-seo-ai-audit',
					'amanah-seo-ai-internal-links',
					'amanah-seo-ai-schema-generator',
					'amanah-seo-ai-schema-validation',
				),
			);
			$ai_placeholder_pages = array(
				'amanah-seo-ai-audit',
				'amanah-seo-ai-topic-authority',
				'amanah-seo-ai-content-brief',
				'amanah-seo-ai-faq-generator',
				'amanah-seo-ai-content-optimizer',
				'amanah-seo-ai-internal-links',
				'amanah-seo-ai-schema-generator',
				'amanah-seo-ai-schema-validation',
				'amanah-seo-ai-reports',
				'amanah-seo-ai-license',
			);
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'AMANAH SEO AI', 'amanah-seo' ); ?></h1>
				<div class="card" style="width:100%;max-width:none;box-sizing:border-box;margin:18px 0 24px;padding:24px;background:#eef7ff;border:1px solid #c7e3ff;border-left:5px solid #4a90e2;border-radius:6px">
					<p style="font-size:15px;line-height:1.7;margin-top:0"><?php esc_html_e( 'AMANAH SEO AI menyediakan kemampuan kecerdasan buatan untuk membantu AMANAH SEO memahami identitas bisnis, memperkuat konteks keahlian, menganalisis konten, dan menemukan peluang perbaikan SEO.', 'amanah-seo' ); ?></p>
					<p style="font-size:15px;line-height:1.7;margin-bottom:0"><?php esc_html_e( 'Kemampuan AI bekerja sebagai bagian dari satu ekosistem: AMANAH SEO mengelola konfigurasi, prioritas, persetujuan, pelaporan, dan tata kelola; AMANAH SEO ENGINE mengatur workflow strategi hingga pembuatan, evaluasi, dan peninjauan konten. Halaman ini berfokus pada kemampuan AI pendukung, bukan menduplikasi tanggung jawab kedua komponen tersebut.', 'amanah-seo' ); ?></p>
				</div>
				<?php if ( ! class_exists( 'AMANAH_SEO_AI_Admin_Menu' ) ) : ?>
					<div class="notice notice-info inline"><p><?php esc_html_e( 'Modul AMANAH SEO AI belum terdeteksi pada instalasi ini. AMANAH SEO tetap dapat digunakan tanpa modul tersebut; kartu placeholder belum menjalankan fungsi AI.', 'amanah-seo' ); ?></p></div>
				<?php endif; ?>

				<h2 style="margin-top:0"><?php esc_html_e( 'Kemampuan AI pendukung', 'amanah-seo' ); ?></h2>
				<p><?php esc_html_e( 'Pilih kemampuan sesuai kebutuhan. Status placeholder berarti integrasi fitur belum tersedia pada paket ini.', 'amanah-seo' ); ?></p>
				<?php foreach ( $ai_groups as $group_title => $group_slugs ) : ?>
					<h3 style="margin-top:24px"><?php echo esc_html( $group_title ); ?></h3>
					<div class="amanah-seo-navigation-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,280px),1fr));gap:18px;width:100%;max-width:none;box-sizing:border-box">
						<?php foreach ( $group_slugs as $slug ) : ?>
							<?php $card = $ai_pages[ $slug ]; ?>
							<div class="postbox" style="padding:18px;margin:0">
								<h3 style="margin-top:0"><?php echo esc_html( $card['title'] ); ?></h3>
								<p><?php echo esc_html( $card['description'] ); ?></p>
								<?php if ( in_array( $slug, $ai_placeholder_pages, true ) ) : ?>
									<p><span class="description"><?php esc_html_e( 'Placeholder — integrasi belum tersedia', 'amanah-seo' ); ?></span></p>
								<?php endif; ?>
								<p><a class="button <?php echo in_array( $slug, $ai_placeholder_pages, true ) ? '' : 'button-primary'; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>"><?php echo esc_html( $card['action'] ); ?></a></p>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php
			return;
		}

		if ( 'amanah-seo-engine-workflow' === $requested_page ) {
			$engine_pages = array(
				'amanah-seo-engine-keyword-research' => '01 — Keyword Research',
				'amanah-seo-engine-intelligence' => '02 — Intelligence',
				'amanah-seo-engine-requirements' => '03 — Requirements',
				'amanah-seo-engine-strategy-plan' => '04 — Strategy & Plan',
				'amanah-seo-engine-brief-blueprint' => '05 — Brief & Blueprint',
				'amanah-seo-engine-generation-evaluation' => '06 — Generation & Evaluation',
				'amanah-seo-engine-refinement-maturity' => '07 — Refinement & Maturity',
				'amanah-seo-engine-review-publishing' => '08 — Review & Publishing',
			);
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'ENGINE Workflow', 'amanah-seo' ); ?></h1>
				<p><?php esc_html_e( 'Delapan tahap workflow ditampilkan berurutan. Integrasi fungsi ENGINE belum termasuk dalam paket navigasi ini.', 'amanah-seo' ); ?></p>
				<div class="amanah-seo-navigation-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:16px;max-width:1100px;margin-top:20px">
					<?php foreach ( $engine_pages as $slug => $label ) : ?>
						<div class="postbox" style="padding:18px;margin:0">
							<h2 style="margin-top:0"><?php echo esc_html( $label ); ?></h2>
							<p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>"><?php esc_html_e( 'View stage', 'amanah-seo' ); ?></a></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $page['title'] ); ?></h1>
			<div class="notice notice-info inline">
				<p><strong><?php esc_html_e( 'Kerangka menu — belum diimplementasikan', 'amanah-seo' ); ?></strong></p>
			</div>
			<p><?php echo esc_html( $page['description'] ); ?></p>
			<hr />
			<p><?php esc_html_e( 'Paket ini hanya menyediakan navigasi untuk pengujian nyata di WordPress. Fitur bisnis akan ditambahkan secara bertahap setelah ruang lingkup dan kriteria penerimaannya disetujui.', 'amanah-seo' ); ?></p>
		</div>
		<?php
	}
}

add_action( 'admin_menu', array( 'AMANAH_SEO_Menu_Shell', 'register' ) );
