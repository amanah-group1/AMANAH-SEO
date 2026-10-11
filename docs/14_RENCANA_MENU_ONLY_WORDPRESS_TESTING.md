# Rencana Implementasi Menu Saja — WordPress Testing

**Status:** RENCANA — belum diimplementasikan  
**Produk:** AMANAH SEO (produk utama saja)  
**Branch:** `plan/menu-only-wordpress-testing`  
**Batas:** menu admin dan placeholder navigasi; tanpa fitur SEO, AI, atau ENGINE.

## 1. Hasil audit titik integrasi

Audit baca-saja terhadap repository GitHub `amanahgroup123/AMANAH-SEO` pada `main` menemukan bahwa repository yang diperiksa berisi dokumentasi dan belum menyediakan source plugin WordPress yang dapat dipakai untuk memverifikasi:

- file entry point/bootstrap plugin;
- cara plugin mendaftarkan menu admin;
- pola capability/otorisasi yang digunakan;
- callback/render halaman admin;
- struktur pengujian plugin dan prosedur sinkronisasi ke WordPress testing.

Pencarian kode untuk `add_menu_page`, `register_activation_hook`, dan jalur `plugin/amanah-seo` tidak menghasilkan titik integrasi yang dapat diverifikasi. Karena itu, **integrasi kode belum boleh dimulai** sebelum repository/folder plugin yang benar tersedia dan diaudit. Jangan menebak file atau membuat bootstrap baru sebagai pengganti entry point yang belum ditemukan.

Struktur menu final secara konseptual dicatat dalam PR terpisah #25: https://github.com/amanahgroup123/AMANAH-SEO/pull/25. PR tersebut belum dianggap sebagai bukti bahwa plugin atau menu telah berjalan.

## 2. Tujuan implementasi yang direncanakan

Menyediakan navigasi admin WordPress sebagai pengingat urutan kerja dan tempat menandai tahapan yang akan dikerjakan. Setiap halaman tahap hanya menampilkan placeholder statis, misalnya:

> Tahap ini belum diimplementasikan. Halaman ini hanya penanda rencana kerja AMANAH SEO.

Menu dan placeholder **bukan** bukti bahwa fitur terkait telah tersedia.

## 3. Struktur menu yang direncanakan

Satu menu induk saja: **AMANAH SEO**. Tidak membuat menu tingkat atas terpisah untuk AMANAH SEO AI atau AMANAH SEO ENGINE.

- **Dashboard** — placeholder ringkasan.
- **Website & Workspace** — placeholder.
- **SEO Intelligence**
  - Ringkasan Kondisi SEO
  - Hasil Pemeriksaan Website
  - Temuan dan Masalah SEO
  - Peluang Pertumbuhan
  - Sumber Data dan Tingkat Keyakinan
- **Prioritas & Tasks**
  - Daftar Prioritas SEO
  - Daftar Tugas
  - Penanggung Jawab dan Status
  - Tenggat dan Dependensi
- **Execution & Approval**
  - Rencana Pelaksanaan
  - Antrean Persetujuan
  - Riwayat Keputusan
  - Catatan Perubahan dan Hasil Eksekusi
- **Monitoring & Results**
  - Status Pekerjaan
  - Perubahan Kondisi SEO
  - Hasil Sebelum dan Sesudah
  - Siklus Evaluasi Berikutnya
- **Reports** — placeholder.
- **Settings & Access**
  - Pengaturan Workspace
  - Pengguna dan Hak Akses
  - Integrasi dan Sumber Data
  - Preferensi Sistem
- **AMANAH SEO AI** — kelompok modul/placeholder saja; tidak membangun kemampuan AI.
- **AMANAH SEO ENGINE** — kelompok modul/placeholder saja; tidak membangun workflow ENGINE.

AI dan ENGINE tidak menjadi dependensi untuk membuka atau menggunakan menu dasar AMANAH SEO. Submenu AI/ENGINE, bila dipertahankan pada navigasi pengingat, harus jelas berstatus placeholder dan tidak mengklaim integrasi aktif.

## 4. Urutan kerja setelah titik integrasi ditemukan

1. **Identifikasi source plugin yang benar.** Catat repository, branch, entry point, folder plugin, dan remote. Pastikan bukan repository AMANAH SEO AI atau AMANAH SEO ENGINE V2.
2. **Audit pola integrasi yang sudah ada.** Baca entry point/bootstrap, registrasi admin menu, callback halaman, capability, nonce bila ada aksi, pola escaping output, autoloading, serta tes yang tersedia. Tahap ini baca-saja.
3. **Tetapkan daftar file yang akan diubah.** Gunakan registrasi menu dan pola akses yang telah ada; jangan membuat bootstrap atau mekanisme otorisasi kedua.
4. **Implementasi terbatas.** Tambahkan menu induk AMANAH SEO dan halaman placeholder statis. Tidak ada query bisnis, tabel/database baru, koneksi eksternal, perubahan website, aksi tulis, API AI, atau fitur ENGINE.
5. **Validasi statis dan tes.** Jalankan pemeriksaan sintaks PHP, tes yang relevan, pemeriksaan diff, dan pastikan tidak ada perubahan di luar daftar file yang disetujui.
6. **Buka PR implementasi terpisah.** PR harus menjelaskan daftar file, hasil tes, perilaku placeholder, risiko, dan langkah rollback. Jangan merge otomatis.
7. **Uji di WordPress testing setelah PR ditinjau/disetujui.** Sinkronkan hanya paket yang disetujui ke plugin target yang sudah dipastikan benar. Verifikasi menu tampil, setiap placeholder dapat dibuka, pengguna tanpa capability tidak dapat mengaksesnya, tidak ada fatal error, dan menu/plugin lama tetap berfungsi.
8. **Catat hasil runtime.** Status PASS hanya diberikan jika disertai bukti pengujian yang benar-benar dijalankan.

## 5. Kriteria penerimaan menu-only

- Hanya satu menu induk bernama **AMANAH SEO**.
- Menu tidak mengklaim fitur placeholder sudah tersedia.
- Semua item yang direncanakan membuka halaman placeholder yang aman dan konsisten.
- Pemeriksaan capability menggunakan pola yang sesuai dengan plugin setelah diaudit; akses langsung ke URL juga terlindungi.
- Output halaman di-escape sesuai konteks; tidak ada aksi perubahan data atau publikasi.
- Tidak ada perubahan database, konfigurasi, atau koneksi eksternal.
- Tidak ada implementasi fitur SEO, AI, atau ENGINE.
- Tidak ada perubahan pada repository AMANAH SEO AI maupun AMANAH SEO ENGINE V2.
- Pemeriksaan sintaks, tes yang relevan, diff, dan uji WordPress testing memiliki bukti yang tercatat.

## 6. Hal yang memblokir implementasi kode

**BLOCKED — source plugin WordPress yang benar dan titik integrasinya belum terverifikasi dari repository yang diaudit.**

Sebelum menulis kode, cocokkan source plugin yang benar dengan runtime WordPress testing. Jangan menyalin atau mengubah runtime sampai kecocokan tersebut dibuktikan. Setelah titik integrasi ditemukan, audit tersebut harus menyebut path dan nama file yang nyata; barulah daftar file implementasi dapat dikunci.

## 7. Batas perubahan PR ini

PR ini hanya mengusulkan rencana dan kriteria penerimaan. Tidak ada kode plugin, sinkronisasi, deployment, perubahan runtime, atau implementasi fitur yang dilakukan oleh dokumen ini.
