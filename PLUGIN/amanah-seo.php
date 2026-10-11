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
			'title'       => 'Dashboard',
			'description' => 'Ringkasan kondisi website, prioritas, pekerjaan tertunda, persetujuan, dan hasil akan ditampilkan di sini setelah fungsi terkait diimplementasikan.',
		),
		'amanah-seo-websites' => array(
			'title'       => 'Website & Workspace',
			'description' => 'Pengelolaan website dan ruang kerja belum diimplementasikan. Belum ada website yang didaftarkan atau diubah oleh plugin ini.',
		),
		'amanah-seo-intelligence' => array(
			'title'       => 'SEO Intelligence',
			'description' => 'Status SEO, pemeriksaan website, temuan, peluang pertumbuhan, sumber data, dan tingkat keyakinan belum diimplementasikan. Tidak ada pemindaian yang dijalankan.',
		),
		'amanah-seo-priorities' => array(
			'title'       => 'Priorities & Tasks',
			'description' => 'Prioritas, alasan rekomendasi, tugas, penanggung jawab, status, tenggat, dan dependensi belum diimplementasikan.',
		),
		'amanah-seo-execution' => array(
			'title'       => 'Execution & Approval',
			'description' => 'Rencana pelaksanaan, antrean persetujuan, riwayat keputusan, dan log perubahan/hasil belum diimplementasikan. Tidak ada perubahan website yang dijalankan.',
		),
		'amanah-seo-monitoring' => array(
			'title'       => 'Monitoring & Results',
			'description' => 'Pemantauan pekerjaan, perubahan SEO, perbandingan sebelum/sesudah, dan siklus evaluasi berikutnya belum diimplementasikan. Tidak ada metrik yang dibuat-buat.',
		),
		'amanah-seo-reports' => array(
			'title'       => 'Reports',
			'description' => 'Laporan belum diimplementasikan. Belum ada data atau hasil yang diklaim oleh halaman ini.',
		),
		'amanah-seo-settings' => array(
			'title'       => 'Settings & Access',
			'description' => 'Pengaturan workspace, pengguna/hak akses khusus, integrasi/sumber data, dan preferensi produk belum diimplementasikan. Akses halaman kerangka ini dibatasi ke manage_options.',
		),
		'amanah-seo-ai-dashboard' => array(
			'title'       => 'AI — Dashboard',
			'description' => 'Dashboard AMANAH SEO AI belum ditampilkan dari produk AI. Ini hanya pintasan navigasi dan tidak menjalankan atau menghubungkan produk AI.',
		),
		'amanah-seo-ai-content-analyzer' => array(
			'title'       => 'AI — Content Analyzer',
			'description' => 'Content Analyzer merupakan bagian produk AMANAH SEO AI yang terpisah. Halaman ini hanya placeholder navigasi; analisis konten belum dijalankan dari produk utama.',
		),
		'amanah-seo-ai-entity-profile' => array(
			'title'       => 'AI — Entity Profile',
			'description' => 'Entity Profile berada pada produk AMANAH SEO AI yang terpisah. Halaman ini hanya placeholder navigasi.',
		),
		'amanah-seo-ai-authority' => array(
			'title'       => 'AI — Authority',
			'description' => 'Authority berada pada produk AMANAH SEO AI yang terpisah. Halaman ini hanya placeholder navigasi.',
		),
		'amanah-seo-ai-settings' => array(
			'title'       => 'AI — Settings',
			'description' => 'Pengaturan AMANAH SEO AI dikelola pada produk terpisah. Halaman ini tidak membaca atau mengubah pengaturan produk AI.',
		),
		'amanah-seo-engine-keyword-research' => array(
			'title'       => 'ENGINE — Keyword Research',
			'description' => 'Tahap Keyword pada AMANAH SEO ENGINE V2. Ini hanya placeholder navigasi dan tidak menjalankan workflow ENGINE.',
		),
		'amanah-seo-engine-intelligence' => array(
			'title'       => 'ENGINE — Intelligence',
			'description' => 'Tahap Intelligence pada AMANAH SEO ENGINE V2. Ini hanya placeholder navigasi.',
		),
		'amanah-seo-engine-requirements' => array(
			'title'       => 'ENGINE — Requirements',
			'description' => 'Tahap Requirements pada AMANAH SEO ENGINE V2. Ini hanya placeholder navigasi.',
		),
		'amanah-seo-engine-strategy-plan' => array(
			'title'       => 'ENGINE — Strategy & Plan',
			'description' => 'Tahap Strategy dan Plan pada AMANAH SEO ENGINE V2. Ini hanya placeholder navigasi.',
		),
		'amanah-seo-engine-brief-blueprint' => array(
			'title'       => 'ENGINE — Brief & Blueprint',
			'description' => 'Tahap Brief dan Blueprint pada AMANAH SEO ENGINE V2. Ini hanya placeholder navigasi.',
		),
		'amanah-seo-engine-generation-evaluation' => array(
			'title'       => 'ENGINE — Generation & Evaluation',
			'description' => 'Tahap Generation dan Evaluation pada AMANAH SEO ENGINE V2. Ini hanya placeholder navigasi.',
		),
		'amanah-seo-engine-refinement-maturity' => array(
			'title'       => 'ENGINE — Refinement & Maturity',
			'description' => 'Tahap Refinement dan Maturity pada AMANAH SEO ENGINE V2. Ini hanya placeholder navigasi.',
		),
		'amanah-seo-engine-review-publishing' => array(
			'title'       => 'ENGINE — Review & Publishing',
			'description' => 'Tahap Review dan Publishing Information pada AMANAH SEO ENGINE V2. Ini hanya placeholder navigasi; tidak ada konten yang dipublikasikan.',
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

		foreach ( self::PAGES as $slug => $page ) {
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
