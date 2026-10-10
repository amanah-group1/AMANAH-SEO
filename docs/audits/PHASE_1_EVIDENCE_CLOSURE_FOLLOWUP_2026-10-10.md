# Phase 1 Evidence Closure Follow-up — AMANAH SEO AI & AMANAH SEO ENGINE V2

**Tanggal:** 2026-10-10  
**Repository otoritas:** `amanahgroup123/AMANAH-SEO`, branch `main`  
**Tujuan:** Melanjutkan audit baca-saja dengan memverifikasi baseline GitHub terbaru dan memperdalam bukti untuk temuan yang sudah dicatat.  
**Status:** PARTIAL — bukti GitHub tambahan tercatat; Gate 1 kedua produk tetap BLOCKED.  
**Batas perubahan:** Hanya dokumentasi audit pada repository induk AMANAH SEO. Tidak ada source code, test, database, konfigurasi, atau runtime kedua produk yang diubah.

## 1. Baseline Revalidation

| Produk | Repository | HEAD saat diverifikasi | Hasil |
|---|---|---|---|
| AMANAH SEO AI | [AMANAH-SEO-AI](https://github.com/amanahgroup123/AMANAH-SEO-AI) | `ab8e18c5e23e0d16f79115619955b0f4f00bf4b6` | Sama dengan baseline laporan Phase 1 tanggal 2026-10-10 |
| AMANAH SEO ENGINE V2 | [amanah-seo-engine-v2](https://github.com/amanahgroup123/amanah-seo-engine-v2) | `80e7c38a327baeef1d7ad7b13d006d453d5838b1` | Sama dengan baseline laporan Phase 1 tanggal 2026-10-10 |

Kedua repository tidak menunjukkan commit `main` yang lebih baru daripada baseline laporan ketika diperiksa. Kesimpulan temuan kode di bawah karenanya tetap merujuk pada commit tersebut.

## 2. AMANAH SEO AI — Bukti Tambahan

### AI-F-001 — Entity Profile route mismatch

**Status bukti:** VERIFIED pada source code GitHub; belum direproduksi ulang pada runtime dalam sesi ini.

- [Entity list view](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/modules/entity/views/entity-form.php) membangun link entity menggunakan parameter halaman `page=amanah-seo-ai` dengan `entity_id`.
- [Entity admin registration](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/modules/entity/class-entity-admin.php) mendaftarkan halaman `amanah-entity-profile` di bawah menu `amanah-seo-ai-settings`.
- [Top-level admin menu](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/admin/class-admin-menu.php) mendaftarkan slug `amanah-seo-ai-settings`, bukan `amanah-seo-ai`.

**Kesimpulan:** mismatch slug tetap terkonfirmasi dan konsisten dengan gejala akses halaman yang dilaporkan. Ini belum membuktikan seluruh perilaku runtime; koreksi harus memiliki scope terpisah dan regression test untuk link daftar, detail/edit entity, serta capability/ownership.

### AI-F-002 — License key storage

**Status bukti:** VERIFIED pada save path yang diperiksa; keputusan kontrak keamanan tetap OPEN.

Controller menyimpan nilai `license_key` melalui jalur database `amanah_license` tanpa encryption wrapper yang sama dengan credential AI. Dokumentasi yang diperiksa belum secara eksplisit menetapkan kewajiban enkripsi untuk license key. Maka statusnya tetap **risiko penanganan secret / keputusan kontrak**, bukan pelanggaran persyaratan yang sudah terbukti.

**Keputusan pemilik yang dibutuhkan:** tentukan klasifikasi license key dan aturan penyimpanan (plain, encrypted-at-rest, atau token non-secret) berdasarkan model lisensi yang disetujui. Jangan mengubah format penyimpanan sebelum kontrak dan migrasi/backward compatibility dinilai.

### AI-F-003 — Operational documentation drift

**Status bukti:** VERIFIED pada dokumen `main`.

`PROJECT_STATUS.md` dan `NEXT_TASK.md` masih menyebut CORE-005/Entity Profile sebagai implementasi atau runtime yang menunggu verifikasi, sedangkan code tree `main` sudah berisi modul Entity Profile dan Authority beserta test terkait. Kehadiran kode tidak membuktikan runtime PASS.

**Tindak lanjut:** dokumentasikan status aktual GitHub dan pisahkan secara eksplisit status code/CI dari status local WordPress runtime; lakukan sebagai koreksi dokumentasi terkendali.

### AI-F-004 — Settings save-path test coverage

**Status bukti:** GAP tetap ada pada test yang diperiksa.

Test yang disebut dalam laporan awal tidak membuktikan keseluruhan POST → validation → credential/settings persistence → redirect pada controller sebenarnya. Ini adalah gap bukti pengujian, bukan bukti bahwa save path pasti gagal.

**Tindak lanjut:** acceptance criteria untuk validasi berikutnya perlu menguji controller flow dan persistence dengan test doubles yang sesuai; pengujian database/runtime tetap terpisah.

### Batas bukti AMANAH SEO AI

- GitHub source/docs sudah diperiksa ulang pada HEAD yang sama.
- Endpoint runtime WordPress, instalasi plugin lokal, database aktual, dan perilaku klik entity **belum diperiksa ulang dalam follow-up ini**.
- Query workflow-run GitHub yang diperiksa tidak memberikan run yang dapat dipakai sebagai bukti PASS untuk baseline AI; tidak disimpulkan bahwa seluruh CI tidak pernah berjalan.
- PHPUnit tidak dijalankan oleh follow-up ini.

## 3. AMANAH SEO ENGINE V2 — Bukti Tambahan

### ENG-F-001 — Release gate failure: detail kegagalan terkonfirmasi

**Status bukti:** VERIFIED dari log job GitHub Actions untuk [Phase 12 Release Gate run 36844411667](https://github.com/amanahgroup123/amanah-seo-engine-v2/actions/runs/36844411667), commit `80e7c38a327baeef1d7ad7b13d006d453d5838b1`.

- PHP syntax validation: **PASS** — log menyatakan 298 file diperiksa dan tidak ditemukan syntax error.
- PHPUnit: **FAIL** — 112 tests, 275 assertions, **12 errors dan 4 failures**.
- Package build dan package validation: **SKIPPED** setelah PHPUnit gagal.
- PHP 7.4 matrix job dibatalkan setelah job PHP 8.2 gagal.

Kegagalan yang tercatat meliputi:

1. Pemanggilan fungsi WordPress `sanitize_key()` tidak tersedia pada konteks beberapa unit test, termasuk `PageBlueprintBuilder` dan `Configuration`.
2. Pemanggilan `wp_strip_all_tags()` tidak tersedia pada unit test `DeterministicQualityEngine`.
3. Test mencoba membuat mock untuk DTO final `ContentPlanData`.
4. Test merujuk tipe `Amanah\\SEOEngine\\Tests\\Unit\\GenerateKeywordStrategy` yang tidak ditemukan.
5. Test `CreateKeyword` memberikan satu argumen constructor, sementara implementasi yang diuji mensyaratkan dua.
6. Test lama mengharapkan `Schema::VERSION` bernilai 6, sedangkan implementasi pada baseline bernilai 11.
7. Test admin translation mengharapkan 20 tetapi memperoleh 0; test navigasi admin juga mengharapkan key `content` yang tidak ditemukan.

Daftar ini mendeskripsikan output CI, bukan keputusan akar masalah untuk setiap kasus. Beberapa error dapat berasal dari test harness atau test yang tertinggal, tetapi penentuan akar masalah membutuhkan diagnosis terkontrol. Tidak ada test yang diperbaiki atau dijalankan ulang pada follow-up ini.

**Tindak lanjut wajib:** diagnosis terpisah terhadap error/failure tersebut, koreksi hanya setelah scope disetujui, lalu jalankan ulang full release gate sampai PHPUnit, package build, dan package validation lulus pada matrix yang diwajibkan.

### ENG-F-002 — Database schema version contract

**Status bukti:** ketidakselarasan angka terkonfirmasi; makna version domain masih membutuhkan keputusan.

- `release/release.json`: `database_schema_version = 7`.
- `release/compatibility.json`: `database.schema_version = 7`.
- `plugin/Infrastructure/Database/Schema.php`: `Schema::VERSION = 11`.
- `MigrationManager` menyimpan progress migration berdasarkan versi migration yang terdaftar, dan source yang diperiksa mencakup pemeriksaan migration 11 dan 18.

Jangan mengganti angka 7 menjadi 11 atau 18 secara mekanis. Pemilik/arsitektur perlu menentukan apakah release metadata merepresentasikan schema compatibility contract yang berbeda dari migration ledger. Setelah keputusan itu, metadata, migration behavior, dan test harus konsisten.

### ENG-F-003 — License secret fallback

**Status bukti:** VERIFIED pada source code.

`LicenseSecretStore::key()` menurunkan key enkripsi dari `AUTH_KEY`, tetapi menggunakan literal fallback `amanah-seo-engine` jika konstanta itu tidak terdefinisi. Ini merupakan risiko konfigurasi karena fallback diketahui dan dapat melemahkan kerahasiaan secret pada lingkungan yang tidak memiliki `AUTH_KEY`.

**Tindak lanjut:** tentukan fail-closed behavior ketika secret konfigurasi wajib tidak tersedia, beserta compatibility/migration behavior untuk secret yang sudah tersimpan. Jangan mengubah mekanisme enkripsi tanpa scope dan test khusus.

### ENG-F-004 — Implementation status documentation drift

**Status bukti:** VERIFIED pada dokumen dan source tree yang diperiksa.

DOC-13/DOC-14 masih memuat status “Not Yet Implemented”, sementara repository memiliki struktur implementasi, migration, test, dan release workflow. Ini perlu dipisahkan menjadi status historis dokumen versus status code aktual; tidak boleh digunakan untuk mengklaim produk selesai atau lulus acceptance.

## 4. Daftar Keputusan / Persetujuan yang Masih Diperlukan

| ID | Keputusan atau pekerjaan | Status |
|---|---|---|
| AI-F-001 | Pilih slug kanonis Entity Profile dan scope regression test | OPEN — memerlukan paket koreksi terpisah |
| AI-F-002 | Tetapkan klasifikasi dan aturan penyimpanan license key | OPEN — keputusan kontrak keamanan |
| AI-F-003 | Setujui koreksi status operasional AI agar code/CI/runtime dibedakan | OPEN — dokumentasi saja |
| AI-F-004 | Setujui cakupan test controller save/persistence/redirect | OPEN — test package terpisah |
| ENG-F-001 | Diagnosis dan perbaiki 12 errors + 4 failures, lalu full release gate PASS | BLOCKED — pekerjaan terkontrol terpisah |
| ENG-F-002 | Tetapkan definisi schema version metadata versus migration ledger | OPEN — keputusan arsitektur/kontrak |
| ENG-F-003 | Tentukan fail-closed secret-key configuration dan penanganan secret tersimpan | OPEN — keputusan keamanan/kompatibilitas |
| ENG-F-004 | Rekonsiliasi status DOC-13/DOC-14 dengan status code dan acceptance evidence | OPEN — dokumentasi saja |
| AI / ENGINE | Bukti database dan runtime yang benar-benar aktif | NOT TESTED — perlu pemeriksaan pada lingkungan lokal |
| AI / ENGINE | Audit/review owner atas laporan dan disposition temuan | PENDING |

## 5. Disposition dan Next Gate

**Hasil follow-up:** bukti source dan CI yang dapat diakses telah diperdalam. Follow-up ini tidak menutup audit Phase 1.

- AMANAH SEO AI: **PARTIAL / Gate 1 BLOCKED**.
- AMANAH SEO ENGINE V2: **PARTIAL / Gate 1 BLOCKED**.
- AMANAH SEO Roadmap Phase 2 / Gate 2: **BLOCKED**.
- Tidak ada implementasi atau perubahan pada kedua repository produk.
- Tidak ada runtime/database PASS yang diklaim.

Langkah berikutnya yang paling aman adalah menyelesaikan pemeriksaan local/runtime dan persetujuan disposition secara terpisah. Untuk ENG-F-001, gunakan hasil CI yang terdokumentasi sebagai diagnosis awal; jangan rerun atau memperbaiki source sampai scope paket validasi/koreksi disetujui. Sinkronisasi repository lokal induk tetap mengikuti aturan bahwa GitHub `main` adalah baseline kanonis dan dilakukan setelah dokumentasi selesai.
