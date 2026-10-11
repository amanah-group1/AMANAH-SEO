# Paket Kesiapan Pengerjaan MVP AMANAH SEO

**Versi:** 0.3-draft  
**Status:** RANCANGAN TERKENDALI — belum disetujui untuk coding  
**Tanggal:** 2026-10-11  
**Acuan utama:** [Pedoman Induk](PEDOMAN_INDUK_AMANAH_SEO.md), [Lingkup MVP](11_MVP_SCOPE_PROPOSAL.md), dan [README](../README.md)  
**Tujuan:** menyiapkan pekerjaan MVP untuk diuji pada website WordPress testing, tanpa menunggu website resmi atau integrasi Google Search Console.

> Dokumen ini hanya merapikan persiapan dan menetapkan batas serta bukti penerimaan. Dokumen ini bukan izin untuk mulai coding, mengubah database/runtime/WordPress, melakukan deployment, atau mengubah produk AMANAH SEO AI maupun AMANAH SEO ENGINE V2.

## 1. Ringkasan keputusan

1. Produk yang dikerjakan adalah **AMANAH SEO**, produk utama pengelola pekerjaan SEO.
2. Website WordPress testing menjadi lingkungan pengujian awal; bukan website resmi/produksi.
3. MVP pertama harus membuktikan satu siklus: **website → kondisi/temuan → prioritas beralasan → tugas → keputusan pemilik → catatan hasil → langkah berikutnya**.
4. Google Search Console **bukan syarat untuk mulai menguji siklus dasar MVP**. Website testing yang belum terdaftar di Search Console tetap bisa dipakai untuk menguji alur, pengelolaan website, temuan yang dimasukkan pemilik, tugas, persetujuan, dan riwayat.
5. Sistem tidak boleh membuat angka klik, tayangan, ranking, atau hasil lain seolah-olah nyata jika belum ada sumber yang membuktikannya. Data yang belum tersedia harus ditandai jelas.
6. WordPress dapat menjadi sumber awal untuk identitas website dan konten yang benar-benar dapat dibaca dengan izin yang tersedia. Cara pengambilan dan data yang bisa dibaca harus diperiksa sebelum fitur tersebut dibuat; jangan menganggap REST API atau akses tertentu sudah tersedia.
7. Temuan awal dapat dimasukkan oleh pengguna secara manual, dengan catatan sumber/alasan dan label bahwa temuan tersebut merupakan masukan pengguna, bukan hasil pengukuran otomatis.
8. Google Search Console dapat ditambahkan pada tahap berikutnya untuk metrik performa pencarian, setelah website yang sesuai tersedia dan pemilik memberi akses resmi baca-saja.
9. Target arsitektur adalah satu plugin WordPress AMANAH SEO yang memuat modul internal AI dan Engine. Modul tersebut tidak menjadi prasyarat fungsional siklus MVP dasar; audit/migrasi modul adalah paket kerja terpisah yang tidak boleh dilakukan secara massal sebelum disetujui.
10. Semua perubahan pada website dan publikasi tetap memerlukan persetujuan eksplisit. MVP tidak melakukan perubahan website secara otomatis.

## 2. Status dokumen dan konflik yang sudah ditemukan

- PR #23 sudah digabung ke `main`; Pedoman Induk dan Lingkup MVP tersedia di `main`.
- PR #24 adalah rancangan persiapan ini. PR masih draft dan belum digabung.
- README sebelumnya memuat kalimat bahwa pedoman berada di branch `controlled/rebuild-product-pedoman` dan belum digabung. Kalimat itu tidak lagi sesuai dengan keadaan saat ini karena Pedoman Induk dan Lingkup MVP sudah ada di `main`. README diperbarui dalam perubahan dokumentasi ini agar merujuk ke keadaan yang benar.
- Dokumen rencana induk penuntasan yang pernah dicari dengan nama `docs/RENCANA_INDUK_PENUNTASAN_PEDOMAN_AMANAH_SEO.md` tidak ditemukan di `main` pada pemeriksaan ini. Karena itu, dokumen tersebut tidak boleh disebut sudah diperiksa atau dijadikan acuan aktif. Acuan aktif yang berhasil diverifikasi adalah Pedoman Induk, Lingkup MVP, dan README.
- Status “pedoman konseptual selesai” tidak berarti kode sudah dibuat, website sudah terhubung, seluruh sumber data sudah diuji, atau implementasi telah disetujui.

## 3. Batas MVP pertama

### Termasuk dalam MVP dasar

1. Mengelola satu website WordPress testing pada satu ruang kerja/pengaturan.
2. Menampilkan identitas dan informasi website yang benar-benar tersedia.
3. Menampilkan status sumber data dan waktu pemeriksaan/pembaruan bila ada.
4. Mencatat temuan SEO dengan judul, uraian, sumber/bukti atau alasan, tingkat kepentingan, ketidakpastian, dan status.
5. Membedakan temuan hasil pengukuran dari temuan yang dimasukkan manual oleh pengguna.
6. Menyusun prioritas yang menjelaskan alasan urutannya; tidak menampilkan skor sebagai fakta bila itu hanya perkiraan.
7. Mengubah temuan menjadi tugas yang memiliki tujuan, penanggung jawab/pemilik keputusan, status, kriteria selesai, dan catatan.
8. Mencatat pemeriksaan dan keputusan pemilik, termasuk status menunggu persetujuan.
9. Mencatat hasil pekerjaan, bukti yang tersedia, kegagalan, dan langkah berikutnya.
10. Menampilkan dashboard sederhana untuk kondisi, prioritas, tugas yang tertahan/gagal, dan keputusan yang menunggu.
11. Menegakkan hak akses dan melindungi tindakan penting.
12. Menguji jalur berhasil, data kosong/tidak tersedia, kegagalan, dan akses yang ditolak.

