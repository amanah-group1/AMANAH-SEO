> **STATUS: DOKUMEN LAMA / TIDAK BERLAKU SEBAGAI PEDOMAN AKTIF.** Mulai 2026-10-10, visi, tujuan, batas produk, dan alur utama ditentukan oleh [Pedoman Induk AMANAH SEO](PEDOMAN_INDUK_AMANAH_SEO.md). Dokumen ini dipertahankan untuk penelusuran historis dan tidak boleh dipakai untuk mengesampingkan pedoman induk baru. Jika ada isi yang masih berguna, adopsi kembali secara eksplisit setelah ditinjau; status keputusan lama tidak otomatis menjadi keputusan aktif.

---

﻿# AMANAH SEO — License and Entitlements

## 1. Status Dokumen

- **Status:** APPROVED — kerangka tata kelola lisensi disetujui; model lisensi, paket, harga, dan protokol aktivasi tetap belum diputuskan
- **Bukti persetujuan baseline:** [commit 0a6f591eb28dc2e28f1b2b3cd9fee953806363f2](https://github.com/amanahgroup123/AMANAH-SEO/commit/0a6f591eb28dc2e28f1b2b3cd9fee953806363f2) (2026-10-09).
- **Model lisensi final:** Belum ditetapkan
- **Paket dan harga:** Belum disahkan
- **Activation protocol:** Belum diputuskan
- **Implementasi:** Belum dinyatakan tersedia

Dokumen ini menetapkan kebutuhan rancangan pengelolaan lisensi. Dokumen ini tidak menyatakan bahwa sistem licensing telah dibuat, diuji, atau siap digunakan secara komersial.

## 2. Tujuan

Dokumen ini bertujuan untuk:

1. Mendefinisikan perbedaan license, entitlement, activation, authentication, dan authorization.
2. Menetapkan kandidat lifecycle lisensi.
3. Mengidentifikasi tanggung jawab platform induk dan produk.
4. Menyusun persyaratan keamanan dan audit.
5. Menentukan acceptance criteria sebelum implementasi.
6. Menjaga agar keputusan komersial tetap terpisah dari rancangan teknis.

## 3. Istilah dan Perbedaan

### 3.1 License

Representasi hak penggunaan produk berdasarkan ketentuan yang disahkan.

License record dapat memuat identifier, customer reference, product scope, status, masa berlaku, dan metadata komersial yang diperlukan.

License record bukan bukti autentikasi pengguna dan tidak otomatis memberikan semua permission.

### 3.2 Entitlement

Pernyataan mengenai capability atau hak penggunaan yang diberikan kepada pemegang lisensi.

Contoh konseptual:
- Menggunakan capability tertentu.
- Mengakses fitur yang termasuk dalam paket.
- Menggunakan sejumlah instalasi apabila batas tersebut telah disahkan.

Entitlement harus berasal dari aturan yang disetujui. Jangan menyimpulkan hak penggunaan hanya berdasarkan nama paket.

### 3.3 Activation

Pendaftaran atau pengikatan hak penggunaan dengan instalasi atau konteks penggunaan yang ditentukan.

Activation memiliki lifecycle tersendiri. Aktivasi yang valid tidak otomatis memberikan permission administratif atau membuktikan bahwa semua fitur produk kompatibel.

### 3.4 Activation Credential

Credential atau token yang digunakan untuk mengidentifikasi atau memvalidasi aktivasi sesuai protokol yang disahkan.

Credential ini harus dilindungi, dapat dicabut sesuai desain, dan tidak boleh dicatat sebagai nilai mentah dalam log.

### 3.5 Authentication

Proses memverifikasi identitas pengguna, service, atau principal.

### 3.6 Authorization

Proses menentukan apakah principal yang telah diverifikasi boleh melakukan operasi tertentu.

### 3.7 AI Provider API Key

Credential untuk mengakses layanan AI eksternal, misalnya provider yang dikonfigurasi oleh pengguna produk.

AI provider API key bukan license key, bukan activation credential, dan bukan credential untuk mengakses platform induk.

### 3.8 Internal Platform Credential

Credential yang digunakan untuk komunikasi internal atau operasi administratif platform. Credential ini memiliki trust boundary dan lifecycle tersendiri.

## 4. Prinsip Non-Equivalence

Kelima hal berikut harus diperlakukan secara terpisah:

| Konsep | Pertanyaan yang dijawab |
|---|---|
| License | Hak penggunaan apa yang telah diberikan? |
| Entitlement | Capability apa yang tercakup? |
| Activation | Instalasi atau konteks penggunaan mana yang terdaftar? |
| Authentication | Siapa atau service apa yang mengajukan request? |
| Authorization | Apakah principal boleh melakukan operasi ini? |

AI provider API key dan internal platform credentials merupakan kategori credential tambahan yang tidak menggantikan kelima konsep tersebut.

## 5. Kandidat Lifecycle License

**Status: PROPOSED.**

Lifecycle berikut merupakan rancangan awal yang harus disesuaikan dengan kebijakan bisnis:

- `PENDING` — menunggu penerbitan atau penyelesaian proses yang ditentukan.
- `ACTIVE` — berlaku sesuai aturan yang disahkan.
- `SUSPENDED` — ditangguhkan sementara.
- `EXPIRED` — masa berlaku telah berakhir.
- `REVOKED` — dicabut sesuai kewenangan dan kebijakan.
- `CANCELLED` — dibatalkan berdasarkan proses yang disahkan.

Status final, transisi yang diperbolehkan, dan konsekuensi masing-masing status belum ditetapkan.

### Persyaratan lifecycle

- Setiap transisi harus memiliki aturan yang jelas.
- Operasi yang mengubah status harus diotorisasi.
- Status akhir atau transisi yang tidak dapat dibalik harus ditangani secara eksplisit.
- Perubahan penting harus dapat diaudit.
- UI dan API tidak boleh memberikan status yang bertentangan.
- Perilaku terhadap aktivasi yang sudah ada harus ditentukan untuk setiap transisi yang relevan.

## 6. Kandidat Lifecycle Activation

**Status: PROPOSED.**

Status awal yang dapat dievaluasi:

- `ACTIVE`
- `INACTIVE`
- `SUSPENDED`
- `REVOKED`
- `EXPIRED`, jika activation memiliki masa berlaku tersendiri.

Status tersebut tidak boleh diterapkan sebelum hubungan lifecycle activation dengan license disepakati.

### Pertanyaan desain

- Apakah activation melekat pada website, instalasi WordPress, server, atau kombinasi identifier?
- Bagaimana instalasi development dan staging diperlakukan?
- Apakah aktivasi dapat dipindahkan?
- Bagaimana aktivasi dipulihkan setelah database atau server gagal?
- Bagaimana revocation dipropagasikan ke produk?
- Bagaimana perilaku produk ketika platform lisensi tidak tersedia?
- Bagaimana duplicate activation requests ditangani?

## 7. Candidate Entitlement Model

**Status: PROPOSED.**

Entitlement sebaiknya dimodelkan berdasarkan capability yang teridentifikasi, bukan hanya berdasarkan label paket.

Informasi kandidat:

- Product identifier.
- Feature atau capability identifier.
- Status entitlement.
- Batas penggunaan jika disahkan.
- Masa berlaku jika relevan.
- Referensi license.
- Metadata yang diperlukan untuk audit.
- Versi policy jika dibutuhkan.

### Prinsip

- Setiap capability yang dibatasi lisensi harus didefinisikan secara eksplisit.
- Capability yang tidak tercantum dalam aturan yang disahkan tidak boleh otomatis dianggap termasuk.
- Batas penggunaan harus memiliki definisi yang dapat diuji.
- Pemeriksaan entitlement tidak boleh menggantikan authorization.
- Perubahan entitlement harus memiliki aturan propagasi dan invalidation yang jelas.

## 8. Kandidat Product Entitlement Matrix

| Capability | AMANAH SEO | AMANAH SEO AI | AMANAH SEO ENGINE V2 |
|---|---|---|---|
| Platform dashboard | Ditentukan oleh akses platform | Tidak otomatis ditentukan oleh lisensi produk | Tidak otomatis ditentukan oleh lisensi produk |
| Product administration | Memerlukan permission administratif | Bukan entitlement default | Bukan entitlement default |
| AI analysis | Bukan pemilik fungsi secara default | Perlu audit dan pemetaan capability | Bukan pemilik fungsi secara default |
| Structured SEO workflow | Bukan pemilik fungsi secara default | Bukan pemilik fungsi secara default | Perlu audit dan pemetaan capability |
| Cross-product integration | Sesuai permission dan kontrak | Harus memenuhi kontrak yang disahkan | Harus memenuhi kontrak yang disahkan |

Matriks ini tidak menentukan fitur paket atau hak komersial final. Pemetaan capability aktual harus didasarkan pada audit kedua produk.

## 9. Paket Komersial dan Harga

**Status: OPEN — Menunggu keputusan bisnis.**

Nama paket seperti Personal, Business, dan Agency hanya merupakan opsi yang pernah dipertimbangkan. Nama tersebut belum disahkan.

Hal-hal yang belum diputuskan:

- Nama dan jumlah tier.
- Harga dan mata uang.
- Masa berlaku lisensi.
- Renewal dan cancellation.
- Refund policy.
- Trial atau evaluation period.
- Fitur yang disertakan pada setiap tier.
- Batas website atau instalasi.
- Batas pengguna atau organisasi.
- Hak penggunaan AMANAH SEO AI dan AMANAH SEO ENGINE V2.
- Paket bundle.
- Grace period.
- Dukungan pelanggan dan SLA.
- Aturan upgrade dan downgrade.

Tidak boleh ada harga, kuota, atau hak penggunaan yang dianggap final hanya karena telah dicantumkan dalam dokumen rancangan.

## 10. Website dan Installation Limits

Jika pembatasan instalasi atau website dipilih, definisinya harus disahkan sebelum implementasi.

Pertanyaan yang harus dijawab:

- Apa definisi satu website?
- Apakah subdomain dihitung terpisah?
- Bagaimana multisite WordPress diperlakukan?
- Apakah staging dan local development dihitung?
- Apakah domain dapat diganti?
- Bagaimana migrasi hosting diperlakukan?
- Apakah transfer aktivasi memiliki cooldown?
- Bagaimana batas dihitung dan diaudit?
- Bagaimana konflik atau duplicate registration diselesaikan?

Angka limit apa pun tetap berstatus PROPOSED sampai mendapat persetujuan eksplisit.

## 11. Authorization dan Enforcement

Pemeriksaan lisensi tidak boleh menjadi satu-satunya kontrol keamanan.

Untuk operasi terlindungi, sistem perlu mengevaluasi pemeriksaan yang relevan:

1. Authentication.
2. Authorization.
3. License validity.
4. Entitlement untuk capability.
5. Activation validity jika diwajibkan.
6. Product compatibility jika diperlukan.

Pemeriksaan harus dilakukan pada server atau trust boundary yang tepat. Menyembunyikan menu atau tombol bukan enforcement yang memadai.

### Aturan enforcement

- Operasi yang ditolak harus gagal secara aman.
- Operasi administratif harus memiliki permission tersendiri.
- Pengguna tidak boleh dapat mengubah status lisensi melalui manipulasi request client.
- License key tidak boleh diperlakukan sebagai password administrator.
- Kegagalan dependency tidak boleh otomatis menghasilkan akses yang diizinkan.
- Kebijakan offline atau cached entitlement harus memiliki batas dan aturan yang eksplisit.

## 12. Security Requirements

Rancangan licensing harus mengevaluasi:

- License key generation dan entropy.
- Penyimpanan credential dan token.
- Hashing atau encryption yang sesuai dengan tujuan.
- Key rotation dan revocation.
- Authorization untuk create, update, suspend, dan revoke.
- CSRF protection untuk operasi berbasis session.
- Rate limiting terhadap percobaan validasi yang berisiko.
- Replay dan duplicate request handling.
- Audit trail untuk perubahan penting.
- Secret redaction.
- Tenant isolation jika model multi-customer digunakan.
- Backup dan recovery.
- Pencegahan privilege escalation.

Metode teknis final harus dipilih berdasarkan threat model dan security review, bukan diasumsikan dari daftar ini.

## 13. Audit Trail

Perubahan berikut perlu dievaluasi untuk pencatatan audit:

- License created.
- License updated.
- License suspended.
- License revoked.
- Activation created.
- Activation transferred atau deactivated.
- Entitlement changed.
- Administrative override.
- Validation failures yang relevan terhadap keamanan.

Kandidat informasi event:

- Event identifier.
- Timestamp.
- Actor reference.
- Target license atau activation reference.
- Action.
- Outcome.
- Reason code.
- Correlation ID jika tersedia.

Jangan mencatat credential mentah, API key, token, atau informasi sensitif yang tidak diperlukan.

## 14. Failure dan Recovery

Sebelum implementasi, tetapkan perilaku untuk:

- License authority tidak tersedia.
- Database mengalami kegagalan.
- Respons validasi rusak atau tidak lengkap.
- License expired ketika produk sedang digunakan.
- License revoked ketika instalasi sedang offline.
- Aktivasi ganda.
- Aktivasi tidak konsisten setelah restore backup.
- Perbedaan waktu antara server.
- Cache entitlement yang kedaluwarsa.
- Versi produk tidak kompatibel.

Setiap kondisi harus memiliki kebijakan yang eksplisit, termasuk apakah operasi ditolak, ditunda, atau menggunakan hasil cache yang masih sah.

## 15. Acceptance Criteria

Sebelum licensing dianggap siap, pengujian minimal harus membuktikan:

1. License yang valid menghasilkan status yang sesuai.
2. License expired, suspended, atau revoked diperlakukan sesuai policy.
3. Entitlement yang tidak mencakup capability ditolak.
4. License tidak memberikan permission administratif secara otomatis.
5. Activation yang tidak valid ditolak sesuai policy.
6. Pengguna tanpa permission tidak dapat mengubah license.
7. Request yang tidak memiliki CSRF protection ditangani sesuai security design.
8. Duplicate request tidak merusak lifecycle.
9. Secret tidak muncul dalam UI, response, atau log yang tidak semestinya.
10. Error dependency tidak memberikan akses secara tidak sengaja.
11. Perubahan entitlement dan revocation mengikuti kebijakan propagasi yang disahkan.
12. Regression tests untuk fitur produk yang telah PASS tetap lulus.

Kriteria ini perlu disesuaikan dengan license model final dan harus memiliki evidence pengujian.

## 16. Open Decisions

- License authority dan source of truth.
- Format license identifier dan credential.
- Activation protocol.
- Entitlement schema.
- Commercial tiers dan harga.
- Website serta installation limits.
- Trial, renewal, cancellation, dan refund.
- Offline behavior dan grace period.
- Cache TTL dan revocation propagation.
- Audit retention.
- Kebijakan administrator override.
- Recovery setelah restore atau migrasi.
- Integrasi dengan kedua produk.

Keputusan tersebut harus dicatat dalam `01_DECISION_REGISTER.md` dan `10_OPEN_QUESTIONS.md`.

## 17. Dokumen Terkait

- `01_DECISION_REGISTER.md`
- `02_ARCHITECTURE_BLUEPRINT.md`
- `03_PRODUCT_BOUNDARIES.md`
- `04_INTEGRATION_CONTRACTS.md`
- `06_SECURITY_AND_SECRETS.md`
- `08_AUDIT_AND_READINESS.md`
- `10_OPEN_QUESTIONS.md`
- `templates/ADR_TEMPLATE.md`
- `templates/AUDIT_FINDING_TEMPLATE.md`

## 18. Status Akhir

Dokumen ini adalah rancangan licensing dan entitlement. Dokumen ini tidak menetapkan harga, tier, kuota, protokol aktivasi, atau implementasi final.

Semua keputusan yang memengaruhi akses pengguna, keamanan, dan hak komersial harus disetujui secara eksplisit serta divalidasi melalui audit dan pengujian.

## 19. Traceability Lifecycle ke Open Questions

Tabel berikut menghubungkan rancangan lisensi, entitlement, aktivasi, dan enforcement dengan pertanyaan yang belum diputuskan di docs/10_OPEN_QUESTIONS.md. Pemetaan ini tidak menutup atau mengubah status pertanyaan.

| Area | Bagian terkait | Pertanyaan terkait | Status / batasan |
|---|---|---|---|
| License lifecycle dan status | §5 | Q-008, Q-009 | Lifecycle kandidat; model lisensi dan paket komersial belum disahkan |
| Activation lifecycle | §6 | Q-011, Q-013, Q-018, Q-019 | Definisi instalasi, failure behavior, deployment, dan persistence belum final |
| Entitlement model dan feature limits | §7–§8 | Q-008, Q-009, Q-010, Q-012 | Capability, paket, limit, dan mekanisme pemeriksaan belum final |
| AI credentials dan cost allocation | §3.7, §12, §16 | Q-014, Q-015, Q-016 | Kepemilikan key, penyimpanan secret, dan pembebanan biaya menunggu keputusan/evidence |
| Authorization dan security enforcement | §11–§12 | Q-003, Q-021 | Role/permission dan security baseline perlu keputusan serta validasi |
| Audit trail dan operational recovery | §13–§14 | Q-022, Q-023, Q-024 | Retensi, backup, monitoring, dan respons insiden belum ditentukan |
| Contract compatibility | §16 | Q-005, Q-020 | Protokol komunikasi dan versioning belum final |

Jika temuan audit produk menghasilkan kebutuhan baru, perbarui Open Questions dan Decision Register secara konsisten. Status APPROVED pada dokumen ini menyetujui kerangka dan persyaratan, bukan lifecycle, harga, entitlement matrix, atau protokol aktivasi yang masih PROPOSED/OPEN.
