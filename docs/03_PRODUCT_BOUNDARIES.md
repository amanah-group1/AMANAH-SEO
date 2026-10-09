# AMANAH SEO — Product Boundaries

## 1. Status Dokumen

- **Status:** DRAFT — Menunggu review
- **Tujuan:** Menentukan batas tanggung jawab dan aturan perubahan antarproduk
- **Implementasi:** Belum disahkan oleh dokumen ini
- **Baseline produk:** Harus diverifikasi melalui audit
- **Integrasi:** Belum dinyatakan siap

Dokumen ini merupakan rancangan batas produk. Dokumen ini tidak menggantikan master documentation, kontrak, atau hasil audit masing-masing produk.

## 2. Produk dalam Cakupan

Ekosistem yang dibahas terdiri atas:

1. **AMANAH SEO** — platform induk yang direncanakan.
2. **AMANAH SEO AI** — produk AI dan SEO yang telah memiliki repository tersendiri.
3. **AMANAH SEO ENGINE V2** — produk workflow SEO terstruktur yang telah memiliki repository tersendiri.

Ketiganya tidak boleh dianggap sebagai satu codebase atau satu lifecycle hanya karena berada dalam ekosistem yang sama.

## 3. Prinsip Pemisahan

### 3.1 Satu produk, satu tanggung jawab utama

Setiap produk harus memiliki tanggung jawab utama yang jelas, dokumentasi baseline, lifecycle versi, dan kriteria penerimaan masing-masing.

### 3.2 Integrasi bukan pengambilalihan

Integrasi tidak memberikan hak otomatis kepada platform induk untuk mengambil alih business logic, database, konfigurasi, atau lifecycle internal produk.

### 3.3 Jangan mengasumsikan struktur internal

Nama class, tabel database, API, hook, capability, dan file implementasi hanya boleh dijadikan dasar keputusan setelah diverifikasi melalui audit.

### 3.4 Tidak ada perubahan lintas produk tanpa persetujuan

Perubahan yang memengaruhi lebih dari satu produk harus memiliki scope, dependency, acceptance criteria, regression plan, dan persetujuan yang jelas.

### 3.5 Kepemilikan data harus eksplisit

Komponen yang membaca atau menulis data milik produk lain harus memiliki kontrak dan otorisasi yang sesuai. Akses langsung ke database lintas produk tidak boleh diasumsikan sebagai mekanisme integrasi yang disetujui.

## 4. Batas AMANAH SEO — Platform Induk

### 4.1 Tanggung jawab kandidat

- Dashboard platform.
- Product registry.
- Administrasi akun dan akses platform.
- Administrasi lisensi dan entitlement.
- Administrasi aktivasi.
- Navigasi menuju produk.
- Pengaturan platform.
- Status integrasi dan diagnostik administratif.
- Audit trail atas operasi administratif yang relevan.
- Pengelolaan metadata compatibility sesuai kontrak yang disetujui.

### 4.2 Bukan tanggung jawab default

- Menjalankan ulang business logic analisis AI.
- Mengambil alih prompt, AI provider integration, atau persistence milik AMANAH SEO AI.
- Mengambil alih workflow state, approval logic, atau publishing logic milik AMANAH SEO ENGINE V2.
- Mengubah data produk tanpa kontrak.
- Mengakses seluruh credential produk secara otomatis.
- Menyatakan produk aktif atau kompatibel hanya berdasarkan keberadaan menu.

Fungsi di luar tanggung jawab default hanya boleh ditambahkan setelah kebutuhan, pemilik data, risiko, dan dampaknya disetujui.

## 5. Batas AMANAH SEO AI

### 5.1 Tanggung jawab produk

AMANAH SEO AI tetap bertanggung jawab atas fungsi AI dan SEO yang memang dimiliki produk berdasarkan implementasi dan dokumentasi aktualnya.

Area yang perlu diperiksa dalam audit antara lain:

- Entity Profile dan entity-related capabilities.
- Content Analyzer.
- AI provider configuration.
- Prompt construction dan AI execution.
- Validation serta sanitization output.
- Persistence dan presentation hasil analisis.
- Diagnostic handling.
- Product-specific settings dan permissions.

Daftar ini merupakan daftar audit awal. Keberadaan, status, dan batas setiap capability harus dibuktikan dari baseline aktual.

### 5.2 Batas terhadap platform induk

