# Paket Tunggal: Kesiapan Implementasi MVP dan Migrasi Modul AMANAH SEO

**Versi:** 1.0-controlled-package  
**Tanggal:** 2026-10-11  
**Status:** AUDIT REPOSITORI SELESAI SEBAGIAN; BUKTI RUNTIME MENUNGGU VERIFIKASI LANGSUNG  
**Acuan keputusan:** [Pedoman Induk](PEDOMAN_INDUK_AMANAH_SEO.md), [Lingkup MVP](11_MVP_SCOPE_PROPOSAL.md)  
**Menggantikan paket persiapan lama:** [Paket Kesiapan MVP](12_MVP_IMPLEMENTATION_PACKAGE.md)  
**Tujuan:** menggabungkan audit kesiapan WordPress, pembuktian sumber data WordPress/GSC, audit source AMANAH SEO AI dan AMANAH SEO ENGINE V2, serta urutan migrasi ke satu plugin dalam satu paket terkendali.

> **Batas penting:** dokumen ini menyelesaikan audit statis yang dapat dilakukan dari repository dan menyatukan semua pekerjaan tersisa dalam satu paket. Akses live WordPress, izin GSC, backup database lokal, serta uji runtime tidak dapat diklaim lulus dari pemeriksaan GitHub saja. Tidak ada kode produk, database, kredensial, atau runtime yang diubah oleh paket ini. Migrasi kode baru dimulai setelah bukti runtime dilampirkan dan persetujuan implementasi diberikan.

## 1. Keputusan dan target yang mengikat

1. Target akhir adalah **satu plugin WordPress yang dipasang pengguna: AMANAH SEO**.
2. Pengguna cukup memasang AMANAH SEO dan menggunakan satu lisensi AMANAH SEO untuk aktivasi produk; tidak diwajibkan memasang/mengaktifkan plugin AI atau Engine secara terpisah.
3. Plugin induk memiliki **Core AMANAH SEO**, modul internal AI (baseline dari repository AMANAH SEO AI), dan modul internal Engine (baseline dari repository AMANAH SEO ENGINE V2).
4. Repository AI dan Engine tetap baseline sumber/audit; keduanya tidak dipasang sebagai plugin runtime tambahan pada target akhir.
5. **Lifecycle Manager** di plugin induk menjadi koordinator tunggal lifecycle internal. Setiap modul tetap memiliki migrasi domain dan versi schema yang dapat dilacak; koordinator mengatur urutan, hasil, logging, dan pemulihan.
6. Keputusan satu plugin bukan izin menyalin seluruh fitur AI/Engine ke MVP. Siklus MVP dasar harus dapat berjalan tanpa fitur modul yang belum dipindahkan dan belum dibuktikan.
7. WordPress internal dipilih untuk data website/konten yang benar-benar dapat diakses. Google Search Console (GSC) dipilih untuk metrik performa pencarian, setelah properti dan akses baca-saja dibuktikan.
8. Tidak ada angka, koneksi, migrasi, atau tes yang boleh disebut PASS tanpa bukti aktual.

## 2. Ringkasan status

| Area | Status saat paket dibuat | Batas klaim |
|---|---|---|
| Pedoman Induk dan keputusan MVP | Terkonsolidasi di `main` | Selesai secara konseptual |
| Audit struktur repository utama AMANAH SEO | Repository/commit dapat diperiksa | Belum sama dengan uji runtime |
| Audit statis source AI | Baseline `main` dibaca; temuan database/kredensial dicatat | Belum aman untuk migrasi langsung |
| Audit statis source Engine | Baseline `main` dibaca; temuan migrasi/lifecycle/lisensi dicatat | Belum aman untuk migrasi langsung |
| WordPress testing untuk produk utama | Belum ada bukti live yang terlampir pada audit ini | BELUM DIUJI |
| Pembacaan data WordPress | Belum ada bukti sampel aktual dari target AMANAH SEO | BELUM DIUJI |
| GSC property dan API read-only | Tidak tersedia bukti koneksi/property/sampel dalam audit ini | BELUM DIUJI |
| Backup dan restore database target | Belum ada bukti backup/restore aktual | BELUM DIUJI |
| Migrasi modul ke satu plugin | Belum dilakukan | DILARANG sebelum gate terpenuhi |
| CI/runtime acceptance | Tidak ada hasil yang dilampirkan untuk paket ini | BELUM DIUJI |

