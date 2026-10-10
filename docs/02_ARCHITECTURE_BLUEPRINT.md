# AMANAH SEO — Architecture Blueprint

## 1. Status Dokumen

- **Status:** APPROVED — disetujui sebagai blueprint konseptual; opsi berstatus PROPOSED tetap belum diputuskan
- **Bukti persetujuan baseline:** [commit 322a75f94ff01869cacbb0fcad525cf73916cb76](https://github.com/amanahgroup123/AMANAH-SEO/commit/322a75f94ff01869cacbb0fcad525cf73916cb76) (2026-10-09).
- **Jenis:** Logical architecture blueprint
- **Implementasi:** Belum dimulai berdasarkan dokumen ini
- **Audit produk:** Belum dinyatakan selesai
- **Deployment topology:** Belum diputuskan
- **Integration contracts:** Masih berupa kandidat untuk ditinjau

Dokumen ini mendeskripsikan batas tanggung jawab dan alur konseptual. Dokumen ini bukan bukti bahwa komponen telah dibuat, diuji, atau siap produksi.

## 2. Tujuan Arsitektur

Arsitektur AMANAH SEO ditujukan untuk:

1. Memisahkan administrasi platform dari business logic produk.
2. Menjaga AMANAH SEO AI dan AMANAH SEO ENGINE V2 sebagai produk dengan baseline masing-masing.
3. Menetapkan batas integrasi yang dapat diaudit.
4. Menyediakan model konseptual untuk identitas, lisensi, entitlement, aktivasi, dan compatibility.
5. Mengurangi risiko perubahan pada fitur yang telah berjalan.
6. Memungkinkan pengujian komponen secara terpisah dan lintas produk.
7. Mendukung perubahan versi secara terkontrol.

## 3. Prinsip Arsitektur

### 3.1 Separation of concerns

Setiap komponen hanya boleh bertanggung jawab atas fungsi yang ditetapkan dalam kontraknya. UI bukan pengganti authorization, dan status lisensi bukan pengganti autentikasi.

### 3.2 Product autonomy

AMANAH SEO AI dan AMANAH SEO ENGINE V2 tetap memiliki business logic dan lifecycle produk masing-masing. Platform induk tidak boleh mengasumsikan detail internal tanpa evidence.

### 3.3 Explicit contracts

Pertukaran data dan permintaan antar-komponen harus memiliki definisi identitas, input, output, error, authorization, compatibility, dan versioning yang sesuai.

### 3.4 Fail-safe access control

Jika pemeriksaan otorisasi gagal atau hasil entitlement tidak dapat dipercaya, operasi terlindungi tidak boleh otomatis dianggap diizinkan. Kebijakan fallback untuk kondisi offline harus ditentukan secara eksplisit.

### 3.5 Observable behavior

Kegagalan integrasi harus menghasilkan diagnostik yang aman dan dapat ditelusuri tanpa mengekspos credential atau data sensitif.

### 3.6 Evidence-based evolution

Setiap komponen dan integrasi harus dinilai berdasarkan audit serta hasil pengujian, bukan hanya berdasarkan diagram atau asumsi.

## 4. Logical Context

```text
                  AMANAH SEO
              Parent Platform
                      |
       +--------------+---------------+
       |              |               |
   Accounts       Product Registry   Admin UI
       |              |               |
       +--------------+---------------+
                      |
        License / Entitlement Services
                      |
             Integration Boundary
                /           \
               /             \
      AMANAH SEO AI     AMANAH SEO ENGINE V2
       AI & SEO         Structured SEO Workflow
       Intelligence
````

Diagram ini bersifat konseptual. Diagram tidak menetapkan bahwa semua komponen harus berupa service terpisah, bahwa komunikasi harus melalui jaringan, atau bahwa integrasi aktual telah tersedia.

## 5. Tanggung Jawab Komponen

### 5.1 Parent Platform

Tanggung jawab kandidat:

* Dashboard dan navigasi administratif.
* Product registry.
* Account dan access administration.
* License dan entitlement administration.
* Activation administration.
* Platform configuration.
* Integration status dan administrative diagnostics.
* Audit trail untuk operasi administratif yang relevan.

Batasan:

* Tidak mengambil alih business logic internal produk.
* Tidak menyimpan AI provider API key kecuali terdapat kebutuhan, desain keamanan, dan kontrak yang secara eksplisit membenarkannya.
* Tidak menyatakan produk kompatibel tanpa evidence.

### 5.2 Identity and Access

Tanggung jawab kandidat:

* Menentukan identitas principal.
* Memvalidasi autentikasi.
* Memetakan permission dan role.
* Menyediakan konteks identitas untuk operasi yang membutuhkan otorisasi.

Pertanyaan yang belum diputuskan:

* Apakah identity dikelola sendiri atau melalui layanan eksternal?
* Bagaimana akun customer, organisasi, dan administrator WordPress dipetakan?
* Apakah setiap produk memiliki autentikasi lokal, atau ada mekanisme identity federation?

### 5.3 Product Registry

Tanggung jawab kandidat:

* Menyimpan identitas produk.
* Mencatat versi dan metadata compatibility.
* Mencatat status integrasi.
* Menghubungkan produk dengan kontrak integrasi yang berlaku.

Registry tidak boleh menganggap metadata deklaratif sebagai bukti runtime bahwa suatu fitur benar-benar tersedia.

### 5.4 License and Entitlement

Tanggung jawab kandidat:

* Mengelola status lisensi.
* Menentukan hak penggunaan berdasarkan entitlement yang disahkan.
* Mendukung lifecycle lisensi dan aktivasi.
* Mencatat keputusan administratif yang penting.
* Menyediakan hasil pemeriksaan yang dapat diverifikasi oleh komponen pemanggil.

Belum diputuskan apakah fungsi ini merupakan bagian dari aplikasi utama atau komponen terpisah.

### 5.5 Integration Boundary

Tanggung jawab kandidat:

* Memvalidasi request dan response sesuai kontrak.
* Memastikan identitas pemanggil dan otorisasi diverifikasi.
* Memeriksa compatibility.
* Menangani error secara konsisten.
* Membatasi data yang dipertukarkan.
* Mencegah kegagalan integrasi mengekspos secrets.

Bentuk implementasi dapat berupa adapter, API, library, atau mekanisme lain setelah audit.

### 5.6 AMANAH SEO AI

Produk ini tetap bertanggung jawab atas fungsi AI dan SEO sesuai baseline aktualnya. Integrasi dengan platform induk tidak boleh secara otomatis memindahkan pengelolaan prompt, provider, analisis, atau persistence ke platform induk.

### 5.7 AMANAH SEO ENGINE V2

Produk ini tetap bertanggung jawab atas workflow SEO terstruktur sesuai baseline aktualnya. Integrasi tidak boleh mengubah urutan workflow, state transition, approval, atau publishing behavior tanpa audit dan persetujuan terpisah.

## 6. Candidate Request Flow

Alur berikut adalah model konseptual untuk operasi yang dilindungi lisensi:

1. Pengguna memulai operasi melalui UI atau entry point produk.
2. Sistem memvalidasi identitas pengguna atau principal.
3. Komponen melakukan authorization terhadap operasi yang diminta.
4. Komponen memeriksa entitlement yang relevan berdasarkan kontrak yang disetujui.
5. Komponen memvalidasi compatibility dan status integrasi jika diperlukan.
6. Operasi produk dijalankan hanya setelah seluruh pemeriksaan yang diwajibkan berhasil.
7. Hasil dikembalikan melalui kontrak yang ditentukan.
8. Kegagalan yang relevan dicatat dengan diagnostik yang tidak membocorkan secrets.

Langkah-langkah ini harus disesuaikan berdasarkan audit. Tidak semua operasi harus memerlukan lisensi; kebijakan entitlement harus ditetapkan per fitur atau capability.

## 7. Authorization dan Entitlement

Keduanya merupakan pemeriksaan berbeda:

* **Authentication:** siapa pemanggilnya?
* **Authorization:** apakah pemanggil berhak melakukan operasi ini?
* **Entitlement:** apakah lisensi atau hak penggunaan mengizinkan capability tersebut?
* **Compatibility:** apakah versi dan konfigurasi komponen mendukung operasi tersebut?

Pemeriksaan yang relevan harus dilakukan di sisi server atau batas kepercayaan yang sesuai. Menyembunyikan menu, tombol, atau halaman bukan kontrol keamanan yang memadai.

Belum diputuskan:

* Model role dan permission.
* Granularitas entitlement.
* Kebijakan cache dan masa berlaku hasil pemeriksaan.
* Offline behavior dan grace period.
* Kecepatan propagasi revocation.
* Kebijakan jika layanan lisensi tidak tersedia.

## 8. Data Ownership — Kandidat

| Domain                           | Kandidat pemilik data                                | Status           |
| -------------------------------- | ---------------------------------------------------- | ---------------- |
| Account dan platform permissions | Parent Platform atau identity provider yang disahkan | Belum diputuskan |
| Product registry                 | Parent Platform                                      | Usulan           |
| License records                  | License authority yang disahkan                      | Belum diputuskan |
| Activation records               | License/activation authority                         | Belum diputuskan |
| AI provider configuration        | AMANAH SEO AI atau komponen yang disahkan            | Perlu audit      |
| AI analysis results              | AMANAH SEO AI sesuai baseline aktual                 | Perlu verifikasi |
| SEO workflow state               | AMANAH SEO ENGINE V2 sesuai baseline aktual          | Perlu verifikasi |
| Product-specific settings        | Produk masing-masing                                 | Perlu audit      |
| Cross-platform audit events      | Komponen yang ditentukan dalam security design       | Belum diputuskan |

Tabel ini bukan hasil pemeriksaan database. Kepemilikan aktual harus dikonfirmasi dari schema, repository, runtime, dan master documentation.

## 9. Deployment Topology Options

### Option A — Single application boundary

Platform induk dan fungsi administratif berada dalam satu aplikasi deployment, sementara produk tetap terpisah.

**Potensi manfaat:**

* Operasi dan konfigurasi dapat lebih sederhana.
* Lebih sedikit komponen deployment untuk dikelola.

**Risiko dan pertanyaan:**

* Batas keamanan dan tanggung jawab harus tetap jelas.
* Upgrade platform dapat memengaruhi lebih banyak fungsi.
* Tidak boleh diasumsikan cocok sebelum audit runtime.

**Status:** PROPOSED OPTION.

### Option B — Modular platform with adapters

Platform induk dibagi menjadi modul logis dengan adapter integrasi yang jelas, tanpa mengharuskan setiap modul menjadi service terpisah.

**Potensi manfaat:**

* Batas tanggung jawab dapat diuji.
* Integrasi dapat dikendalikan melalui adapter.
* Tidak otomatis menambah biaya operasional service terpisah.

**Risiko dan pertanyaan:**

* Batas modul harus ditegakkan.
* Shared database dan shared lifecycle dapat menambah coupling jika tidak dirancang dengan benar.

**Status:** PROPOSED OPTION.

### Option C — Separate services

Sebagian fungsi platform dijalankan sebagai service terpisah.

**Potensi manfaat:**

* Isolasi deployment dan scaling dapat lebih kuat.
* Service tertentu dapat memiliki lifecycle independen.

**Risiko dan pertanyaan:**

* Menambah kompleksitas jaringan, monitoring, deployment, dan failure handling.
* Memerlukan desain autentikasi antar-service dan kebijakan retry.
* Belum ada evidence bahwa pemisahan ini diperlukan.

**Status:** PROPOSED OPTION.

### Kriteria pemilihan

Pemilihan topology harus mempertimbangkan:

* Baseline teknis kedua produk.
* Deployment environment yang ditargetkan.
* Keamanan dan trust boundaries.
* Kebutuhan offline dan network availability.
* Beban operasional serta biaya.
* Backup, recovery, dan observability.
* Compatibility dan strategi upgrade.
* Kemampuan pengujian serta rollback.

Tidak ada opsi yang disahkan oleh dokumen ini.

## 10. Failure Modes yang Harus Dirancang

| Failure mode                     | Risiko                                                    | Persyaratan desain                                                      |
| -------------------------------- | --------------------------------------------------------- | ----------------------------------------------------------------------- |
| License service tidak tersedia   | Operasi terlindungi tidak dapat memverifikasi entitlement | Tentukan kebijakan online/offline dan fallback secara eksplisit         |
| Credential tidak valid           | Unauthorized access atau kegagalan operasi                | Tolak akses yang tidak sah dan catat error aman                         |
| Product version tidak kompatibel | Perilaku salah atau kerusakan data                        | Validasi compatibility sebelum operasi yang relevan                     |
| Kontrak tidak valid              | Data salah atau perilaku tidak konsisten                  | Validasi request/response dan versi kontrak                             |
| Revocation belum tersinkron      | Akses berlanjut setelah hak dicabut                       | Tetapkan TTL, invalidation, dan batas keterlambatan yang dapat diterima |
| Provider AI gagal                | Analisis AI gagal                                         | Pertahankan diagnostik yang aman dan perilaku recovery yang jelas       |
| Database atau storage gagal      | Data atau operasi tidak tersedia                          | Definisikan transaksi, backup, recovery, dan integritas data            |
| Credential bocor ke log          | Kompromi keamanan                                         | Redaction, secret scanning, dan pembatasan akses log                    |

Tabel ini merupakan daftar risiko awal, bukan bukti bahwa failure handling telah diimplementasikan atau diuji.

## 11. Observability dan Audit Trail

Rancangan operasional perlu menentukan:

* Correlation ID untuk request lintas komponen.
* Kategori error yang konsisten.
* Log perubahan administratif yang penting.
* Pelacakan versi produk dan kontrak.
* Redaction terhadap API key, token, password, dan data sensitif.
* Kebijakan retensi serta akses log.
* Mekanisme diagnosis tanpa menampilkan informasi rahasia kepada pengguna.

Detail field, format, retensi, dan storage harus diputuskan dalam security dan operations design.

## 12. Compatibility dan Versioning

Setiap integrasi yang disahkan perlu menentukan:

* Product identifier.
* Product version.
* Contract version.
* Minimum dan maksimum versi yang didukung bila diperlukan.
* Capability yang benar-benar tersedia.
* Perilaku untuk versi yang tidak kompatibel.
* Kebijakan perubahan yang breaking dan non-breaking.
* Prosedur upgrade, rollback, dan pengujian regresi.

Nomor versi atau compatibility range tidak boleh ditetapkan berdasarkan perkiraan. Gunakan metadata dan hasil pengujian yang dapat diverifikasi.

## 13. Testing Strategy

Strategi pengujian perlu mencakup:

1. Unit tests untuk aturan bisnis dan validasi kontrak.
2. Integration tests untuk komunikasi antar-komponen.
3. Authorization dan entitlement tests.
4. Negative tests untuk token, permission, dan payload yang tidak valid.
5. Compatibility tests untuk kombinasi versi yang didukung.
6. Failure tests untuk timeout, layanan tidak tersedia, dan respons rusak.
7. Regression tests untuk fitur yang sudah PASS.
8. Security tests untuk CSRF, akses langsung, secret exposure, dan privilege escalation.
9. Runtime acceptance tests pada environment yang telah ditentukan.
10. Rollback dan recovery tests jika sesuai risiko perubahan.

Keberhasilan pengujian harus direkam dengan perintah, environment, hasil, dan evidence yang sesuai.

## 14. Architecture Readiness Gates

Architecture blueprint tidak siap disahkan sebelum:

* Baseline dokumentasi kedua produk diperiksa.
* Source code dan entry point integrasi diaudit.
* Database ownership dan data flow dikonfirmasi.
* Security boundaries serta trust assumptions ditinjau.
* License dan activation lifecycle ditetapkan.
* Deployment topology dipilih berdasarkan kebutuhan dan evidence.
* Integration contracts disepakati.
* Failure handling dan observability dirancang.
* Risiko migrasi, compatibility, dan rollback dievaluasi.
* Keputusan yang masih terbuka dicatat secara eksplisit.

## 15. Keputusan yang Belum Boleh Dianggap Final

Hal-hal berikut tetap terbuka:

* Framework dan teknologi implementasi platform.
* Deployment topology.
* Bentuk integrasi: API, adapter, library, atau opsi lain.
* Identity provider dan account model.
* License authority dan activation protocol.
* Database ownership final.
* Offline policy dan grace period.
* Product compatibility matrix.
* Commercial tiers, harga, dan website limits.
* Production hosting, monitoring, backup, dan recovery design.

## 16. Dokumen Terkait

* `00_PROJECT_CHARTER.md`
* `01_DECISION_REGISTER.md`
* `03_PRODUCT_BOUNDARIES.md`
* `04_INTEGRATION_CONTRACTS.md`
* `05_LICENSE_AND_ENTITLEMENTS.md`
* `06_SECURITY_AND_SECRETS.md`
* `08_AUDIT_AND_READINESS.md`
* `09_ROADMAP_AND_GATES.md`
* `10_OPEN_QUESTIONS.md`
* `templates/ADR_TEMPLATE.md`

## 17. Status Akhir

Dokumen ini menetapkan model arsitektur konseptual untuk ditinjau. Dokumen ini tidak mengesahkan deployment topology, tidak membuktikan compatibility, dan tidak memberikan izin implementasi. Semua keputusan final harus mengikuti audit, review, dan proses persetujuan yang tercatat.