- Platform induk tidak boleh menggantikan AI execution engine tanpa keputusan arsitektur tersendiri.
- Platform induk tidak boleh membaca atau menulis hasil analisis langsung ke database produk tanpa kontrak.
- Pengaturan AI provider tidak boleh dipindahkan atau diduplikasi tanpa analisis kepemilikan credential.
- Integrasi lisensi tidak boleh merusak analisis, diagnostik, atau persistence yang telah terbukti berfungsi.
- Perubahan pada fitur produk harus mengikuti workflow audit dan approval AMANAH SEO AI.

## 6. Batas AMANAH SEO ENGINE V2

### 6.1 Tanggung jawab produk

AMANAH SEO ENGINE V2 tetap bertanggung jawab atas workflow SEO terstruktur sesuai master documentation dan implementasi aktualnya.

Area yang perlu diperiksa dalam audit antara lain:

- Keyword intelligence.
- Requirements dan strategy.
- Plan, brief, dan blueprint.
- Content generation.
- Evaluation dan refinement.
- Maturity dan review.
- Publishing information.
- Approval dan publishing lifecycle.
- Workflow state, transitions, serta persistence.

Daftar ini merupakan baseline audit konseptual dan bukan bukti bahwa seluruh tahap telah tersedia atau lulus pengujian.

### 6.2 Batas terhadap platform induk

- Platform induk tidak boleh mengubah urutan atau state transition workflow secara diam-diam.
- Platform induk tidak boleh melewati approval atau publishing controls.
- Platform induk tidak boleh menulis workflow state secara langsung tanpa kontrak.
- License enforcement tidak boleh menyebabkan kehilangan data atau perubahan workflow yang tidak dapat dipulihkan.
- Integrasi harus mempertahankan regression guarantees bagi tahap yang telah PASS.

## 7. Matriks Tanggung Jawab Awal

| Capability | AMANAH SEO | AMANAH SEO AI | AMANAH SEO ENGINE V2 |
|---|---|---|---|
| Platform dashboard | Pemilik yang direncanakan | Bukan pemilik default | Bukan pemilik default |
| Product registry | Pemilik yang diusulkan | Menyediakan metadata melalui kontrak | Menyediakan metadata melalui kontrak |
| Platform account administration | Pemilik yang diusulkan | Perlu audit integrasi | Perlu audit integrasi |
| License administration | Pemilik yang diusulkan | Enforcement sesuai kontrak | Enforcement sesuai kontrak |
| Activation lifecycle | Authority belum diputuskan | Perlu audit | Perlu audit |
| AI analysis execution | Bukan pemilik default | Pemilik produk | Bukan pemilik default |
| Entity/content analysis | Bukan pemilik default | Pemilik produk sesuai baseline | Bukan pemilik default |
| Structured SEO workflow | Bukan pemilik default | Bukan pemilik default | Pemilik produk sesuai baseline |
| Publishing workflow | Tidak mengambil alih secara default | Perlu verifikasi | Pemilik jika terbukti pada baseline |
| Product-specific persistence | Bukan pemilik default | Data milik produk sesuai audit | Data milik produk sesuai audit |
| Cross-product navigation | Pemilik yang diusulkan | Menyediakan entry point sesuai kontrak | Menyediakan entry point sesuai kontrak |
| Cross-product diagnostics | Mengelola ringkasan administratif sesuai kontrak | Menyediakan diagnostik produk | Menyediakan diagnostik produk |

**Catatan:** Matriks ini adalah rancangan tanggung jawab, bukan laporan implementasi. Frasa "pemilik yang diusulkan" tidak berarti capability telah tersedia.

## 8. Aturan Kepemilikan Data

Untuk setiap domain data, audit wajib menentukan:

1. Siapa pemilik data.
2. Komponen mana yang boleh membaca.
3. Komponen mana yang boleh menulis.
4. Bagaimana perubahan divalidasi.
5. Bagaimana integritas dan konsistensi dijaga.
6. Bagaimana perubahan diaudit.
7. Bagaimana migrasi dan rollback dilakukan.
8. Bagaimana data dipertahankan jika integrasi gagal.

Tidak boleh ada keputusan final tentang shared database, direct SQL access, atau data replication sebelum ownership dan failure modes ditinjau.

## 9. Aturan Perubahan Lintas Produk

Setiap perubahan lintas produk wajib memiliki:

- Identifikasi repository dan versi baseline.
- Penjelasan kebutuhan serta alasan perubahan.
- Scope file, modul, dan kontrak yang terdampak.
- Daftar fitur yang harus tetap PASS.
- Compatibility assessment.
- Security assessment.
- Implementation plan yang disetujui.
- Acceptance criteria.
- Regression test plan.
- Rollback atau recovery plan sesuai risiko.
- Bukti hasil validasi.