### Tidak termasuk dalam MVP dasar

- Mengambil metrik Google Search Console sebelum akses nyata dan kebutuhan fitur tersebut siap.
- Menjanjikan atau menampilkan angka klik, tayangan, CTR, posisi Google, atau ranking tanpa sumber terverifikasi.
- Membuat atau menerbitkan konten secara otomatis.
- Mengubah website tanpa persetujuan.
- Menjadikan kemampuan AI atau seluruh workflow Engine sebagai prasyarat siklus MVP dasar.
- Memindahkan kode modul AI/Engine ke Core tanpa audit dependensi, kontrak internal, strategi migrasi data/pengaturan, dan persetujuan paket migrasi.
- Integrasi wajib dengan produk AI/ENGINE, banyak penyedia data sekaligus, billing, marketplace, portal pelanggan, atau pengelolaan tim kompleks.
- Menyatakan integrasi atau fitur sudah berfungsi tanpa bukti uji.

## 4. Pemeriksaan sebelum coding

Pemeriksaan ini dilakukan pada lingkungan AMANAH SEO yang benar, yaitu website WordPress testing yang sudah disiapkan pemilik. Jangan menginstal ulang WordPress atau mengubah konfigurasi hanya karena dokumen ini diperbarui.

### GATE-TEST-WP — pastikan lingkungan testing dapat dipakai

Sebelum coding yang bergantung pada lingkungan, catat:
- alamat website testing dan konfirmasi bahwa pemilik dapat masuk ke WordPress;
- versi WordPress dan PHP yang terpasang, bila mudah diperiksa;
- apakah website berisi konten uji yang aman digunakan;
- cara yang sah dan paling sederhana untuk membaca informasi website/konten dengan izin yang tersedia;
- bukti yang disanitasi, tanpa password, token, atau rahasia.

**Lulus bila:** website testing dapat dibuka oleh pemilik, ruang lingkup pengujian jelas, dan cara membaca data awal terbukti dapat dilakukan atau alternatif manual disepakati. Jika sebagian data tidak dapat dibaca, catat batasannya dan jangan mengarang hasil.

### GATE-DATA-01 — pastikan sumber data sesuai fitur

Gerbang ini berlaku pada sumber data yang benar-benar akan dipakai, bukan kewajiban menghubungkan semua sumber sejak awal.

**Untuk siklus dasar MVP:** sumber awal boleh berupa informasi/konten WordPress yang berhasil dibaca dengan izin yang tersedia dan/atau temuan manual yang dimasukkan pengguna. Label dan asal informasi harus jelas.

**Untuk fitur metrik Google Search:** Google Search Console API dengan akses baca-saja adalah kandidat, bukan koneksi yang sudah tersedia. Gerbang ini baru wajib sebelum fitur tersebut dibuat atau dinyatakan berfungsi. Website testing yang belum terdaftar tidak dapat dipakai sebagai bukti bahwa akses Search Console sudah lulus.

Bukti minimum untuk sumber apa pun:
1. nama/jenis sumber dan izin yang digunakan;
2. contoh data yang benar-benar berhasil dibaca atau dimasukkan;
3. waktu pengambilan atau waktu masukan;
4. cara membedakan data nyata, masukan manual, data kosong, data usang, dan kegagalan;
5. contoh kegagalan atau akses ditolak yang ditampilkan secara jujur;
6. bukti yang disanitasi dan persetujuan pemilik atas penggunaan sumber.

**Status sekarang:** akses nyata WordPress maupun Google Search Console belum dibuktikan oleh dokumen ini. Tidak ada gerbang data yang boleh dinyatakan PASS sebelum pengujian nyata menghasilkan bukti.

## 5. Urutan pekerjaan yang diusulkan

### Tahap 0 — pemeriksaan dan persetujuan paket
- Cocokkan repositori dan lingkungan AMANAH SEO yang benar.
- Periksa cara membaca data WordPress testing dan sepakati contoh data yang aman.
- Tetapkan daftar file/komponen yang akan disentuh, rencana pengujian, dan cara membatalkan perubahan.
- Dapatkan persetujuan eksplisit sebelum coding.

### Tahap 1 — dasar website dan data
- Kelola satu website testing.
- Tampilkan informasi yang tersedia dan status sumber.
- Dukung pencatatan temuan manual dengan sumber/alasan dan label yang benar.
- Buktikan data tidak tersedia atau gagal tidak ditampilkan sebagai sukses.

### Tahap 2 — temuan, prioritas, dan tugas
- Buat temuan yang bisa ditelusuri ke sumber atau masukan pengguna.
- Susun prioritas dengan alasan yang dapat dipahami.
- Buat tugas dari temuan dengan status dan kriteria selesai.

### Tahap 3 — keputusan, hasil, dan dashboard
- Catat keputusan/persetujuan pemilik.
- Catat hasil dan bukti yang tersedia.
- Tampilkan pekerjaan selesai, tertahan, gagal, dan langkah berikutnya.

### Tahap 4 — uji penerimaan siklus lengkap
- Jalankan satu skenario dari pendaftaran website sampai langkah berikutnya.
- Uji kegagalan, akses tidak sah, dan data yang belum tersedia.
- Catat hasil PASS/FAIL berdasarkan bukti; perbaiki masalah yang ditemukan dalam lingkup yang disetujui.