### Baseline repository yang diaudit

| Komponen | Repository | Ref/commit baseline saat audit |
|---|---|---|
| Pedoman/produk induk | `amanahgroup123/AMANAH-SEO` | `bd6a3fd625924df54537bb39f766d367069aff42` |
| Modul AI | `amanahgroup123/AMANAH-SEO-AI` | `cd83b80fc7030651cee020391081279a9a36e27f` |
| Modul Engine | `amanahgroup123/amanah-seo-engine-v2` | `80e7c38a327baeef1d7ad7b13d006d453d5838b1` |

Commit tersebut adalah snapshot audit, bukan jaminan commit itu tetap menjadi HEAD saat migrasi dimulai. Paket implementasi wajib mengunci ulang SHA setiap repository dan mengulang pemeriksaan perubahan sejak snapshot ini.

## 3. Temuan audit source — AMANAH SEO AI

### 3.1 Database dan installer

File yang diperiksa:
- `plugin/includes/class-install.php` — SHA `bbf923d846a434c047c326fb9aab6186cfd84970`
- `00_MASTER_DOCUMENTATION/04_AMANAH_SEO_AI_DATABASE_DESIGN_DOCUMENT_v1.0.sql` — SHA `6ee0a8b20c94d347b9dd5824d3b4faed421d4248`
- `00_MASTER_DOCUMENTATION/TQ-005_PHASE_2_MIGRATION_SYSTEM.md` — SHA `5e4ea3a86c8fc19acf306e23d86d86a82efbfbf7`

Temuan:
- Installer menyatakan schema version `1.3.0`, menggunakan option `amanah_version` dan `amanah_installed`.
- Implementasi aktif mendefinisikan 11 tabel: `amanah_entities`, `amanah_entity_topics`, `amanah_audits`, `amanah_content_analyses`, `amanah_content_plan`, `amanah_schema`, `amanah_ai_logs`, `amanah_tasks`, `amanah_settings`, `amanah_article_packages`, dan `amanah_license`.
- Dokumen desain SQL lama tidak sepenuhnya cocok dengan implementasi aktif: misalnya nama `amanah_content_analysis` (tunggal) dibanding `amanah_content_analyses` (jamak), dan daftar tabel tidak sepenuhnya sama. **Kode aktif dan data aktual harus direkonsiliasi sebelum migrasi; dokumen lama tidak boleh dipakai sebagai satu-satunya schema authority.**
- Installer memindahkan tabel legacy `amanah_content_analysis` ke `amanah_content_analysis_legacy`. Jika tabel legacy dan tabel arsip sudah sama-sama ada, implementasi yang diperiksa menjalankan `DROP TABLE` pada tabel legacy. Ini adalah **risiko kehilangan data** sampai isi, jumlah baris, dan aturan retensi diperiksa; jangan jalankan installer/migrasi ini secara buta pada database gabungan.
- Repository pada baseline ini tidak menampilkan `uninstall.php` pada root tree. Kebijakan cleanup harus dikonfirmasi melalui seluruh lifecycle dan artefak rilis, bukan diasumsikan.

### 3.2 Kredensial AI dan pengaturan

File yang diperiksa:
- `plugin/modules/ai/class-ai-credentials.php` — SHA `ccb4d248d1670311bc1008e96177adae67b2fa8a`
- `plugin/modules/ai/class-ai-settings.php` — SHA `8728823b830ad4be66670ff50627144595498d7c`
- `00_MASTER_DOCUMENTATION/TQ-008_AI_API_CREDENTIAL_ENCRYPTION.md` — SHA `85fe45979c3bca79382f35f889d77bf73bf8942e`

