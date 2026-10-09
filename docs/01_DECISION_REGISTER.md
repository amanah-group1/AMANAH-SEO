# AMANAH SEO — Decision Register

**Status Dokumen:** DRAFT — baseline awal; status keputusan dicatat per entri di bawah.

## 1. Tujuan

Dokumen ini mencatat keputusan, usulan, asumsi, dan pertanyaan terbuka yang memengaruhi arah pengembangan AMANAH SEO.

Tujuan utamanya:
- Mencegah keputusan penting hilang atau berubah tanpa persetujuan.
- Membedakan keputusan yang sudah dikonfirmasi dari rekomendasi teknis.
- Menyediakan jejak alasan, konsekuensi, dan status setiap keputusan.
- Menjadi rujukan sebelum audit, architecture review, dan implementation planning.

## 2. Status Keputusan

Gunakan status berikut secara konsisten:

| Status | Makna |
|---|---|
| CONFIRMED | Disetujui secara eksplisit dan dapat ditelusuri ke persetujuan yang relevan. |
| PROPOSED | Usulan yang belum memperoleh persetujuan final. |
| OPEN | Membutuhkan keputusan atau informasi tambahan. |
| ASSUMPTION | Asumsi kerja yang belum diverifikasi. |
| SUPERSEDED | Digantikan oleh keputusan lain; riwayat harus tetap dipertahankan. |

Status di atas adalah status keputusan, bukan status dokumen. Status dokumen ditetapkan dalam `docs/07_DOCUMENT_GOVERNANCE.md`; status hasil audit ditetapkan dalam `docs/08_AUDIT_AND_READINESS.md`.

Dokumen ini tidak boleh mengubah status PROPOSED menjadi CONFIRMED hanya karena suatu usulan terlihat masuk akal atau telah ditulis dalam dokumen.

## 3. Keputusan Dasar yang Telah Dikonfirmasi

### DEC-001 — Platform induk merupakan proyek terpisah

- **Status:** CONFIRMED
- **Keputusan:** AMANAH SEO dikembangkan sebagai proyek dan repository tersendiri dari AMANAH SEO AI serta AMANAH SEO ENGINE V2.
- **Konsekuensi:** Dokumentasi dan perubahan pada platform induk tidak otomatis mengubah kedua repository produk.
- **Batasan:** Keputusan ini tidak dengan sendirinya menentukan deployment topology atau bentuk teknis integrasi.

### DEC-002 — Kedua produk yang ada harus tetap dibedakan

- **Status:** CONFIRMED
- **Keputusan:** AMANAH SEO AI dan AMANAH SEO ENGINE V2 merupakan dua produk berbeda dengan tanggung jawab dan baseline masing-masing.
- **Konsekuensi:** Audit, compatibility assessment, dan rencana integrasi harus dilakukan secara terpisah sebelum digabungkan ke dalam satu sistem operasional.
- **Batasan:** Tidak ada klaim bahwa keduanya telah kompatibel atau siap diintegrasikan.

### DEC-003 — Audit mendahului implementasi

- **Status:** CONFIRMED
- **Keputusan:** Audit dan penetapan scope harus mendahului implementasi yang berdampak pada produk atau platform.
- **Konsekuensi:** Temuan harus memiliki evidence, risiko, dan rekomendasi yang jelas sebelum implementation plan disusun.
- **Batasan:** Pembuatan dokumentasi fondasi ini tidak dianggap sebagai implementasi fitur produk.

### DEC-004 — Perubahan produk yang ada memerlukan otorisasi terpisah

- **Status:** CONFIRMED
- **Keputusan:** Penyusunan fondasi AMANAH SEO tidak memberikan izin otomatis untuk mengubah source code, database, konfigurasi, atau runtime AMANAH SEO AI maupun AMANAH SEO ENGINE V2.
- **Konsekuensi:** Setiap perubahan harus memiliki scope dan persetujuan yang jelas.
- **Batasan:** Audit baca-saja dapat direncanakan secara terpisah; metode dan akses aktual tetap perlu ditentukan.

### DEC-005 — Persetujuan scope sebelum implementasi

- **Status:** CONFIRMED
- **Keputusan:** Implementasi memerlukan scope yang disepakati, acceptance criteria, rencana validasi, dan persetujuan eksplisit.
- **Konsekuensi:** Fitur di luar scope tidak boleh ditambahkan diam-diam.
- **Batasan:** Persetujuan atas satu fase tidak otomatis menjadi persetujuan untuk fase berikutnya.

