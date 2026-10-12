# AMANAH SEO — Core MVP

Plugin WordPress tunggal untuk menjalankan siklus dasar pengelolaan SEO: workspace website → temuan dan prioritas → tugas → persetujuan → bukti hasil → riwayat.

## Fitur yang tersedia dalam paket Core

- Satu entry point plugin AMANAH SEO; aktivasi Core tidak memerlukan lisensi.
- Dashboard dan profil workspace untuk website WordPress tempat plugin dipasang.
- Pencatatan temuan dengan sumber, bukti/alasan, prioritas, ketidakpastian, status, serta catatan saat diselesaikan/dikesampingkan.
- Tugas dengan penanggung jawab, kriteria selesai, tenggat, dependensi, status, dan bukti penyelesaian.
- Persetujuan eksplisit dan riwayat peristiwa; tidak ada perubahan atau publikasi website otomatis.
- Diagnostik runtime yang tidak menampilkan API key atau rahasia.
- Uji runtime satu-klik pada halaman Diagnostik untuk menguji penyimpanan temuan/tugas, dependensi, persetujuan, bukti penyelesaian, dan pembersihan catatan uji.

## Batasan yang masih terbuka

- Website yang dikelola pada MVP adalah situs WordPress tempat plugin dipasang; manajemen website jarak jauh belum tersedia.
- Google Search Console belum terhubung; metrik pencarian tidak dibuat-buat dan tidak diklaim tersedia.
- Source AMANAH SEO AI dan AMANAH SEO ENGINE V2 belum dimigrasikan sebagai modul internal pada paket ini.
- Integrasi server lisensi dan entitlement AI/ENGINE belum tersedia. Core tetap bisa diaktifkan tanpa lisensi; status akses modul tidak diklaim aktif.
- Backup/restore, recovery drill, dan penerimaan runtime akhir tetap harus dibuktikan pada WordPress testing sebelum rilis.

## Instalasi untuk pengujian terkontrol

Salin isi direktori `plugin/` ke `wp-content/plugins/amanah-seo` pada situs WordPress testing yang ditetapkan, lalu aktifkan **AMANAH SEO** di wp-admin. Jangan mengganti plugin lain atau menguji pada produksi. Core membuat/memperbarui hanya tiga tabel miliknya dengan prefix situs saat ini: `amanah_seo_findings`, `amanah_seo_tasks`, dan `amanah_seo_events`. Upgrade tugas menambahkan kolom tenggat/dependensi secara aditif; tidak menghapus baris data yang ada dan tidak menjalankan installer AI/ENGINE lama.

## Validasi otomatis

GitHub Actions menjalankan `php -l` pada file PHP plugin dan tes regresi tanpa dependensi pada PHP 7.4 dan 8.2. Pemeriksaan ini bukan pengganti uji runtime WordPress, uji akses/nonce langsung, backup/restore, atau migrasi modul.

## Uji runtime

Setelah plugin aktif dan skema terbaru terbaca, buka **AMANAH SEO → Diagnostik → Jalankan uji runtime Core**. Uji membuat catatan sementara yang diberi ID unik, menjalankan alur temuan/tugas/dependensi/persetujuan, lalu menghapus hanya catatan dan event yang dibuatnya. Halaman menampilkan PASS/FAIL; bila pembersihan gagal, statusnya dinyatakan FAIL dan harus ditangani sebelum pengujian ulang.