### Tahap berikutnya — metrik Search Console (opsional untuk siklus dasar)
- Baru dimulai jika ada properti yang sesuai, akses resmi baca-saja, dan kebutuhan metrik disetujui.
- Uji izin properti, satu kueri data, keterkinian data, data kosong/parsial, kuota, dan kegagalan autentikasi.
- Jangan mengirim password, token, client secret, atau kredensial ke chat atau repository.

## 6. Kriteria penerimaan MVP

MVP dasar hanya dapat dinyatakan lulus bila bukti menunjukkan seluruh hal berikut:

| ID | Yang harus dibuktikan | Bukti penerimaan |
|---|---|---|
| AC-01 | Pengguna berwenang dapat mengelola satu website testing | Hasil uji tambah/lihat/perbarui/nonaktifkan sesuai lingkup |
| AC-02 | Asal data jelas | Tampilan membedakan data WordPress, masukan manual, dan data yang belum tersedia |
| AC-03 | Tidak ada angka atau temuan rekaan | Uji data kosong/tidak tersedia tidak menghasilkan klaim palsu |
| AC-04 | Temuan memiliki dasar | Setiap temuan memiliki sumber/bukti atau label masukan manual, alasan, status, dan ketidakpastian |
| AC-05 | Prioritas dapat dijelaskan | Urutan prioritas menunjukkan alasan yang dapat ditelusuri ke temuan |
| AC-06 | Temuan dapat menjadi tugas | Tugas memiliki tujuan, status, penanggung jawab/pemilik keputusan, dan kriteria selesai |
| AC-07 | Keputusan pemilik tercatat | Uji keputusan/persetujuan menyimpan status dan catatan yang relevan |
| AC-08 | Hasil dan riwayat dapat ditinjau | Status, waktu, hasil, bukti, dan kegagalan penting tercatat |
| AC-09 | Akses dibatasi | Pengguna tanpa hak yang sesuai ditolak pada sisi server |
| AC-10 | Kegagalan terlihat | Data/sumber gagal atau usang tidak ditampilkan sebagai keberhasilan baru |
| AC-11 | Tidak ada perubahan website otomatis | Pengujian membuktikan alur MVP hanya mengatur pekerjaan dan keputusan |
| AC-12 | Siklus inti selesai | Satu skenario penuh dari website sampai langkah berikutnya berhasil dan terdokumentasi |

Setiap butir harus diberi status PASS, FAIL, atau BELUM DIUJI disertai bukti. Jangan menulis PASS hanya karena kode terlihat benar atau halaman tampil.

### Bukti pengujian minimum

- daftar skenario dan hasilnya;
- hasil pengujian otomatis yang relevan;
- bukti uji manual/integrasi yang disanitasi;
- bukti akses yang diizinkan dan ditolak;
- contoh data kosong atau sumber gagal;
- catatan masalah yang belum selesai dan dampaknya.

## 7. Keamanan dan batas perubahan

- Gunakan hak akses minimum yang diperlukan; semua pemeriksaan izin penting dilakukan di sisi server.
- Jangan menyimpan rahasia di repository, log, screenshot, atau percakapan.
- Jangan mengubah atau menerbitkan konten website tanpa persetujuan pemilik.
- Jangan mengubah kode, database, konfigurasi, atau runtime AMANAH SEO AI maupun AMANAH SEO ENGINE V2 selama tahap persiapan dokumen.
- Target satu plugin tidak mengizinkan pemindahan kode secara massal. Migrasi baru boleh dimulai setelah paket migrasi tersendiri memetakan baseline, file/dependensi, kontrak modul, data/pengaturan, lisensi, tes regresi, dan rollback serta mendapat persetujuan.
- Jangan mengubah database, runtime, konfigurasi, atau website testing saat tahap persiapan dokumen.
- Implementasi harus berada dalam branch/PR tersendiri dengan daftar file, tes, dan cara rollback yang jelas.
- Jangan menggabungkan PR atau melakukan deployment hanya karena paket ini selesai.

## 8. Keputusan yang sudah ditetapkan dan yang masih terbuka

### Sudah ditetapkan
- WordPress adalah platform pertama.
- MVP dimulai dari satu website per ruang kerja/pengaturan.
- Pilih sumber data terkecil yang dapat dibuktikan tersedia.
- Perubahan website/publikasi memerlukan persetujuan eksplisit.
- Akses awal adalah owner/admin.
- Integrasi AI/ENGINE opsional.
- Keberhasilan diukur dari siklus inti, alasan/sumber temuan, persetujuan tercatat, dan kegagalan yang terlihat.

### Masih harus dibuktikan atau diputuskan sebelum coding terkait
- Cara nyata membaca informasi/konten dari WordPress testing dan contoh data yang akan dipakai.
- File/komponen yang disentuh dan rancangan minimum setelah pemeriksaan repositori.
- Strategi penyimpanan, backup, retensi, dan deployment yang diperlukan untuk implementasi yang dipilih.
- Apakah metrik Search Console diperlukan untuk MVP dasar atau ditunda. Rekomendasi saat ini: tunda sampai siklus dasar terbukti.

### Tidak memblokir siklus dasar
- Website resmi dan pendaftaran Search Console untuk website resmi.
- Integrasi AI/ENGINE.
- Harga, billing, banyak website/tim, KPI numerik, dan analitik lanjutan.

## 9. Persetujuan sebelum mulai coding

