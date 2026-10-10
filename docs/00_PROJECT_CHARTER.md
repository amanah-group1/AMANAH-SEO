# AMANAH SEO — Project Charter

## 1. Status Dokumen

- **Status:** APPROVED — disetujui sebagai pedoman fondasi; bukan bukti implementasi atau kesiapan produk
- **Jenis:** Project charter dan batasan ruang lingkup
- **Bahasa:** Bahasa Indonesia dengan istilah teknis standar dalam bahasa Inggris
- **Repository:** AMANAH SEO
- **Implementasi kode:** Belum dimulai
- **Audit produk:** Belum dinyatakan selesai

Dokumen ini merupakan rancangan dasar untuk ditinjau. Dokumen ini tidak membuktikan bahwa arsitektur, keamanan, integrasi, atau kesiapan produk telah tervalidasi.

## 2. Visi

AMANAH SEO dirancang sebagai platform induk untuk mengelola produk, akun, lisensi, aktivasi, konfigurasi, integrasi, dan administrasi ekosistem AMANAH SEO.

Platform harus memiliki batas tanggung jawab yang jelas, kontrol keamanan terpusat sesuai kebutuhan, serta kontrak integrasi yang terdokumentasi. Platform tidak boleh menghilangkan kemandirian produk yang dikelolanya.

## 3. Tujuan

1. Menyediakan rancangan platform induk dan batas tanggung jawabnya.
2. Mendefinisikan hubungan yang terkontrol antara platform induk, AMANAH SEO AI, dan AMANAH SEO ENGINE V2.
3. Menyusun dasar pengelolaan akun, produk, lisensi, aktivasi, dan entitlement.
4. Menetapkan prinsip keamanan, pengelolaan secrets, authorization, dan audit.
5. Menetapkan proses audit sebelum keputusan implementasi dibuat.
6. Menyediakan integration contracts yang dapat ditinjau sebelum implementasi.
7. Menetapkan acceptance criteria dan readiness gates yang dapat dibuktikan dengan evidence.
8. Menjaga agar perubahan pada produk yang telah berjalan tetap berada di luar scope sampai disetujui secara eksplisit.

## 4. Produk dalam Ekosistem

### 4.1 AMANAH SEO — Platform Induk

Tanggung jawab yang direncanakan:

- Dashboard platform.
- Administrasi produk.
- Manajemen akun dan akses.
- Administrasi lisensi dan entitlement.
- Pengelolaan aktivasi.
- Navigasi lintas produk.
- Pengaturan platform.
- Monitoring status integrasi dan diagnostik administratif.
- Fondasi audit, keamanan, dan governance.

Daftar ini adalah target rancangan, bukan pernyataan bahwa fitur tersebut telah tersedia.

### 4.2 AMANAH SEO AI

Produk terpisah yang berfokus pada kemampuan AI untuk SEO, termasuk analisis konten, entity intelligence, diagnostik, rekomendasi, dan fungsi terkait sesuai dokumentasi serta implementasi aktualnya.

Ruang lingkup final harus ditentukan melalui audit dokumentasi, source code, konfigurasi, database, dan runtime yang relevan.

### 4.3 AMANAH SEO ENGINE V2

Produk terpisah yang berfokus pada workflow SEO terstruktur. Workflow yang menjadi referensi awal mencakup keyword intelligence, requirements, strategy, plan, brief, blueprint, generation, evaluation, refinement, maturity, review, publishing information, approval, dan publishing.

Urutan serta implementasi aktual wajib diverifikasi terhadap master documentation dan baseline produk.

## 5. Batasan Ruang Lingkup

### 5.1 Termasuk dalam fase fondasi

- Dokumentasi charter dan decision register.
- Penyusunan kriteria dan rencana audit baseline dua produk. Audit produk aktual dilakukan pada tahap berikutnya, setelah pedoman fondasi dan audit silang dokumentasi ditutup serta persetujuan eksplisit untuk audit produk dicatat.
- Definisi arsitektur logis.
- Definisi batas produk.
- Rancangan integration contracts.
- Rancangan license dan entitlement model.
- Rancangan security dan secret management.
- Governance dokumen dan perubahan.
- Readiness assessment dan implementation planning.

### 5.2 Tidak termasuk tanpa persetujuan terpisah

- Mengubah source code AMANAH SEO AI.
- Mengubah source code AMANAH SEO ENGINE V2.
- Mengubah database, konfigurasi, atau runtime kedua produk.
- Memindahkan seluruh source code kedua produk ke repository platform induk.
- Mengaktifkan integrasi produksi sebelum kontrak dan readiness disetujui.
- Menetapkan harga, paket komersial, atau batas penggunaan sebagai keputusan final.
- Mengklaim fitur, keamanan, kompatibilitas, atau kesiapan produksi tanpa evidence.

## 6. Prinsip Arsitektur

1. **Audit before implementation:** audit baseline dan kontrak terlebih dahulu.
2. **Explicit boundaries:** setiap produk memiliki tanggung jawab yang jelas.
3. **Contract-based integration:** integrasi menggunakan kontrak yang ditinjau dan disetujui.
4. **Least privilege:** akses diberikan sebatas kebutuhan.
5. **Server-side authorization:** pemeriksaan akses tidak hanya bergantung pada tampilan UI.
6. **Separation of secrets:** license key, activation credential, AI provider API key, dan internal platform credentials memiliki fungsi serta pengamanan berbeda.
7. **Backward compatibility:** perubahan kontrak harus mempertimbangkan produk dan versi yang sudah berjalan.
8. **Evidence-based status:** status PASS harus didukung hasil pengujian atau evidence yang dapat ditelusuri.
9. **Controlled changes:** perubahan dibatasi oleh scope, acceptance criteria, dan persetujuan.
10. **No implicit approval:** usulan teknis atau komersial tidak dianggap keputusan final sebelum disetujui.

