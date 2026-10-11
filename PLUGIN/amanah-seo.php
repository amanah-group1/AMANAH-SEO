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
		'amanah-seo-ai-status' => array(
			'title'       => 'AI — Status Produk',
			'description' => 'AMANAH SEO AI adalah produk terpisah. Halaman ini hanya penanda navigasi; tidak menghubungkan, menjalankan, atau mengubah AMANAH SEO AI.',
		),
		'amanah-seo-engine-status' => array(
			'title'       => 'ENGINE — Status Produk',
			'description' => 'AMANAH SEO ENGINE V2 adalah produk terpisah. Halaman ini hanya penanda navigasi; tidak menghubungkan, menjalankan, atau mengubah AMANAH SEO ENGINE V2.',
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