Sebelum implementasi, pemilik perlu menyetujui:
1. Paket MVP dan batas yang tertulis di dokumen ini.
2. Hasil pemeriksaan lingkungan testing dan sumber data awal.
3. Daftar file/komponen yang akan diubah.
4. Kriteria penerimaan dan skenario uji.
5. Rencana rollback.
6. Izin eksplisit untuk mulai coding.

**Status saat ini:** persiapan dokumentasi sedang dirapikan. Pedoman dan lingkup MVP sudah ada di `main`; paket ini masih draft. Lingkungan dan sumber data nyata belum diverifikasi dalam dokumen ini. Belum ada izin implementasi, perubahan runtime, atau penggabungan PR.


## 10. Paket arsitektur satu plugin dan migrasi modul — belum disetujui untuk implementasi

Pemilik menyetujui target bahwa pengguna memasang satu plugin **AMANAH SEO** yang memuat modul internal AI dan Engine. Persetujuan arsitektur ini tidak dengan sendirinya mengizinkan migrasi kode, perubahan database, atau deployment.

### Tahap A — audit baseline (baca-saja)
- Catat branch, commit, entry point, struktur source, dependensi, tests, dan status kerja untuk ketiga repository.
- Baca master documentation dan aturan proyek AMANAH SEO AI serta AMANAH SEO Engine sebelum menyentuh source mereka.
- Petakan fitur yang nyata, status pengujian, titik aktivasi, hooks, capabilities, routes, REST/AJAX, cron/background jobs, provider credentials, dan diagnostik.
- Petakan database tables, options/settings, versioning, migrations, dan kepemilikan data tanpa mengubahnya.
- Tandai setiap fakta sebagai terverifikasi, parsial, belum diuji, atau terhalang.

### Tahap B — desain kontrak modul
- Tentukan bootstrap dan lifecycle satu plugin induk.
- Tentukan interface/contract internal Core ↔ AI module dan Core ↔ Engine module.
- Tetapkan aturan kegagalan modul: Core harus tetap aman dan diagnostik harus jelas jika suatu modul tidak dapat dimuat.
- Tetapkan kepemilikan data, kredensial, lisensi/entitlement, capabilities, hooks, dan migration ownership.
- Tetapkan kompatibilitas terhadap instalasi/data yang sudah ada serta cara menghindari class/function collisions.

### Tahap C — urutan migrasi terkendali
1. Siapkan kerangka plugin induk dan pengujian bootstrap tanpa mengubah runtime.
2. Migrasikan Core yang diperlukan untuk siklus MVP dan buktikan acceptance criteria MVP.
3. Migrasikan modul AI sebagai paket terpisah di dalam plugin induk, beserta tes regresi provider/settings/entity/content analyzer yang relevan.
4. Migrasikan modul Engine secara terpisah, dengan tes workflow dan publishing approval yang relevan.
5. Tambahkan migrasi data/settings hanya jika dibutuhkan dan setelah rencana upgrade/rollback diuji.
6. Jalankan integrasi lintas modul, keamanan, aktivasi/nonaktifkan, update/upgrade, dan uji WordPress runtime.
7. Pensiunkan plugin lama hanya setelah pengganti lulus dan keputusan penutupan disetujui secara terpisah.

### Kriteria penerimaan arsitektur akhir
- Pengguna dapat memasang dan mengaktifkan hanya plugin AMANAH SEO.
- Core tidak fatal error bila modul gagal dimuat; status kegagalan ditampilkan dengan aman.
- Modul berinteraksi melalui kontrak yang jelas dan tidak mengakses data satu sama lain secara tidak terdokumentasi.
- Data, pengaturan, kredensial, lisensi, hak akses, dan workflow lama dipertahankan atau dimigrasikan dengan bukti.
- Tidak ada class/function collision atau aktivasi ganda dari dua plugin.
- Tes unit, integrasi, regresi, keamanan, migrasi, dan WordPress runtime yang relevan lulus.
- Rollback dapat mengembalikan keadaan sebelumnya tanpa kehilangan data.

**Status:** DESAIN MIGRASI BELUM SELESAI; AUDIT BASELINE BELUM LENGKAP; IMPLEMENTASI BELUM DIMULAI. Tahap berikutnya adalah audit baca-saja dan penyusunan daftar perubahan yang tepat. Tidak ada kode produk yang diubah dalam branch dokumentasi ini.


## 11. Audit baseline awal — read-only, belum lengkap

Pemeriksaan awal dilakukan langsung terhadap file di branch `main` masing-masing repository. Temuan ini hanya baseline sumber; belum merupakan audit seluruh file, database aktual, atau runtime.