Prinsip-prinsip ini adalah baseline rancangan dan belum menggantikan audit teknis.

## 7. Prinsip Integrasi Produk

- Platform induk tidak boleh mengasumsikan struktur internal produk tanpa audit.
- Produk tidak boleh dianggap kompatibel hanya karena menggunakan nama platform yang sama.
- Integrasi harus memiliki definisi identity context, authorization, entitlement, error handling, compatibility, dan observability yang jelas sesuai kebutuhan.
- Kegagalan platform lisensi atau integrasi harus ditangani dengan perilaku yang ditentukan secara eksplisit; tidak boleh diam-diam memberikan akses.
- Mode kegagalan dan kebijakan operasional harus ditentukan sebelum implementasi.
- Integrasi tidak boleh merusak fitur yang sebelumnya telah terbukti PASS.
- Tidak ada klaim integrasi selesai sebelum pengujian lintas produk membuktikannya.

## 8. Tata Kelola Keputusan

Setiap keputusan penting harus masuk ke `01_DECISION_REGISTER.md` atau dokumen ADR yang relevan.

Gunakan klasifikasi berikut:

- **CONFIRMED:** keputusan yang telah disetujui secara eksplisit.
- **PROPOSED:** usulan yang belum disetujui.
- **OPEN:** pertanyaan yang membutuhkan keputusan.
- **ASSUMPTION:** asumsi kerja yang belum diverifikasi.
- **SUPERSEDED:** keputusan yang telah digantikan oleh keputusan baru.

Persetujuan harus memiliki konteks yang jelas: keputusan apa yang disetujui, batas scope, konsekuensi, dan hal-hal yang tetap dikecualikan.

## 9. Kriteria Keberhasilan

Fondasi dokumentasi dianggap siap ditinjau apabila:

- Semua dokumen inti dan template tersedia.
- Batas tanggung jawab platform induk dan kedua produk dijelaskan.
- Keputusan, usulan, asumsi, dan pertanyaan terbuka dibedakan.
- Audit plan mencakup dokumentasi, source code, database, konfigurasi, runtime, dan integrasi sesuai akses yang tersedia.
- Security, license, entitlement, activation, dan secret management dibahas secara terpisah.
- Setiap fase roadmap memiliki entry criteria, exit criteria, dan evidence yang diperlukan.
- Tidak ada klaim audit atau implementasi yang tidak didukung bukti.
- Tidak ada perubahan yang tidak disetujui pada kedua repository produk.

Kesiapan fondasi dokumentasi tidak otomatis berarti kesiapan implementasi atau production readiness.

## 10. Urutan Kerja

1. Menyelesaikan seluruh dokumen pedoman fondasi dan meninjau referensi silang.
2. Melakukan audit silang dokumentasi Phase 2, mencatat semua konflik, kesenjangan, keputusan terbuka, serta evidence dalam register temuan.
3. Melakukan koreksi dokumentasi yang telah berada dalam scope, memvalidasi ulang seluruh inventaris, dan menutup Gate audit dokumentasi dengan bukti.
4. Mencatat persetujuan eksplisit bahwa pedoman fondasi telah selesai dan bahwa audit produk boleh dimulai.
5. Mengaudit AMANAH SEO AI secara terpisah setelah persetujuan pada langkah 4.
6. Mengaudit AMANAH SEO ENGINE V2 secara terpisah setelah persetujuan pada langkah 4.
7. Membandingkan hasil audit dan mengidentifikasi risiko integrasi.
8. Memperbarui architecture blueprint dan integration contracts berdasarkan evidence audit produk.
9. Menyusun implementation plan dengan scope, acceptance criteria, pengujian, risiko, dan rollback.
10. Meminta persetujuan eksplisit sebelum implementasi dimulai.
11. Melakukan implementasi bertahap setelah readiness gate terpenuhi.
12. Memvalidasi hasil melalui pengujian yang dapat diulang.

**Batas urutan:** audit silang dokumentasi AMANAH SEO bukan audit teknis produk. Tidak boleh memulai audit, mengubah source code, database, konfigurasi, atau runtime AMANAH SEO AI maupun AMANAH SEO ENGINE V2 sebelum langkah 1–4 selesai dan persetujuan eksplisit untuk audit produk tercatat.

## 11. Batas Kewenangan Dokumen

Project Charter menetapkan visi, tujuan, prinsip, dan batasan proyek. Dokumen ini tidak menggantikan master documentation masing-masing produk, tidak mengesahkan perubahan pada repository produk, dan tidak menetapkan keputusan komersial yang belum disetujui.

Jika ditemukan konflik dengan dokumentasi produk yang telah disahkan, konflik harus dicatat dan diselesaikan melalui proses governance. Jangan mengubah baseline produk secara diam-diam.

## 12. Pernyataan Status

Pada saat dokumen ini dibuat:

- Repository platform induk masih berada pada tahap fondasi dokumentasi.
- Audit teknis kedua produk belum dinyatakan selesai.
- Arsitektur final dan integration contracts belum dinyatakan disetujui.
- Belum ada otorisasi implementasi kode yang diberikan oleh dokumen ini.
- Harga, paket komersial, dan batas entitlement tetap menunggu keputusan eksplisit.