### DEC-006 — Status harus berdasarkan evidence

- **Status:** CONFIRMED
- **Keputusan:** Klaim PASS, compatibility, keamanan, atau readiness harus didukung evidence yang sesuai.
- **Konsekuensi:** Hasil yang belum diuji harus diberi status NOT TESTED atau status lain yang tepat.
- **Batasan:** Dokumen rancangan saja tidak membuktikan bahwa implementasi telah lolos pengujian.

### DEC-007 — Bahasa dokumentasi

- **Status:** CONFIRMED
- **Keputusan:** Dokumentasi menggunakan Bahasa Indonesia dengan istilah teknis standar dalam bahasa Inggris.
- **Konsekuensi:** Istilah teknis penting harus konsisten antardokumen.

## 4. Usulan Arsitektur — Belum Disetujui

### PROP-001 — Platform induk sebagai control plane

- **Status:** PROPOSED
- **Usulan:** AMANAH SEO menjadi pusat administrasi produk, akun, lisensi, aktivasi, entitlement, dan status integrasi.
- **Alasan:** Memusatkan fungsi administrasi tanpa harus memindahkan seluruh business logic produk.
- **Belum diputuskan:** Batas tanggung jawab runtime, model deployment, ketergantungan jaringan, dan perilaku ketika platform tidak tersedia.
- **Syarat keputusan:** Architecture review berdasarkan audit kedua produk.

### PROP-002 — Integrasi melalui explicit contracts

- **Status:** PROPOSED
- **Usulan:** Integrasi dilakukan melalui kontrak yang terdokumentasi, dengan adapter atau mekanisme lain yang dipilih setelah audit.
- **Alasan:** Mengurangi ketergantungan pada detail internal dan mempermudah compatibility management.
- **Belum diputuskan:** Bentuk kontrak final, protokol komunikasi, versioning, dan mekanisme autentikasi.
- **Syarat keputusan:** Audit kebutuhan integrasi dan review keamanan.

### PROP-003 — Pemeriksaan entitlement pada operasi terlindungi

- **Status:** PROPOSED
- **Usulan:** Operasi yang memerlukan lisensi harus memeriksa entitlement dan authorization di sisi server.
- **Alasan:** Visibilitas menu tidak boleh menjadi satu-satunya kontrol akses.
- **Belum diputuskan:** Mode offline, caching, grace period, revocation propagation, dan kebijakan kegagalan layanan lisensi.
- **Syarat keputusan:** License model dan threat model disetujui.

### PROP-004 — Product registry

- **Status:** PROPOSED
- **Usulan:** Platform memiliki registry untuk menyimpan identitas produk, versi, compatibility metadata, dan status integrasi.
- **Belum diputuskan:** Schema, source of truth, discovery mechanism, dan kebutuhan migrasi.
- **Syarat keputusan:** Audit metadata produk dan architecture review.

### PROP-005 — Pemisahan credential berdasarkan fungsi

- **Status:** PROPOSED
- **Usulan:** License credential, activation credential, AI provider API key, dan internal platform credential dipisahkan berdasarkan tujuan, penyimpanan, izin, serta siklus hidupnya.
- **Alasan:** Mencegah pencampuran akses komersial, identitas instalasi, akses AI provider, dan akses internal.
- **Belum diputuskan:** Format token, key management, rotation policy, dan implementasi penyimpanan.
- **Syarat keputusan:** Security review dan penetapan threat model.

## 5. Usulan Komersial — Belum Disetujui

### PROP-006 — Tier lisensi

- **Status:** PROPOSED
- **Usulan awal:** Menilai model tier seperti Personal, Business, dan Agency.
- **Belum diputuskan:** Nama final, harga, fitur setiap tier, kuota, website limit, pengguna, dan aturan upgrade atau downgrade.
- **Peringatan:** Nama tier tersebut bukan keputusan komersial final.

### PROP-007 — Bundle produk

- **Status:** PROPOSED
- **Usulan:** Menilai kemungkinan lisensi AMANAH SEO AI, AMANAH SEO ENGINE V2, dan paket gabungan.
- **Belum diputuskan:** Hak penggunaan, harga, entitlement matrix, masa berlaku, dan kebijakan pembatalan.
- **Syarat keputusan:** Analisis produk, biaya operasional, dan persetujuan pemilik bisnis.

### PROP-008 — Website limit