| Area | Bukti sumber | Temuan awal | Dampak migrasi |
|---|---|---|---|
| AI entry point | `AMANAH-SEO-AI/plugin/amanah-seo-ai.php` | Memiliki header plugin sendiri, konstanta global `AMANAH_SEO_AI_VERSION/PATH/URL/PREFIX`, memuat loader/core/install/activator/deactivator, dan mendaftarkan activation/deactivation hooks sendiri. | Tidak boleh menyalin entry point apa adanya ke plugin induk. Bootstrap, konstanta, install/upgrade, dan lifecycle harus diintegrasikan secara eksplisit. |
| AI source boundary | `00_MASTER_DOCUMENTATION/REPO-STRUCTURE-001_CANONICAL_PLUGIN_SOURCE_BOUNDARY.md` | `plugin/` adalah source boundary canonical; tests dan master docs berada di luar plugin deployable. | Migrasi harus mengambil source dari `plugin/`, bukan root repo atau folder development/runtime. |
| Engine entry point | `amanah-seo-engine-v2/plugin/amanah-seo-engine.php` | Memiliki header plugin sendiri, memuat autoloader, mendaftarkan namespace `Amanah\\SEOEngine\\`, memanggil `Plugin::boot()`, dan mendaftarkan activation/deactivation hooks sendiri. | Perlu menggabungkan lifecycle ke satu plugin file dan memastikan class loader/namespace tetap kompatibel. |
| Engine PHP baseline | `amanah-seo-engine-v2/composer.json` | Menyatakan PHP `>=7.4`, PSR-4 namespace `Amanah\\SEOEngine\\` ke `plugin/`, serta PHPUnit/WPCS/parallel-lint sebagai dev dependencies. | Versi PHP minimum gabungan dan toolchain harus dipilih berdasarkan baseline semua komponen; jangan mengasumsikan sama. |
| Test configuration | Kedua repository memiliki `phpunit.xml.dist` dan `tests/bootstrap.php`. AI suite bernama `AMANAH SEO AI CORE-003`; Engine suite bernama `Amanah SEO Engine V2`. | Struktur dan bootstrap tes berbeda. | Test suites perlu dipertahankan dan dijalankan dalam layout gabungan tanpa benturan class, path, fixture, atau bootstrap. |
| Engine governance | `Master Dokumen/DOC-00 — Phase 0 Master Index & Document Control.md` dan `DOC-13 — Implementation Boundary, Architecture & Change-Control Specification.md` | Master docs berstatus LOCKED; change-control melarang perubahan diam-diam dan mewajibkan audit penuh sebelum implementasi. | Perubahan yang memengaruhi Engine harus ditangani sebagai controlled migration package dan tidak boleh mengubah aturan LOCKED secara diam-diam. |

### Risiko yang sudah teridentifikasi

1. Dua bootstrap plugin yang terpisah tidak dapat dipertahankan apa adanya sebagai satu plugin WordPress; activation/deactivation dan upgrade hooks harus dipusatkan serta dirancang ulang dengan cakupan yang jelas.
2. Konstanta global AI dan namespace Engine berpotensi berbenturan dengan Core atau modul lain; seluruh definisi global, autoloading, hooks, option keys, capabilities, routes, cron jobs, dan table names harus diinventarisasi.
3. Skema dan data dari kedua plugin belum direkonsiliasi. Tidak ada tabel/options yang boleh digabung, diganti nama, dihapus, atau di-reset sebelum migration plan dan bukti backup/rollback disetujui.
4. Kesamaan folder bernama `modules/` bukan bukti bahwa kedua codebase memiliki kontrak modul yang kompatibel.
5. Audit ini belum memverifikasi semua dokumen AI 01–16, semua dokumen Engine DOC-00–DOC-15, seluruh source tree, database aktual, runtime, atau semua hasil CI. Status audit keseluruhan: **PARTIAL / NOT TESTED**.

### Langkah audit baca-saja berikutnya

- Selesaikan pembacaan dokumen master yang relevan dan aturan proyek pada kedua repository.
- Inventarisasi file entry point, loader, activator, installer/migrations, global constants/functions, namespaces, hooks, capabilities, REST/AJAX routes, cron/jobs, settings/options, tables, uninstall behavior, and tests.
- Bandingkan implementasi aktual dengan locked docs dan status terbaru; catat file/commit untuk setiap temuan.
- Hasilkan mapping sumber → target modul (Core / AI / Engine), dependency graph, daftar benturan, keputusan yang dibutuhkan, dan urutan migrasi.

**Batas audit:** read-only. Tidak ada file kode, branch, database, konfigurasi, runtime, atau WordPress dari repository AI/Engine yang diubah.


## 12. Temuan audit baseline lanjutan — source dan kontrak data

Audit ini masih baca-saja dan berlandaskan file di branch `main` kedua repository pendamping. Ini memperluas baseline pada bagian 11; bukan audit live database atau runtime.

### 12.1 AMANAH SEO AI — struktur dan data

**Source yang diverifikasi:**
- `plugin/amanah-seo-ai.php` — entry point dan lifecycle plugin.
- `plugin/includes/class-plugin-loader.php`, `class-plugin-core.php`, `class-install.php`, `class-activator.php`, `class-deactivator.php` — loader, core, install/upgrade, activation, deactivation.
- `plugin/modules/` memiliki modul `ai/`, `authority/`, `content/`, dan `entity/`.
- Dokumen arsitektur menyatakan `plugin/` sebagai canonical source boundary; dokumentasi dan tes tetap di luar source deployable.

**Daftar tabel yang tercantum dalam dokumen desain database AI (10):**

`wp_amanah_entities`, `wp_amanah_entity_topics`, `wp_amanah_audits`, `wp_amanah_content_analysis`, `wp_amanah_content_plan`, `wp_amanah_schema`, `wp_amanah_ai_logs`, `wp_amanah_tasks`, `wp_amanah_settings`, `wp_amanah_license`.

Daftar tersebut berasal dari dokumen desain, bukan hasil inspeksi database runtime. Nama tabel aktual dapat menggunakan prefix WordPress yang berbeda atau memiliki perbedaan versi; harus diverifikasi melalui installer/migration source dan database aktual sebelum rencana migrasi ditetapkan. Dokumen juga menyebut penggunaan `wp_options`; option keys aktual belum diinventarisasi penuh.

### 12.2 AMANAH SEO Engine — struktur dan data