Temuan:
- Kredensial disimpan melalui tabel pengaturan `amanah_settings`; implementasi enkripsi menggunakan Sodium `sodium_crypto_secretbox`, nonce acak, envelope berversi, dan derivasi kunci dari `wp_salt('auth')`.
- Perubahan WordPress auth salt dapat membuat credential lama tidak dapat didekripsi; desain terdokumentasi mengharuskan credential dimasukkan ulang bila pemulihan tidak mungkin.
- Migrasi tidak boleh menyalin nilai credential terenkripsi ke tempat lain dan menganggapnya tetap dapat didekripsi. Migrasi harus memilih salah satu: pertahankan lokasi/kunci lama yang kompatibel secara aman, atau minta pengguna memasukkan ulang credential. Tidak boleh ada plaintext key di log, output audit, dump yang dibagikan, atau repository.
- Tabel `amanah_settings` berisi konfigurasi lain, sehingga migrasi harus memetakan key per key dan tidak memindahkan seluruh tabel tanpa filter/kontrak.

## 4. Temuan audit source — AMANAH SEO ENGINE V2

### 4.1 Migrasi dan schema version

File yang diperiksa:
- `plugin/Infrastructure/Migration/MigrationManager.php` — SHA `7ff5b17360a72c8784c8aeaaa4a156927c1a06b7`
- `plugin/Infrastructure/Database/Schema.php` — SHA `2710e3b92f1991f7303f90b4665b357fc861a5ce`
- `plugin/Infrastructure/Migration/Migrations/Version17RemoveLegacySerpCredentials.php` — SHA `34e5a925b85a5f19da316938d660cfc616fc7f78`
- `plugin/Infrastructure/Migration/Migrations/Version18ContentIntelligenceFoundation.php` — SHA `ca37b2ccc6ec0ae188f3c244cbaf46f85307fc93`

Temuan:
- Migration manager menyimpan versi pada option `amanah_seo_engine_database_version`, mengurutkan migrasi yang terdaftar, menjalankan `up()`, lalu memperbarui option setelah tiap migrasi.
- Ada migrasi bernomor 1–8 dan 11–18; file `Version9SEOScan.php` pada baseline ini dilaporkan menggunakan version 10, dan tidak ada file bernama `Version10...` pada inventory. Urutan dan maksud nomor ini harus diverifikasi terhadap kelas yang benar-benar diregistrasikan dan data target sebelum menyatukan version marker.
- `Schema::VERSION` bernilai 11, sementara MigrationManager menggunakan option version yang dapat mencapai 18. Keduanya mungkin merepresentasikan lapisan berbeda, tetapi kontrak dan urutan pemanggilannya harus dibuktikan sebelum Lifecycle Manager induk mengorkestrasi keduanya.
- Migration manager memiliki perbaikan schema drift khusus (termasuk tabel issue/recommendation dan content-intelligence). Jalur perbaikan ini wajib diuji pada database yang sengaja dibuat tidak lengkap; jangan diasumsikan idempotent hanya dari nama metode.
- Migrasi 17 menghapus entry credential legacy DataForSEO secara spesifik. Ini perlu dipertahankan sebagai perilaku domain dan diuji agar tidak menghapus provider credentials lain.
- Engine mempunyai banyak tabel domain (keyword, search intent, cluster, owner URL, strategi/brief/blueprint, konten, kualitas, internal link, cannibalization, AI provider/model/task/usage, jobs, scan, issue/recommendation, portability, diagnostics, dan intelligence/requirements). Inventaris fisik aktual harus dibuat dari database target, bukan hanya dari daftar kode.

### 4.2 Lifecycle, cleanup, lisensi

File yang diperiksa:
- `plugin/Infrastructure/WordPress/Lifecycle/WordPressLifecycle.php` — SHA `882e3117c1709e1e42b318c0f993042a2cb35ffa`
- `uninstall.php` — SHA `ef8c86c902b340e21c26667f705e0fb88906e499`
- `plugin/Platform/License/LicenseSecretStore.php` — SHA `8982f7a53781ff184a2bcd5f7109c7c2696dd3c7`