Jika scope awal tidak cukup untuk menyelesaikan masalah, pekerjaan harus dihentikan pada batas scope dan meminta persetujuan perubahan.

## 10. Siklus Audit dan Integrasi

### Tahap A — Baseline capture

Catat branch, commit, status repository, dokumentasi master, struktur code, konfigurasi relevan, database state yang dapat diperiksa, dan runtime target.

### Tahap B — Product audit

Audit AMANAH SEO AI dan AMANAH SEO ENGINE V2 secara terpisah. Jangan menganggap hasil audit satu produk berlaku untuk produk lain.

### Tahap C — Boundary analysis

Identifikasi fungsi yang tumpang tindih, dependency tersembunyi, kepemilikan data, kebutuhan identitas, dan titik integrasi potensial.

### Tahap D — Contract design

Rumuskan kontrak yang diperlukan, termasuk input/output, authorization, error handling, versioning, dan compatibility.

### Tahap E — Readiness review

Evaluasi apakah kontrak dan implementasi yang ada memenuhi kebutuhan integrasi. Catat blocker dan risiko yang belum diterima.

### Tahap F — Approval

Minta persetujuan eksplisit untuk scope implementasi, urutan perubahan, acceptance criteria, dan regression plan.

### Tahap G — Controlled implementation

Implementasikan hanya scope yang disetujui, lalu validasi masing-masing produk dan integrasi.

## 11. Perlindungan Fitur yang Sudah PASS

Sebelum implementasi:

- Identifikasi test dan runtime evidence yang membuktikan status PASS.
- Rekam commit baseline.
- Buat daftar regression tests yang wajib tetap lulus.
- Tentukan dampak yang diperbolehkan.
- Hindari refactoring yang tidak diperlukan untuk scope.

Sesudah implementasi:

- Jalankan kembali regression tests yang relevan.
- Bandingkan hasil dengan baseline.
- Catat perubahan perilaku.
- Jangan melaporkan PASS jika test gagal, belum dijalankan, atau evidence tidak tersedia.

## 12. Kriteria Kesiapan Integrasi

Integrasi tidak boleh dianggap siap sampai:

- Tanggung jawab masing-masing produk telah diverifikasi.
- Data ownership dan trust boundaries telah disepakati.
- Identity dan authorization contract tersedia.
- Entitlement dan activation contract telah disetujui bila diperlukan.
- Versi produk dan compatibility requirements diketahui.
- Error handling serta offline behavior ditentukan.
- Secret handling dan audit trail ditinjau.
- Regression tests tersedia.
- Runtime acceptance criteria dapat diuji.
- Blocker kritis telah diselesaikan atau diterima secara eksplisit oleh pihak berwenang.

## 13. Kondisi yang Memerlukan Penghentian

Hentikan pekerjaan integrasi dan lakukan review apabila:

- Scope memerlukan perubahan di luar persetujuan.
- Data ownership tidak jelas.
- Integrasi membutuhkan akses credential yang tidak diotorisasi.
- Fitur yang telah PASS mengalami regresi.
- Contract mismatch dapat merusak data atau workflow.
- Security control belum dapat diverifikasi.
- Tidak ada cara yang memadai untuk menguji atau melakukan rollback atas perubahan berisiko.

## 14. Dokumen Terkait

- `00_PROJECT_CHARTER.md`
- `01_DECISION_REGISTER.md`
- `02_ARCHITECTURE_BLUEPRINT.md`
- `04_INTEGRATION_CONTRACTS.md`
- `05_LICENSE_AND_ENTITLEMENTS.md`
- `06_SECURITY_AND_SECRETS.md`
- `08_AUDIT_AND_READINESS.md`
- `09_ROADMAP_AND_GATES.md`
- `10_OPEN_QUESTIONS.md`
- `templates/PRODUCT_AUDIT_CHECKLIST.md`
- `templates/IMPLEMENTATION_PLAN_TEMPLATE.md`

## 15. Status Akhir

Dokumen ini menetapkan batas tanggung jawab awal untuk ditinjau. Dokumen ini tidak membuktikan kesiapan integrasi, tidak mengesahkan akses lintas produk, dan tidak memberikan izin untuk mengubah repository produk.

Semua batas final harus dikonfirmasi berdasarkan audit dan keputusan yang terdokumentasi.