**Source yang diverifikasi:**
- `plugin/amanah-seo-engine.php` — entry point, autoloader, boot, activation/deactivation hooks.
- `plugin/Core/Autoload/ClassLoader.php` — loader namespace `Amanah\\SEOEngine\\` dari `plugin/`.
- `plugin/Core/Bootstrap/Plugin.php` — bootstrap utama Engine.
- `plugin/Infrastructure/Migration/MigrationInterface.php` dan `MigrationManager.php`, beserta direktori `Migrations/` — terdapat migration framework eksplisit.
- Struktur Engine mencakup `AI`, `Application`, `Core`, `Domain`, `Infrastructure`, `Platform`, dan `Presentation`. Infrastructure memiliki area Database, Migration, Repository, Configuration, BackgroundJob, Diagnostics, WordPress, dan lainnya. Platform memiliki area License dan Update.
- `composer.json` menyatakan PHP `>=7.4`, PSR-4 autoload, PHPUnit 9.6, WPCS, dan parallel-lint untuk development.

Migration manager dan direktori migration berarti urutan serta versi migration Engine harus dipertahankan; tidak boleh disamakan secara asumtif dengan installer/upgrade AI. Isi migration dan schema Engine belum diinventarisasi penuh pada tahap ini.

### 12.3 Konflik dan keputusan teknis yang wajib diselesaikan sebelum coding

| ID | Risiko/pertanyaan | Aturan kerja sementara | Bukti yang masih dibutuhkan |
|---|---|---|---|
| MIG-01 | Nama tabel AI dan Engine mungkin bertumpang tindih atau memakai prefix/versi berbeda. | Jangan rename, merge, drop, atau reset tabel. | Daftar tabel dan skema dari semua migration AI/Engine serta database aktual tiap runtime. |
| MIG-02 | AI menggunakan installer/upgrade; Engine memiliki MigrationManager. | Jangan membuat satu migration runner gabungan sebelum urutan dan ownership dipetakan. | Semua migration file, version option, activation path, upgrade path, dan idempotency test. |
| BOOT-01 | Dua entry point dan lifecycle plugin. | Plugin induk kelak satu-satunya pemilik header/lifecycle; modul tidak mendaftarkan aktivasi plugin mandiri setelah migrasi. | Pemetaan side effects activation/deactivation dan hook yang sekarang terdaftar. |
| LOAD-01 | AI loader dan Engine PSR-4 autoloader. | Namespace, class names, function names, constants, and include paths harus bebas benturan. | Inventaris deklarasi PHP dan pemeriksaan static collision. |
| SEC-01 | API credentials, encrypted settings, custom capabilities, licensing/entitlements. | Tidak boleh memindahkan atau mengubah format rahasia, capability, atau entitlement tanpa compatibility contract. | Dokumen TQ-008 AI, rules licensing Engine, option keys aktual, key derivation/encryption path, role/capability registration, and tests. |
| TEST-01 | PHPUnit bootstrap/suite berbeda. | Pertahankan tes per modul sambil membangun integration tests plugin induk. | Inventaris seluruh test suite dan dependency/fixture masing-masing. |
| UPDATE-01 | Update/licensing area Engine dan AI punya mekanisme masing-masing. | Jangan mengaktifkan dua updater/plugin identity di paket tunggal. | Audit platform license/update code, product IDs, API contracts, and distribution workflow. |

### 12.4 Batas audit dan status

- Master governance Engine DOC-00, DOC-13, DOC-14 berstatus LOCKED dan mewajibkan audit penuh, audit database/migration/dependency, serta controlled change. Dokumen LOCKED tidak diubah oleh pekerjaan ini.
- Master documentation AI yang telah ditinjau mencakup arsitektur, desain database, coding standard, security/privacy, role/permission, implementation control, dan plugin-core implementation plan; dokumen terkait lainnya masih perlu diperiksa sebelum audit dinyatakan lengkap.
- Source entry point dan beberapa direktori telah diperiksa; belum seluruh file PHP, seluruh migration, seluruh tests, uninstall behavior, update/activation side effects, atau source CI.
- Belum ada akses/verifikasi ke database runtime AI/Engine dalam audit ini. Tidak ada klaim skema aktual atau migrasi PASS.

**Status keseluruhan:** AUDIT BASELINE PARTIAL. **Coding/migrasi:** NOT STARTED. **Database/runtime changes:** NONE.

### 12.5 Urutan audit baca-saja berikutnya

1. Baca dokumen kontrol AI lengkap, terutama TQ-007, TQ-008, TQ-005, CORE-002, REPO-STRUCTURE-001, testing/QA, REST API, deployment, dan dokumen implementation control.
2. Baca Engine DOC-04 (data), DOC-05 (master rules), DOC-12 (AI/search/usage), serta aturan migration/portability/license/update yang relevan; hormati seluruh dokumen LOCKED.
3. Inventarisasi seluruh migration source, table/options, class/function/constant declarations, hooks, cron, REST/AJAX, capability, licensing, updater, uninstall, dan tests.
4. Hasilkan dependency graph dan mapping file-by-file ke target Core / AI module / Engine module, disertai disposition: reuse, adapt, replace, defer, atau exclude beserta alasannya.
5. Siapkan rencana migrasi data dan rollback terpisah untuk review sebelum ada perubahan kode.


## 13. Audit kontrak terkunci dan desain migrasi yang disarankan

Bagian ini menerjemahkan bukti dokumen locked ke batas desain migrasi. Ini belum mengubah kontrak pada repository AI atau Engine.

### 13.1 Kontrak AI yang wajib dipertahankan