Temuan:
- Deactivation membersihkan scheduled hook `amanah_seo_engine_run_jobs`.
- Uninstall Engine menghapus data hanya bila `delete_data_on_uninstall` bernilai true; saat jalur itu berjalan, ia menghapus tabel domain dan sejumlah options.
- Daftar cleanup Engine juga menghapus option `amanah_seo_ai_credentials`. Ini berpotensi beririsan dengan data legacy/credential AI; sebelum migrasi, identifikasi apakah option tersebut berisi data nyata, siapa pemiliknya, dan apakah ada tabel/format lain. Jangan biarkan uninstall modul internal menghapus data milik modul lain.
- `LicenseSecretStore` memakai AES-256-GCM, tetapi derivasi kunci memiliki fallback literal `amanah-seo-engine` bila `AUTH_KEY` tidak didefinisikan. Ini tidak boleh dipindahkan ke desain induk sebagai pola aman. Audit lisensi wajib memetakan option, format secret, endpoint, cache, grace period, entitlement, domain binding, update path, dan efek kehilangan koneksi sebelum migrasi.
- File `composer.json` mendeklarasikan PHP `>=7.4`, PHPUnit 9.6, WPCS, dan PHP Parallel Lint. Target versi PHP/WordPress plugin induk harus ditetapkan dari environment dan tes aktual; jangan menyamakan kompatibilitas deklaratif dengan kompatibilitas runtime yang sudah terbukti.

## 5. Risiko migrasi lintas modul — gerbang wajib

| ID | Risiko | Tindakan wajib sebelum migrasi |
|---|---|---|
| MIG-01 | Nama/schema tabel AI berbeda antara dokumen dan kode | Inventaris schema aktual, row count, constraints/index, dan pemilik setiap tabel; buat mapping sumber→target |
| MIG-02 | Installer AI dapat menghapus tabel legacy bila tabel archive sudah ada | Reproduksi pada salinan DB; ubah strategi migrasi agar tidak destruktif; uji preservasi row |
| MIG-03 | Credential AI terikat ke `wp_salt('auth')` | Tentukan kompatibilitas kunci atau prosedur re-entry; jangan menyalin ciphertext tanpa validasi |
| MIG-04 | Version marker AI dan Engine berbeda | Pertahankan marker legacy, tambahkan marker modul induk terpisah; jangan menimpa option lama sebelum verifikasi |
| MIG-05 | Engine `Schema::VERSION=11` vs migration manager hingga 18 | Dokumentasikan lifecycle sebenarnya dan uji fresh install/upgrade/drift |
| MIG-06 | Nomor file migrasi Engine 9/10 tidak selaras | Periksa seluruh registrasi/version class dan uji migrasi 8→current pada clone |
| MIG-07 | Cleanup Engine menghapus option `amanah_seo_ai_credentials` | Kepemilikan option dipastikan; hapus perilaku cross-module dan uji uninstall induk |
| MIG-08 | License secrets/entitlement/updater tidak seragam | Peta lisensi dan endpoint; jangan menggabungkan secret stores atau lisensi sebelum kontrak kompatibilitas disetujui |
| MIG-09 | Hook, cron, menu, capability, REST/AJAX, class names dapat bentrok | Inventaris hook/class/route/capability; namespace/adapter internal; uji tidak ada double registration |
| MIG-10 | Rollback setelah data/schema berubah belum terbukti | Backup database + files, rehearsal restore ke clone, migration journal, stop-on-failure, dan rencana forward-fix/rollback eksplisit |
| MIG-11 | Perbedaan versi PHP/WordPress/runtime | Catat versi aktual dan jalankan unit, static/lint, integration, activation/upgrade/deactivation/uninstall tests |
| MIG-12 | Satu plugin dapat memuat modul yang gagal | Core harus tetap hidup; modul gagal dinonaktifkan dengan diagnostik; jangan fatal error atau menjalankan migration parsial diam-diam |

**Larangan:** jangan menjalankan kedua installer/lifecycle lama secara otomatis di bawah plugin induk. Jangan menggabungkan tabel hanya karena nama mirip. Jangan menghapus tabel/options legacy pada migrasi pertama. Jangan mematikan plugin sumber sampai migrasi dan restore lulus.

## 6. Pembuktian WordPress testing — bukti yang diperlukan

