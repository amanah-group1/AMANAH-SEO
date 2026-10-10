> **STATUS: DOKUMEN LAMA / SUPERSEDED (2026-10-10).** Sumber acuan aktif untuk visi, tujuan, batas produk, dan alur utama adalah [Pedoman Induk AMANAH SEO](../PEDOMAN_INDUK_AMANAH_SEO.md). Isi di bawah dipertahankan hanya sebagai catatan historis; tidak boleh mengesampingkan pedoman induk baru. Materi lama hanya boleh diadopsi kembali setelah ditinjau dan disetujui secara eksplisit.

﻿# AMANAH SEO — Integration Contracts

## 1. Status Dokumen

- **Status:** APPROVED — prinsip dan kebutuhan kontrak disetujui; kontrak teknis final dan protokol tetap belum disetujui
- **Bukti persetujuan baseline:** [commit dfbaed6b3a79b77a7837fc002f9712241cf08192](https://github.com/amanahgroup123/AMANAH-SEO/commit/dfbaed6b3a79b77a7837fc002f9712241cf08192) (2026-10-09).
- **Tujuan:** Mendefinisikan kebutuhan kontrak integrasi lintas produk
- **Kontrak final:** Belum disetujui
- **Implementasi integrasi:** Belum dinyatakan tersedia
- **Protokol komunikasi:** Belum diputuskan
- **Versi kontrak:** Belum ditetapkan

Dokumen ini menetapkan kebutuhan kontrak secara konseptual. Dokumen ini tidak menyatakan bahwa API, adapter, endpoint, event, atau mekanisme integrasi telah tersedia.

## 2. Tujuan

Integration contracts dirancang untuk:

1. Membuat komunikasi antarproduk eksplisit dan dapat diuji.
2. Mencegah ketergantungan pada detail internal produk.
3. Menentukan struktur request, response, error, dan compatibility.
4. Menetapkan pemeriksaan identitas, authorization, dan entitlement yang relevan.
5. Membatasi pertukaran data sesuai kebutuhan.
6. Mendukung perubahan versi tanpa merusak integrasi yang telah disahkan.
7. Menyediakan dasar integration tests dan runtime acceptance tests.

## 3. Prinsip Kontrak

### 3.1 Explicit ownership

Setiap kontrak harus memiliki pemilik yang bertanggung jawab atas definisi, perubahan, dan compatibility.

### 3.2 Minimal data exchange

Pertukaran data dibatasi pada field yang dibutuhkan untuk tujuan kontrak. Jangan mengirim API key, password, atau credential lain hanya karena suatu komponen dapat mengaksesnya.

### 3.3 Validate both sides

Pengirim dan penerima harus memvalidasi data sesuai tanggung jawab masing-masing. Validasi client tidak menggantikan validasi server.

### 3.4 Authorization is mandatory where applicable

Setiap operasi terlindungi harus memeriksa principal, permission, dan entitlement yang relevan. Identitas produk atau instalasi tidak otomatis memberikan hak kepada pengguna.

### 3.5 Errors must be safe

Error harus memiliki kategori yang dapat diproses tanpa menampilkan secrets, stack traces sensitif, atau detail internal yang tidak diperlukan pengguna.

### 3.6 Version explicitly

Kontrak yang digunakan harus dapat diidentifikasi versinya. Perubahan breaking harus mengikuti proses review dan compatibility assessment.

## 4. Kontrak A — Product Manifest

**Status:** PROPOSED.

### Tujuan

Menyediakan metadata produk yang diperlukan untuk registrasi, compatibility checks, dan administrasi platform.

### Kandidat field

| Field | Tujuan | Ketentuan |
|---|---|---|
| `product_id` | Identitas stabil produk | Wajib unik dalam registry |
| `display_name` | Nama yang ditampilkan | Tidak digunakan sebagai satu-satunya identifier |
| `product_version` | Versi produk | Harus mengikuti versioning yang disepakati |
| `contract_versions` | Versi kontrak yang didukung | Harus sesuai kemampuan implementasi |
| `capabilities` | Capability yang didukung | Hanya capability yang benar-benar tersedia |
| `compatibility` | Batas kompatibilitas | Harus berbasis hasil verifikasi |
| `manifest_version` | Versi schema manifest | Diperlukan untuk evolusi schema |

### Aturan

- Manifest tidak boleh digunakan sebagai bukti tunggal bahwa suatu fitur berfungsi.
- Metadata harus divalidasi dan memiliki source of truth yang jelas.
- Field final dan format serialization belum ditentukan.

## 5. Kontrak B — Identity Context

**Status:** PROPOSED.

### Tujuan

Menentukan konteks identitas dan otorisasi untuk request yang melintasi batas produk.

### Kandidat informasi

- Principal atau subject identifier.
- Organization atau tenant identifier jika model tersebut disahkan.
- Product atau installation identifier bila relevan.
- Permission atau scope yang telah diverifikasi.
- Correlation ID.
- Timestamp atau expiry jika diperlukan oleh mekanisme keamanan.
- Audience atau intended recipient bila menggunakan token.

### Persyaratan

- Identitas harus diverifikasi oleh pihak penerima sesuai trust model.
- Data yang dikirim oleh client tidak boleh langsung dipercaya sebagai bukti permission.
- Token harus memiliki audience, masa berlaku, dan mekanisme validasi yang sesuai bila token digunakan.
- Token tidak boleh diterima oleh komponen yang bukan targetnya.
- Replay protection harus dinilai sesuai risiko dan metode autentikasi.

### Belum diputuskan

- Identity provider.
- Token format.
- Token issuance dan validation.
- Session federation.
- Tenant atau organization model.
- Mekanisme service-to-service authentication.

## 6. Kontrak C — Authorization

**Status:** PROPOSED.

### Tujuan

Memastikan bahwa principal hanya dapat melakukan operasi yang diizinkan.

### Informasi minimum untuk keputusan

- Principal yang telah diverifikasi.
- Resource yang akan diakses.
- Action yang diminta.
- Permission atau policy yang berlaku.
- Context tambahan yang diwajibkan oleh policy.

### Aturan

- Authorization dilakukan pada batas kepercayaan yang sesuai, umumnya di sisi server.
- Menu visibility bukan kontrol authorization.
- Product activation tidak otomatis memberikan semua permission.
- License validity tidak otomatis memberikan akses ke semua data.
- Permission administratif tidak boleh diberikan hanya karena pengguna memiliki license key.

### Hasil kandidat

- `ALLOW`
- `DENY`

Error internal dan alasan penolakan dapat dicatat secara terkontrol. Detail sensitif tidak harus ditampilkan kepada pengguna.

## 7. Kontrak D — Entitlement Check

**Status:** PROPOSED.

### Tujuan

Memeriksa apakah suatu license atau hak penggunaan mencakup capability yang diminta.

### Kandidat input

- Identitas license atau entitlement reference.
- Product identifier.
- Capability atau feature identifier.
- Context instalasi yang diperlukan.
- Product version jika relevan.

### Kandidat output

- Hasil pemeriksaan.
- Capability yang diizinkan.
- Batas penggunaan yang relevan jika telah disahkan.
- Waktu pemeriksaan atau masa berlaku hasil bila diperlukan.
- Kategori error jika keputusan tidak dapat diberikan.

### Aturan

- Entitlement bukan pengganti autentikasi atau authorization.
- Jangan menyimpulkan entitlement dari nama tier saja.
- Batas penggunaan harus berasal dari aturan komersial yang disahkan.
- Respons yang tidak valid tidak boleh dianggap sebagai persetujuan.
- Cache, offline behavior, dan grace period belum ditentukan.

## 8. Kontrak E — Activation Lifecycle

**Status:** PROPOSED.

### Tujuan

Mendefinisikan lifecycle aktivasi instalasi produk.

### Kandidat operasi

- Activate.
- Validate activation.
- Inspect activation status.
- Deactivate.
- Transfer activation.
- Revoke activation.
- Recover activation sesuai policy.

### Kandidat data

- Product identifier.
- License reference.
- Installation identifier.
- Activation status.
- Created atau updated timestamp.
- Status reason yang aman untuk ditampilkan.
- Metadata minimum untuk audit dan dukungan.

### Persyaratan

- Activation credential harus diperlakukan sebagai credential sensitif.
- Perubahan status harus diotorisasi.
- Operasi berulang harus memiliki perilaku yang ditentukan, termasuk idempotency bila diperlukan.
- Transfer atau revocation harus menangani risiko penggunaan ganda.
- Audit trail harus mencatat perubahan penting tanpa menyimpan secret mentah.

### Belum diputuskan

- Definisi instalasi.
- Apakah staging dan development dihitung sebagai aktivasi.
- Batas aktivasi per license.
- Offline activation.
- Recovery setelah kegagalan server atau database.
- Kebijakan transfer dan reset.

## 9. Kontrak F — Navigation dan Product Entry Point

**Status:** PROPOSED.

### Tujuan

Menyediakan navigasi lintas produk tanpa mencampurkan tanggung jawab UI dengan authorization.

### Persyaratan

- Entry point harus menunjuk ke halaman atau capability yang benar-benar tersedia.
- Link atau menu tidak boleh menjadi satu-satunya kontrol akses.
- Pengguna yang tidak berhak harus ditolak pada tujuan akhir melalui pemeriksaan server-side.
- Product unavailable, incompatible, atau inactive harus memiliki status UI yang jelas.
- Navigation metadata tidak boleh berisi credential.
- Perubahan route atau menu harus mempertahankan capability checks yang ada.

### Belum diputuskan

- Apakah navigasi berada di satu dashboard atau di dalam setiap produk.
- Mekanisme registrasi menu.
- Deep-linking dan redirect policy.
- Pengalaman pengguna untuk produk yang belum diaktivasi.

## 10. Kontrak G — Diagnostics dan Health Status

**Status:** PROPOSED.

### Tujuan

Menyediakan status operasional dan informasi diagnostik yang dapat dipahami tanpa mengekspos informasi sensitif.

### Kandidat kategori status

- `HEALTHY`
- `DEGRADED`
- `UNAVAILABLE`
- `INCOMPATIBLE`
- `NOT_CONFIGURED`
- `NOT_TESTED`

Kategori tersebut merupakan kandidat dan perlu distandardisasi sebelum implementasi.

### Persyaratan

- Health status tidak boleh mengandung API key atau token.
- Status konektivitas tidak boleh disamakan dengan status authorization.
- Health check tidak boleh mengubah data bisnis.
- Error publik harus dipisahkan dari diagnostic detail internal.
- Diagnostic detail hanya dapat diakses oleh principal yang berwenang.

## 11. Kontrak H — Audit Events

**Status:** PROPOSED.

### Tujuan

Mencatat operasi administratif dan perubahan lintas produk yang memerlukan jejak audit.

### Kandidat field

- Event identifier.
- Event type.
- Timestamp.
- Actor atau principal reference.
- Product identifier.
- Target resource reference.
- Outcome.
- Correlation ID.
- Metadata yang telah disaring.

### Persyaratan

- Jangan menyimpan password, token mentah, API key, atau credential sensitif dalam event.
- Akses terhadap audit log harus dibatasi.
- Retention dan deletion policy harus ditentukan.
- Integritas event dan kebutuhan tamper resistance harus dievaluasi.
- Kegagalan audit logging untuk operasi kritis harus memiliki kebijakan yang eksplisit.

## 12. Error Contract

**Status:** PROPOSED.

Gunakan kategori error yang konsisten. Kode berikut adalah contoh awal, bukan standar final:

| Candidate code | Makna |
|---|---|
| `INVALID_REQUEST` | Request tidak memenuhi schema atau aturan validasi |
| `UNAUTHENTICATED` | Identitas tidak dapat diverifikasi |
| `FORBIDDEN` | Principal tidak berhak melakukan operasi |
| `ENTITLEMENT_DENIED` | Hak penggunaan tidak mencakup capability |
| `ACTIVATION_INVALID` | Status aktivasi tidak memenuhi ketentuan |
| `INCOMPATIBLE_VERSION` | Versi tidak memenuhi compatibility requirements |
| `RESOURCE_NOT_FOUND` | Resource tidak tersedia atau tidak dapat ditemukan |
| `RATE_LIMITED` | Batas request terlampaui |
| `DEPENDENCY_UNAVAILABLE` | Komponen yang diperlukan tidak tersedia |
| `INTERNAL_ERROR` | Kegagalan internal yang tidak diklasifikasikan secara publik |

### Error response requirements

Respons error perlu menentukan:

- Error code.
- Pesan aman untuk pengguna atau pemanggil.
- Correlation ID jika tersedia.
- Retry guidance jika aman dan relevan.
- Detail diagnostik internal yang terpisah dan dibatasi aksesnya.

HTTP status code, payload schema, retry policy, dan transport behavior belum diputuskan.

## 13. Contract Versioning

Setiap kontrak yang disahkan perlu memiliki:

- Nama dan identifier kontrak.
- Versi schema.
- Pemilik kontrak.
- Daftar consumer dan provider.
- Compatibility policy.
- Perubahan breaking dan non-breaking.
- Deprecation policy.
- Acceptance tests.
- Migration guidance.

Perubahan tidak boleh langsung diterapkan pada semua produk tanpa memeriksa versi yang didukung.

## 14. Security Requirements

Sebelum kontrak disahkan, review minimal harus memeriksa:

- Authentication dan authorization.
- CSRF protection untuk operasi yang relevan.
- Input validation dan output encoding.
- Replay dan idempotency risks.
- Rate limiting sesuai risiko.
- Secret handling dan redaction.
- Privilege escalation.
- Tenant atau organization isolation bila relevan.
- Auditability.
- Timeout dan dependency failure.
- Version downgrade atau contract confusion.
- Data minimization.

Kontrol yang relevan harus memiliki test cases atau evidence verifikasi.

## 15. Contract Testing

Strategi pengujian perlu mencakup:

1. Schema validation.
2. Valid request dan valid response.
3. Missing atau malformed fields.
4. Unauthorized dan forbidden request.
5. Entitlement granted dan denied.
6. Invalid atau revoked activation.
7. Incompatible contract version.
8. Timeout dan dependency unavailable.
9. Duplicate request serta idempotency jika diperlukan.
10. Secret redaction.
11. Regression tests untuk consumer yang sudah didukung.
12. Runtime tests pada environment yang ditentukan.

Kontrak tidak dianggap tervalidasi hanya karena dokumentasinya lengkap.

## 16. Proses Persetujuan Kontrak

Sebelum kontrak berstatus CONFIRMED:

1. Audit provider dan consumer.
2. Tentukan ownership dan trust boundaries.
3. Tetapkan schema dan error semantics.
4. Tentukan authentication dan authorization requirements.
5. Review security dan data minimization.
6. Tentukan compatibility serta versioning.
7. Buat acceptance tests.
8. Tinjau failure handling.
9. Dokumentasikan risiko dan keputusan terbuka.
10. Dapatkan persetujuan eksplisit.

## 17. Open Decisions

- Transport dan protocol.
- Token atau credential model.
- Identity authority.
- License authority.
- Product manifest source of truth.
- Contract versioning policy.
- Offline dan cache policy.
- Activation lifecycle.
- Audit event retention.
- Health status semantics.
- Retry dan timeout policy.
- Compatibility matrix.
- Ownership setiap schema dan endpoint.

Semua pertanyaan tersebut harus tercatat di `10_OPEN_QUESTIONS.md` dan ditutup melalui keputusan terdokumentasi.

## 18. Traceability ke Open Questions

Bagian ini adalah indeks traceability untuk memastikan kontrak kandidat tidak disalahartikan sebagai keputusan final. Referensi Q mengarah ke docs/10_OPEN_QUESTIONS.md; seluruh Q yang disebutkan tetap berstatus OPEN sampai keputusan disetujui dan dicatat.

| Area kontrak | Bagian terkait | Pertanyaan terkait | Batas status |
|---|---|---|---|
| Product Manifest / product registry | §4 | Q-004, Q-020 | Field, source of truth, dan versioning final belum diputuskan |
| Identity Context | §5 | Q-001, Q-002, Q-003, Q-021 | Identity model, organisasi, role, dan security baseline belum diputuskan |
| Authorization | §6 | Q-003, Q-021 | Model permission dan standar keamanan tetap menunggu keputusan |
| Entitlement Check | §7 | Q-008, Q-009, Q-010, Q-012, Q-013 | Model lisensi, paket, limit, pemeriksaan, dan failure policy belum final |
| Activation Lifecycle | §8 | Q-011, Q-013, Q-018, Q-019 | Definisi instalasi, kegagalan, deployment, dan persistence perlu keputusan/evidence |
| Navigation / Product Entry Point | §9 | Q-007 | Pola navigasi dan deep link belum diputuskan |
| Diagnostics / Health Status | §10 | Q-022, Q-024 | Audit event, monitoring, dan respons insiden belum diputuskan |
| Audit Events | §11 | Q-021, Q-022, Q-023, Q-024 | Security baseline, retensi, backup, monitoring, dan ownership belum final |
| Error Contract | §12 | Q-005, Q-013, Q-020, Q-024 | Transport, retry, versioning, dan perilaku saat dependency gagal belum final |

Matriks ini menunjukkan ketergantungan dokumentasi, bukan penyelesaian pertanyaan. Jika satu area memerlukan pertanyaan tambahan, tambahkan pertanyaan di Open Questions dan referensikan kembali di sini; jangan mengubah status Q tanpa keputusan yang tercatat.

## 19. Dokumen Terkait

- `00_PROJECT_CHARTER.md`
- `01_DECISION_REGISTER.md`
- `02_ARCHITECTURE_BLUEPRINT.md`
- `03_PRODUCT_BOUNDARIES.md`
- `05_LICENSE_AND_ENTITLEMENTS.md`
- `06_SECURITY_AND_SECRETS.md`
- `08_AUDIT_AND_READINESS.md`
- `10_OPEN_QUESTIONS.md`
- `templates/ADR_TEMPLATE.md`
- `templates/AUDIT_FINDING_TEMPLATE.md`

## 20. Status Akhir

Semua kontrak dalam dokumen ini berstatus kandidat. Belum ada schema, endpoint, token format, atau protocol yang dinyatakan final.

Kontrak hanya dapat disahkan setelah audit, security review, compatibility assessment, dan approval sesuai governance proyek.