- `TQ-007_AI_ENGINE_IMPLEMENTATION_CONTRACT.md` berstatus LOCKED. Konfigurasi provider harus eksplisit; provider/model yang digunakan harus dapat diketahui; fallback hanya ke provider lain yang dikonfigurasi setelah retryable failures habis; tidak boleh membuat fallback diam-diam di luar kontrak.
- `TQ-008_AI_API_CREDENTIAL_ENCRYPTION.md` berstatus LOCKED. Kontrak yang tercantum memakai PHP Sodium `sodium_crypto_secretbox()`, nonce acak `random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES)`, serta derivasi kunci HMAC-SHA-256 dengan purpose string tetap dan WordPress auth salt sebagai secret. Migrasi tidak boleh mengubah format, derivasi, atau penanganan credential tanpa kontrak perubahan tersendiri.
- `TQ-005_PHASE_2_MIGRATION_SYSTEM.md` dan `CORE-002_DATABASE_DECISION.md` berstatus LOCKED. AI menggunakan version-based migration terintegrasi installer, database version di WordPress options, tabel dengan `$wpdb->prefix` aktif, migrasi idempotent dan non-destruktif, serta version hanya dinaikkan setelah langkah berhasil. Tidak boleh mengganti mekanisme ini dengan framework baru secara diam-diam.
- `REPO-STRUCTURE-001_CANONICAL_PLUGIN_SOURCE_BOUNDARY.md` menetapkan `plugin/` sebagai source produksi canonical AI; root repo tidak boleh disalin sebagai plugin deployable.

### 13.2 Kontrak Engine yang wajib dipertahankan

- DOC-00, DOC-04, DOC-05, DOC-12, DOC-13, dan DOC-14 yang diperiksa berstatus LOCKED. Perubahan perilaku workflow, data model, provider/model governance, approval, history, atau change-control tidak boleh disisipkan sebagai efek samping integrasi.
- DOC-04 menjelaskan logical data model dan secara eksplisit menyatakan bahwa dokumen itu sendiri belum menetapkan implementasi database aktual. Karena itu, tabel Engine tidak boleh direka dari logical entities; source migration/repository dan database aktual harus menjadi bukti.
- DOC-12 melarang silent provider/model changes dan mengharuskan provider/model/usage yang dipakai dalam operasi dapat diketahui. Integrasi tidak boleh menyamakan kebijakan provider Engine dengan AI tanpa review kontrak dan pengujian.
- Engine memiliki `Infrastructure/Migration/MigrationManager.php` dan `Infrastructure/Migration/Migrations/`. Detail runner, version state, side effects, dan failure/rollback behavior harus dipetakan dari source sebelum desain coordinator ditetapkan.

### 13.3 Rancangan migrasi awal — usulan untuk review

**Prinsip:** satu plugin lifecycle, tetapi migrasi data tetap memiliki ownership per modul. Jangan langsung mengganti dua sistem migration dengan satu implementasi generik.

1. Plugin induk memiliki satu header WordPress, satu entry point, satu activation/deactivation registration, dan satu coordinator untuk melaporkan hasil inisialisasi modul.
2. AI module mempertahankan adapter terhadap kontrak version migration AI yang sudah locked. Ia tidak boleh menjalankan migrasi berulang/destruktif dan tidak boleh menaikkan versi jika langkah gagal.
3. Engine module mempertahankan MigrationManager dan urutan migration Engine setelah audit source membuktikan bagaimana versi dan failure dikelola.
4. Coordinator plugin induk memanggil migrasi modul secara terkontrol, berurutan, dapat diulang dengan aman, dan melaporkan kegagalan. Coordinator tidak menyatukan tabel atau menimpa version option milik modul.
5. Bila aktivasi hanya sebagian berhasil, status modul dan pesan diagnostik harus jelas. Tidak boleh mengklaim semua modul sehat atau menandai seluruh instalasi berhasil jika satu migrasi wajib gagal.
6. Database/option namespace masing-masing modul dipertahankan pada tahap pertama. Konsolidasi namespace, rename tabel, merge data, atau penghapusan sistem lama adalah pekerjaan terpisah yang memerlukan rencana, backup, test migrasi, dan persetujuan.
7. Lifecycle plugin induk tidak boleh memuat dua header plugin terpisah. File entry point lama tidak disertakan sebagai entry point aktif dalam paket tunggal; kelas/fungsi yang masih diperlukan dipindahkan/adaptasi setelah inventarisasi dependency.
8. Plugin lama tidak boleh dihapus/nonaktifkan otomatis sebelum migration compatibility dan upgrade path diuji pada salinan runtime/data.

Rancangan ini adalah usulan teknis berdasarkan kontrak yang dibaca. Detail final harus menunggu audit semua migration files, installer/activator source, uninstall handlers, options, capabilities, and integration tests.

### 13.4 Rencana implementasi terkendali yang akan disiapkan