Lingkungan WordPress testing produk utama belum dibuktikan dari akses yang tersedia dalam audit ini. Jangan menganggap site `amanah-group.test` atau plugin AMANAH SEO AI yang pernah diuji sebagai runtime AMANAH SEO utama tanpa konfirmasi dari path dan daftar plugin.

### Bukti minimum WP-01

- Alamat/site label yang secara eksplisit ditetapkan untuk pengujian **AMANAH SEO utama**.
- Versi WordPress, PHP, database, dan plugin aktif.
- Path instalasi WordPress dan path plugin target.
- Login admin dapat dilakukan; user memiliki hak administrator.
- Plugin AMANAH SEO dapat diaktifkan/deaktivasi pada lingkungan testing tanpa fatal error.
- Backup database dan folder WordPress/plugin dibuat; restore ke salinan terpisah dibuktikan sebelum migrasi data.
- Sampel post/page non-rahasia tersedia atau alasan bila tidak tersedia.
- Log error dan hasil uji disanitasi; jangan kirim password, salts, API keys, cookies, atau database dump ke chat/GitHub.

### Cara mengumpulkan bukti lokal (PowerShell, read-only)

Perintah ini hanya mengumpulkan informasi dasar; tidak mengubah file/database dan tidak mencetak credential:

```powershell
$WpRoot = "F:\WP LOKAL TES\wordpress"
$PluginMain = Join-Path $WpRoot "wp-content\plugins\amanah-seo"
$PluginAlt = Join-Path $WpRoot "wp-content\plugins\AMANAH SEO"

Write-Output "=== WordPress target paths ==="
[pscustomobject]@{
  WpRootExists = Test-Path $WpRoot
  WpConfigExists = Test-Path (Join-Path $WpRoot "wp-config.php")
  AmanahSeoSlugPathExists = Test-Path $PluginMain
  AmanahSeoNamedPathExists = Test-Path $PluginAlt
} | Format-List

Write-Output "=== PHP CLI ==="
php -v

Write-Output "=== Local HTTP response ==="
try {
  $r = Invoke-WebRequest -Uri "http://amanah-group.test/wp-login.php" -Method Get -MaximumRedirection 3
  [pscustomobject]@{ StatusCode = [int]$r.StatusCode; FinalUri = $r.BaseResponse.ResponseUri.AbsoluteUri } | Format-List
} catch {
  Write-Output ("HTTP_CHECK_FAILED: " + $_.Exception.GetType().Name)
}

Write-Output "=== WordPress plugin files matching AMANAH SEO ==="
$pluginsRoot = Join-Path $WpRoot "wp-content\plugins"
if (Test-Path $pluginsRoot) {
  Get-ChildItem $pluginsRoot -Directory |
    Where-Object { $_.Name -match "amanah" } |
    Select-Object Name, FullName
}
```

**Catatan:** path/site di atas adalah petunjuk dari lingkungan lokal yang pernah disebut, bukan bukti bahwa itu target AMANAH SEO utama. Sebelum menetapkan PASS, pastikan pemilik mengidentifikasi target yang benar. Jika target berbeda, ganti variabelnya; jangan copy plugin ke target berdasarkan nama folder semata.

Setelah target dipastikan, hasil admin UI harus mencakup: aktivasi, halaman utama, satu website testing, tambah/lihat/update status, hak akses ditolak untuk pengguna tanpa capability, error/failure state, serta uji deactivate/activate. Jalankan restore drill pada clone sebelum uji upgrade/migrasi.

## 7. Pembuktian Google Search Console

GSC bukan syarat siklus dasar MVP; tetapi akses GSC harus dibuktikan sebelum fitur metrik pencarian dikembangkan atau dinyatakan lulus.

### Bukti minimum GSC-01

