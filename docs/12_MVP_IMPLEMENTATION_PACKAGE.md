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