| Paket | Perubahan yang direncanakan | Batas keselamatan | Bukti penerimaan |
|---|---|---|---|
| A — Source inventory | Daftar lengkap file, class/function/constants, hooks, route, cron, tests, migrations, licensing/updater. | Read-only. | Inventory lengkap dengan sumber/commit dan gap yang ditandai. |
| B — Parent skeleton | Satu header/entry point, bootstrap minimal, module registry/status, tests. | Tidak memuat kode modul atau migrasi data dulu. | Plugin induk bisa diaktifkan/nonaktifkan tanpa fatal error; Core skeleton test PASS. |
| C — AI module adapter | Pindahkan/adapt source AI di bawah module boundary, mempertahankan kontrak AI. | Tidak mengubah schema/credential format/provider behavior. | Tes AI suite lulus dalam paket induk; tests untuk credential/settings/entity/content tetap lulus. |
| D — Engine module adapter | Pindahkan/adapt Engine source dengan namespace dan dependency yang terisolasi. | Tidak mengubah locked workflow/provider/data/approval rules. | Tes Engine suite lulus di layout gabungan; migration sequence tetap valid. |
| E — Data lifecycle | Coordinator memanggil migrasi AI dan Engine dengan ownership terpisah. | Tidak rename/drop/merge; backup dan rollback diuji. | Fresh install dan upgrade path lulus pada database salinan; kegagalan migration tidak menaikkan versi secara keliru. |
| F — Integration hardening | Collision tests, permission/security, activation/deactivation, updater/license, regression, WordPress runtime. | Tidak deploy ke site aktif sebelum seluruh gate lulus. | Seluruh test suite lulus dan runtime evidence terdokumentasi; rollback diuji. |

**Keputusan yang sudah disetujui pemilik:** satu plugin AMANAH SEO dengan AI dan Engine sebagai modul internal.

**Keputusan teknis yang belum boleh diasumsikan:** schema/option consolidation, module migration coordinator details, module enable/disable policy, plugin-old upgrade/disable sequence, updater/license identity, dan pemindahan database aktual.

**Status:** audit masih parsial; desain teknis di atas masih berupa proposal untuk diselesaikan melalui source inventory. Tidak ada kode produk atau database yang diubah.


## 14. Source audit lebih dalam — installer AI dan MigrationManager Engine

Pemeriksaan langsung terhadap implementasi sumber menemukan detail penting yang harus menjadi migration gate. Temuan berikut berasal dari source di branch `main`, bukan dari inspeksi database runtime.

### 14.1 AI installer / upgrade source

File `AMANAH-SEO-AI/plugin/includes/class-install.php` menyatakan `DATABASE_VERSION = '1.3.0'`, membaca option `amanah_version`, dan memakai `amanah_installed` untuk status instalasi. Implementasi memeriksa kesiapan tabel, memakai `$wpdb->prefix`, membuat/memperbarui skema melalui `dbDelta()`, dan hanya menganggap versi siap bila status database sesuai. Ada pula jalur `archive_legacy_content_analysis_table()` yang memindahkan tabel lama ke nama arsip.

**Peringatan data penting:** dokumentasi desain menyebut `wp_amanah_content_analysis`, sedangkan source installer aktif membuat `{$wpdb->prefix}amanah_content_analyses` (bentuk plural). Installer juga mendefinisikan `amanah_article_packages`, yang tidak tercantum dalam daftar 10 tabel awal dokumen desain yang ditinjau. Terdapat kemungkinan perubahan kontrak skema antara dokumen, source, dan data runtime. Ini bukan alasan untuk otomatis mengubah skema; perlu rekonsiliasi formal terhadap CORE-002, migration history, legacy archive path, dan database salinan sebelum integrasi.

### 14.2 Engine MigrationManager source

File `amanah-seo-engine-v2/plugin/Infrastructure/Migration/MigrationManager.php` menyimpan version pada option `amanah_seo_engine_database_version`, mendaftarkan migration berdasarkan versi, lalu menjalankan `up()` dan memperbarui option versi. Ia juga memiliki `repairKnownSchemaDrift()` yang secara khusus dapat menjalankan ulang migration 11 dan 18 bila kondisi tabel tertentu ditemukan.

Ini menunjukkan Engine memiliki logika repair untuk schema drift. Migrasi gabungan harus mempertahankan perilaku tersebut dan tidak boleh menganggap update option versi saja membuktikan schema sehat. Source audit lanjutan harus memeriksa seluruh migration 11/18, registrasi semua migration, tabel yang diverifikasi, serta bagaimana exception/failure ditangani oleh pemanggil.

### 14.3 Dampak dan keputusan teknis

1. Sebelum migrasi, lakukan audit skema aktual per runtime/database yang dituju, termasuk prefix, semua tabel AI/Engine, options versi, status instalasi, tabel arsip legacy, dan jumlah/relasi data. Audit ini harus baca-saja.
2. Rekonsiliasi perbedaan dokumen AI vs installer source sebagai keputusan change-control tersendiri; jangan menganggap dokumen atau source sendirian cukup membuktikan kondisi database.
3. Pertahankan AI option `amanah_version` / `amanah_installed` dan Engine option `amanah_seo_engine_database_version` selama tahap awal. Jangan memakai satu option versi gabungan yang menimpa status migrasi modul.
4. Uji migrasi dari salinan database pada kondisi fresh install, database versi lama, schema drift, migration failure, dan rerun. Verifikasi row counts/data integrity, bukan hanya versi option.
5. Setiap tindakan terhadap tabel legacy/archive harus memiliki backup, precondition eksplisit, bukti pemetaan data, serta rollback yang diuji. Jangan jalankan jalur archive pada database pengguna selama tahap integrasi tanpa paket migrasi yang disetujui.
6. Sebelum menyatukan plugin, tentukan urutan kompatibilitas jika dua plugin lama aktif bersamaan, keduanya memakai tabel bernama mirip, atau salah satu modul sudah pernah menginisialisasi skema. Aktivasi plugin induk tidak boleh memicu side effect destruktif.

**Perubahan yang diizinkan saat ini:** dokumentasi audit pada branch AMANAH-SEO saja. Tidak ada installer, migration source, tabel, options, atau database pendamping yang diubah.