1. Property GSC yang benar dan jenis property (Domain atau URL-prefix); URL/property cocok dengan website yang disetujui.
2. Akun integrasi mempunyai akses baca-saja yang sah. Jika memakai OAuth API, gunakan scope minimum `https://www.googleapis.com/auth/webmasters.readonly`.
3. Satu permintaan Search Analytics berhasil membaca data untuk rentang tanggal yang tersedia, dengan contoh field minimal: date, page, query (hanya bila diizinkan), clicks, impressions, ctr, position.
4. Simpan waktu pengambilan, rentang tanggal, property identifier yang aman untuk dicatat, jumlah baris, dan status API.
5. Uji property tanpa akses, data kosong, keterlambatan data, partial rows, token expired/revoked, dan rate limit.
6. Aplikasi menandai data yang tidak tersedia/parsial/usang; tidak mengganti data hilang menjadi nol dan tidak menyatakan posisi sebagai ranking real-time.
7. Bukti yang diserahkan hanya metadata/sampel tersanitasi. Jangan bagikan access token, refresh token, OAuth client secret, cookie, atau file kredensial.

**Status GSC pada paket ini: BELUM DIUJI.** Tidak ada koneksi Google atau bukti property/API yang dapat dibaca dalam pemeriksaan repository. Jangan buat credentials baru atau meminta pemilik menempel rahasia ke chat. Jika akses belum tersedia, tandai GSC sebagai terhambat dan lanjutkan siklus MVP dengan data WordPress yang benar-benar dapat dibaca dan/atau temuan manual yang diberi label.

## 8. Urutan pelaksanaan dalam satu package

Paket ini mengatur urutan yang sama untuk menghindari pekerjaan terpisah yang tidak terkendali.

### Gate 0 — kunci baseline dan target
- Konfirmasi commit SHA terkini untuk tiga repository.
- Konfirmasi satu target WordPress testing untuk produk utama AMANAH SEO.
- Catat PHP/WordPress/database versions, plugin path, aktif/nonaktif, dan hak admin.
- Simpan daftar file baseline dan status working tree; jangan mencampur file lokal tak terlacak ke paket.

### Gate 1 — backup dan bukti runtime
- Backup files + database; restore ke clone terpisah dan verifikasi row count dasar.
- Buktikan plugin induk dapat diaktifkan dan data WordPress dapat dibaca.
- GSC hanya menjadi gerbang jika scope fitur metrik dipilih.
- Jika backup/restore belum berhasil, berhenti sebelum migrasi.

### Gate 2 — kontrak modul dan data
- Tetapkan bootstrap/loader internal, namespace, kontrak Core↔AI dan Core↔Engine, ownership table/option, capability, cron, route, logging, serta error boundary.
- Tetapkan nama/version marker modul internal yang baru tanpa menimpa marker legacy.
- Tetapkan credential handling (pertahankan kompatibilitas yang aman atau re-entry) dan license/entitlement handling.
- Buat mapping schema per tabel dan per option; tandai keep/migrate/archive/manual re-entry/retire, dengan alasan dan row-count checks.
- Tentukan migration journal, idempotency, stop-on-error, backup reference, dan prosedur recovery.

### Gate 3 — migrasi dalam satu PR terkontrol, urutan internal terpisah
1. **Core shell**: plugin tunggal, loader, Lifecycle Manager, capability checks, health/diagnostics; belum menjalankan installer AI/Engine.
2. **MVP core**: siklus website→temuan→prioritas→tugas→persetujuan→hasil, dengan data WordPress/manual yang terbukti.
3. **AI module**: adapter internal dan migrasi terkontrol setelah kontrak settings/credentials, table map, provider behavior, dan regression tests disetujui.
4. **Engine module**: adapter internal setelah version sequence, table ownership, job scheduling, workflow transitions, licensing/updater, and cleanup contract lulus audit.
5. **Cross-module integration**: collision tests, authorization tests, activation/upgrade/deactivation/uninstall, error isolation, backup/restore, and rollback/recovery rehearsal.
6. **Retirement gate**: plugin lama baru dinonaktifkan/dipensiunkan setelah data parity, workflow parity yang disepakati, upgrade/restore, dan acceptance tests PASS.

Satu package berarti satu change-set/PR terkontrol, tetapi tahapan di atas tetap dijalankan berurutan dan masing-masing menghasilkan bukti. Jangan membuat satu migrasi besar yang sekaligus mengubah schema, credentials, licensing, dan runtime tanpa checkpoint.

## 9. Acceptance criteria dan artefak bukti