- **Status:** PROPOSED
- **Usulan:** Mengevaluasi pembatasan jumlah website atau instalasi berdasarkan tier.
- **Belum diputuskan:** Angka limit, definisi satu website, aturan staging atau development, transfer aktivasi, dan mekanisme reset.
- **Peringatan:** Angka limit yang pernah dibahas sebelumnya tidak boleh dianggap final tanpa persetujuan eksplisit.

## 6. Indeks Pertanyaan Terbuka

Bagian ini adalah indeks ringkas untuk membantu navigasi, bukan daftar pertanyaan kedua yang berdiri sendiri. Daftar rinci dan status setiap pertanyaan berada di `docs/10_OPEN_QUESTIONS.md`. ID `OPEN-xxx` di bawah dipertahankan untuk riwayat Decision Register; ID pertanyaan rinci menggunakan format `Q-xxx`.

| ID indeks | Pokok bahasan | Pertanyaan rinci terkait |
|---|---|---|
| OPEN-001 | Identitas dan kepemilikan akun | Q-001 sampai Q-003 |
| OPEN-002 | Otoritas lisensi dan pemeriksaan entitlement | Q-008, Q-012, Q-013 |
| OPEN-003 | Identitas dan siklus aktivasi | Q-011 |
| OPEN-004 | Ketersediaan layanan dan kebijakan offline | Q-012 sampai Q-013 |
| OPEN-005 | Bentuk penempatan aplikasi | Q-018 |
| OPEN-006 | Cara komunikasi dan versi kontrak | Q-005, Q-020 |
| OPEN-007 | Pendaftaran produk dan kompatibilitas | Q-004, Q-020 |
| OPEN-008 | Paket dan batas komersial | Q-009 sampai Q-010 |
| OPEN-009 | Keamanan dan pengelolaan operasional | Q-021 sampai Q-024, Q-027 |
| OPEN-010 | Penyimpanan dan masa retensi data | Q-022 sampai Q-023 |
| OPEN-011 | Kepemilikan data dan navigasi produk | Q-006 sampai Q-007 |
| OPEN-012 | Kredensial AI dan pembebanan biaya | Q-014 sampai Q-016 |
| OPEN-013 | Teknologi platform dan database | Q-017, Q-019 |
| OPEN-014 | Prioritas produk, MVP, dan strategi rilis | Q-025 sampai Q-027 |

Semua indeks di tabel ini tetap **OPEN** sampai pertanyaan rinci terkait mendapat keputusan yang tercatat. Pertanyaan rinci di `docs/10_OPEN_QUESTIONS.md` tetap menjadi daftar acuan untuk isi, ketergantungan, dan status pertanyaan. Jika keputusan menutup suatu pertanyaan, perbarui kedua dokumen dalam perubahan yang sama dan tautkan keputusan yang disetujui. Jangan menandai pertanyaan selesai hanya karena pemetaan di tabel ini telah dibuat.

## 7. Cara Menambahkan Keputusan

Setiap keputusan baru wajib memuat:

1. ID unik.
2. Status.
3. Konteks atau masalah.
4. Keputusan atau usulan.
5. Alasan.
6. Konsekuensi dan trade-off.
7. Alternatif yang dipertimbangkan, jika relevan.
8. Evidence atau sumber persetujuan.
9. Tanggal dan pihak yang menyetujui, bila tersedia.
10. Dokumen atau kontrak yang terdampak.

Keputusan arsitektur yang memiliki konsekuensi luas sebaiknya memiliki dokumen ADR tersendiri menggunakan `templates/ADR_TEMPLATE.md`.

## 8. Aturan Perubahan

- Jangan menghapus keputusan lama hanya karena statusnya berubah.
- Tandai keputusan yang digantikan sebagai SUPERSEDED dan tautkan ke keputusan pengganti.
- Jangan menyimpulkan persetujuan dari diamnya reviewer.
- Jangan mengubah usulan menjadi keputusan final hanya untuk mempercepat implementasi.
- Jika keputusan memengaruhi kedua produk yang sudah ada, catat dampaknya dan lakukan review terhadap baseline masing-masing produk.

## 9. Status Keseluruhan

Decision Register ini adalah baseline awal untuk governance AMANAH SEO.

Keputusan dasar pada Bagian 3 merupakan keputusan yang telah dinyatakan dalam arahan proyek. Usulan arsitektur dan komersial pada Bagian 4 dan 5 tetap PROPOSED sampai disetujui secara eksplisit. Pertanyaan pada Bagian 6 tetap OPEN sampai memperoleh jawaban yang terdokumentasi.

Tidak ada bagian dokumen ini yang menyatakan bahwa audit teknis, implementasi, atau integrasi telah selesai.