| ID | Kriteria | Bukti wajib |
|---|---|---|
| PKG-01 | Baseline terkunci | SHA tiga repository, branch/ref, daftar perubahan |
| PKG-02 | Target WordPress benar | URL/site label, path plugin, versi stack, status admin |
| PKG-03 | Backup dapat dipulihkan | log backup + hasil restore pada clone + row-count comparison |
| PKG-04 | Data WordPress terbaca | contoh post/page tersanitasi dan sumber/permission |
| PKG-05 | GSC siap bila scope dipilih | property verified, read-only request success, metadata hasil |
| PKG-06 | Schema mapping lengkap | setiap tabel/option memiliki owner, source, destination, policy, row counts |
| PKG-07 | Kredensial aman | tidak ada plaintext; kebijakan key compatibility/re-entry diuji |
| PKG-08 | Lisensi aman | state/secret/cache/endpoint/entitlement mapping dan negative tests |
| PKG-09 | Lifecycle terkoordinasi | aktivasi/upgrade/deaktivasi/uninstall, double hooks avoided, failure states visible |
| PKG-10 | Migrasi idempotent | dua kali run tidak menggandakan/menghapus data; marker hanya maju setelah sukses |
| PKG-11 | Modul terisolasi | kegagalan AI/Engine tidak membuat Core fatal error |
| PKG-12 | MVP cycle lulus | end-to-end WordPress runtime test dengan evidence |
| PKG-13 | Rollback/recovery terbukti | restore drill dan prosedur pemulihan teruji |
| PKG-14 | Tidak ada perubahan tak disetujui | diff sesuai scope; tidak ada production deployment atau silent data deletion |

Setiap kriteria memakai status **PASS / FAIL / BELUM DIUJI / TERHAMBAT**, bukti, commit, tanggal, dan penguji. Status tidak boleh dinaikkan berdasarkan rencana atau source inspection saja.

## 10. Persetujuan dan stop conditions

### Sudah disetujui secara konseptual
- Target satu plugin runtime AMANAH SEO dengan modul internal AI/Engine.
- Lifecycle Manager sebagai koordinator tunggal komponen internal.
- WordPress internal sebagai sumber struktur/konten dan GSC sebagai sumber performa pencarian setelah akses diverifikasi.
- Persetujuan manusia untuk perubahan website/publikasi; tidak ada aksi otonom MVP dasar.

### Belum disetujui oleh dokumen ini
- Coding atau pemindahan file source.
- Perubahan schema/database atau transformasi data.
- Perubahan format/penyimpanan credential, lisensi, entitlement, atau updater.
- Menonaktifkan atau menghapus plugin sumber.
- Deployment ke website produksi.
- Menyatakan runtime WordPress/GSC/CI PASS tanpa bukti.

### Stop conditions
Berhenti dan jangan meneruskan migrasi bila: target WordPress belum jelas; backup/restore gagal; row count/schema berbeda tanpa mapping; credential tidak bisa didekripsi atau kepemilikan tidak jelas; license entitlement tidak dapat dipetakan; migration marker ambigu; permission test gagal; source module menyebabkan fatal error; atau rollback belum terbukti.

## 11. Hasil paket dan status final

- **Audit source statis:** selesai untuk file yang dirujuk pada bagian 3–4; perlu diulang pada SHA terbaru sebelum coding.
- **Kesiapan WordPress runtime:** BELUM DIUJI sampai bukti dari target yang benar tersedia.
- **Akses data WordPress:** BELUM DIUJI.
- **Akses GSC:** BELUM DIUJI.
- **Backup/restore:** BELUM DIUJI.
- **Desain urutan migrasi dan risk register:** selesai sebagai paket perencanaan terkendali.
- **Migrasi kode/runtime:** belum dilakukan dan tidak diizinkan hanya oleh dokumen ini.

Paket ini menggabungkan seluruh tiga pekerjaan yang diminta—kesiapan WordPress, pembuktian WordPress/GSC, serta audit/migrasi AI/Engine—dalam satu rencana dan satu change-set dokumentasi. Langkah eksternal yang masih memerlukan akses lokal adalah pengumpulan bukti live; hal itu tidak dapat secara jujur dinyatakan selesai dari GitHub source audit saja.
