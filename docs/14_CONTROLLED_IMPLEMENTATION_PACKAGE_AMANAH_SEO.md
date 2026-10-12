# Paket Implementasi Terkontrol: Core AMANAH SEO

**Versi:** 0.1-draft-controlled  
**Tanggal:** 2026-10-12  
**Status:** PERENCANAAN SAJA — BELUM DISETUJUI UNTUK CODING  
**Produk:** AMANAH SEO (produk utama)  
**Acuan:** [Pedoman Induk](PEDOMAN_INDUK_AMANAH_SEO.md), [Lingkup MVP](11_MVP_SCOPE_PROPOSAL.md), [Paket Tunggal Kesiapan dan Migrasi](13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md)

> Dokumen ini bukan izin mengubah kode, database, lisensi, konfigurasi, branch utama, runtime WordPress, atau melakukan deployment. Perubahan implementasi hanya boleh dimulai setelah seluruh gate wajib lulus, daftar file diizinkan secara eksplisit, dan pemilik menyetujui paket implementasi.

## 1. Keputusan produk yang mengikat

1. Target akhir adalah satu plugin WordPress yang dipasang pengguna: **AMANAH SEO**.
2. AMANAH SEO Core boleh diunduh, dipasang, dan diaktifkan tanpa lisensi. AMANAH SEO menjadi satu pusat pengelolaan dua lisensi modul independen: lisensi AMANAH SEO AI dan lisensi AMANAH SEO ENGINE V2. Status dan entitlement tiap modul diverifikasi terpisah; lisensi satu modul tidak membuka modul lainnya. Plugin AI/Engine tidak perlu dipasang sebagai plugin runtime terpisah.
3. Kemampuan AI dan Engine menjadi modul internal setelah audit dan migrasi terkontrol; fitur modul tidak otomatis termasuk MVP dasar.
4. AMANAH SEO Lifecycle Manager adalah koordinator tunggal lifecycle komponen internal. Jangan menjalankan lifecycle/installer lama secara paralel tanpa kontrak yang telah diuji.
5. Core harus tetap bisa aktif dan menampilkan diagnostik aman saat modul opsional gagal. Kegagalan modul tidak boleh menyebabkan fatal error pada core.
6. Tidak ada perubahan produksi, tindakan otonom terhadap website, penghapusan data, atau klaim PASS tanpa bukti.
7. Keputusan pemilik bahwa Core AMANAH SEO dapat dipasang tanpa lisensi dan AI/Engine memiliki lisensi modul independen telah didokumentasikan dan digabungkan ke `main` melalui PR #36, commit `fdbdeb8c1caf8691140237ae66b607db0739f383`. Ini menyetujui keputusan arsitektur lisensi, bukan persetujuan coding.

## 2. Sasaran paket implementasi pertama

Paket implementasi pertama dibatasi pada **Core Shell AMANAH SEO**, bukan migrasi penuh AI/Engine.

Hasil yang direncanakan setelah gate dan persetujuan:
- satu entry point plugin induk yang dapat dikenali WordPress;
- bootstrap minimal dan pemeriksaan dependensi;
- Lifecycle Manager tunggal dengan activation/deactivation yang aman dan tanpa menjalankan installer legacy;
- pemeriksaan capability/authorization dan nonce untuk tindakan admin;
- halaman health/diagnostics yang tidak membocorkan rahasia;
- penanganan kegagalan modul yang fail-safe;
- pengujian otomatis dan prosedur recovery.

**Tidak termasuk dalam paket pertama:** menyalin seluruh modul AI/Engine; menjalankan migrasi schema dari kedua plugin; menggabungkan tabel; mengubah atau memindahkan lisensi/secret store; memindahkan API keys; mengubah provider AI; mengubah workflow Engine; menghapus plugin lama; deployment produksi; perubahan GSC; perubahan data website. Semua itu memerlukan paket/gerbang lanjutan.

## 3. Audit gerbang teknis

Status di bawah ini adalah status bukti, bukan perkiraan.

| Gate | Pemeriksaan wajib | Status saat dokumen dibuat | Stop condition |
|---|---|---|---|
| G0 Baseline | SHA terkini, branch, working tree, inventory file, perbedaan sejak audit | SEBAGIAN — SHA baseline historis tersedia; perlu dikunci ulang | SHA/tree belum diverifikasi |
| G1 Target runtime | Identitas target WordPress AMANAH SEO, path plugin, versi WP/PHP/DB, plugin aktif | BELUM DIUJI | Target atau path ambigu |
| G2 Backup/recovery | Backup files + DB, restore ke clone, row count pembanding | BELUM DIUJI | Restore belum terbukti |
| G3 Source inventory | Daftar entry point, autoload/bootstrap, hooks, routes, admin pages, capabilities, tests, config/build files | PARSIAL — tree GitHub dan entry point utama kedua repo sumber telah diverifikasi; inventory lengkap dan allowlist belum dikunci | Belum seluruh data ownership, hooks/routes, tests, build/config, dan dependency audit ditutup |
| G4 Lisensi per modul | Audit kontrak lisensi AI dan Engine secara independen: client/server API, product ID, aktivasi/deaktivasi, entitlement, expiry, renewal, offline/grace, secret storage, domain binding, update path, cache, dan error states | PARSIAL — entry point dan bagian lisensi Engine dibaca pada SHA terkini; kontrak AI/Engine belum lengkap | Perbedaan kontrak dan risiko secret storage belum diselesaikan |
| G5 Data ownership | Inventaris tabel/options/transients/cron dan pemilik data; kebijakan preserve/retire | BELUM DIUJI untuk target runtime | Ada risiko penghapusan/perpindahan data |
| G6 Keamanan | Capability, nonce/CSRF, sanitization, escaping, secret redaction, least privilege | BELUM DIUJI | Ada bypass akses atau rahasia tercetak |
| G7 Test readiness | Lint/unit/integration/runtime commands, baseline hasil sebelum perubahan | BELUM DIUJI | Tes tidak reproducible atau baseline gagal tanpa diagnosis |
| G8 Approval | Scope, file allowlist, acceptance tests, recovery, reviewer/owner approval | BELUM DISETUJUI | Dilarang mulai coding |

### Temuan yang tidak boleh dilupakan dari audit paket tunggal

- Schema AI memiliki perbedaan antara dokumentasi lama dan kode aktif; installer AI pernah memiliki jalur yang dapat menghapus tabel legacy ketika tabel arsip sudah ada.
- Credential AI terkait dengan derivasi kunci WordPress salt; ciphertext tidak boleh dipindahkan dengan asumsi bisa didekripsi.
- Engine memiliki perbedaan antara `Schema::VERSION` dan versi MigrationManager, serta penomoran migrasi yang perlu diverifikasi pada SHA terkini.
- Jalur uninstall Engine berpotensi menyentuh option credential AI; ownership lintas modul harus diperjelas.
- LicenseSecretStore Engine mempunyai fallback kunci literal yang tidak boleh disalin sebagai pola aman ke produk induk.
- Backup/restore, akses data WordPress, GSC, dan runtime belum boleh dianggap PASS tanpa bukti aktual.

Temuan historis di atas harus diperiksa ulang terhadap SHA terkini sebelum paket apa pun mengubah source atau data.

## 4. Kunci ruang lingkup dan file

### 4.1 Allowlist yang dikunci sekarang

Hanya dokumen perencanaan pada branch terkontrol ini yang diizinkan berubah selama tahap persiapan. `main`, kode plugin, tes produk, konfigurasi, database, credential, serta runtime tidak berubah melalui paket ini.

### 4.2 File implementasi

**Allowlist implementasi masih belum dapat dikunci.** Pemeriksaan GitHub-only menemukan repository AMANAH SEO utama hanya berisi dokumentasi dan template; source runtime berada di repository AI dan Engine. Entry point awal telah teridentifikasi, tetapi daftar file lengkap, dependensi, side effect, tests, data ownership, dan kontrak lisensi belum selesai diaudit. Jangan menebak atau mengizinkan coding sebelum manifest exact ADD/MODIFY/DELETE disetujui.

Sebelum coding, buat manifest file eksplisit berisi:
- path relatif persis;
- tindakan (ADD/MODIFY/DELETE; DELETE default dilarang);
- alasan dan tanggung jawab;
- dependency/pemanggil;
- tes yang melindunginya;
- data/side effect yang mungkin disentuh;
- prosedur rollback file.

File di luar manifest otomatis **OUT OF SCOPE**. Perluasan manifest memerlukan persetujuan pemilik sebelum perubahan.

### 4.3 Perubahan yang dilarang tanpa persetujuan terpisah

- file pada repository AMANAH SEO AI atau AMANAH SEO ENGINE V2;
- API licensing server, skema database, credential store, endpoint lisensi atau entitlement;
- schema/data migration, uninstall cleanup, atau penghapusan data;
- production files, runtime WordPress, konfigurasi, branch `main`, secrets, dan database;
- dependensi/framework baru, build/release pipeline, atau perubahan scope MVP.

## 5. Kriteria penerimaan paket Core Shell

Setiap kriteria harus memiliki status PASS/FAIL/BELUM DIUJI/TERHAMBAT, bukti, SHA commit, tanggal, dan penguji.

| ID | Kriteria | Bukti wajib |
|---|---|---|
| CORE-01 | Plugin induk dikenali WordPress pada target testing yang benar | Screenshot/hasil admin tersanitasi dan path terverifikasi |
| CORE-02 | Aktivasi tidak menjalankan installer AI/Engine legacy | Tes hook/spy atau bukti kode + runtime log |
| CORE-03 | Aktivasi dan deaktivasi idempotent | Jalankan berulang tanpa duplikasi hook/data/cron |
| CORE-04 | Tidak ada migrasi atau penghapusan data tak disetujui | DB diff/row-count dan pemeriksaan side effect |
| CORE-05 | Admin page dilindungi capability dan nonce | Tes admin berwenang, pengguna tanpa hak, nonce hilang/tidak valid |
| CORE-06 | Diagnostik tidak memuat secret | Fixture secret palsu + pemeriksaan output/log |
| CORE-07 | Modul gagal tidak menjatuhkan core | Simulasikan modul hilang, exception, dan dependency gagal |
| CORE-08 | Kompatibilitas runtime yang ditargetkan terbukti | Versi PHP/WP/DB tercatat dan lint/test lulus |
| CORE-09 | Backup/restore berhasil | Bukti restore clone dan pembanding dasar |
| CORE-10 | Tidak ada file di luar allowlist | Diff final hanya berisi file yang disetujui |
| CORE-11 | Lisensi AI dan Engine independen | Tes membuktikan lisensi valid untuk satu modul tidak membuka modul lain; pusat pengelolaan tetap satu di AMANAH SEO |
| CORE-12 | Recovery terdokumentasi dan dapat dijalankan | Simulasi gagal aktivasi/upgrade dan recovery pada clone |

Tes statis atau pembacaan source saja tidak dapat menggantikan bukti runtime yang diwajibkan oleh masing-masing kriteria.

## 6. Rencana pengujian dan recovery

### Sebelum perubahan
1. Catat SHA dan branch semua repository yang relevan.
2. Simpan daftar file, status working tree, versi runtime, plugin aktif, serta baseline tes.
3. Buat backup file dan database dengan akses terbatas; jangan unggah backup/secret ke chat atau repository.
4. Restore backup ke clone terpisah dan verifikasi akses serta row-count pembanding.
5. Jalankan baseline lint/unit/integration tests dan simpan hasil yang telah disanitasi.

### Sesudah setiap change-set
1. Tinjau diff terhadap allowlist dan hentikan bila ada file tambahan.
2. Jalankan lint dan tes terarah, kemudian seluruh regresi yang disepakati.
3. Jalankan activation/deactivation/re-activation di clone.
4. Periksa database, options, cron, hooks, capabilities, error logs, dan output diagnostik.
5. Jalankan skenario gagal yang sengaja dipicu dan buktikan core tetap aman.
6. Simpan bukti beserta commit SHA; jangan menaikkan status tanpa hasil aktual.

### Pemulihan
- Jika tes statis gagal: hentikan, jangan deploy.
- Jika aktivasi membuat fatal error: gunakan akses filesystem/backup untuk menonaktifkan hanya pada clone, simpan log tersanitasi, lalu pulihkan snapshot sebelum percobaan berikutnya.
- Jika database berubah tidak sesuai: hentikan semua migrasi; pulihkan backup ke clone dan bandingkan row count/schema sebelum mengulang.
- Jangan melakukan rollback database produksi atau menghapus plugin sumber otomatis.
- Jangan lanjut ke migrasi AI/Engine sampai recovery drill dan data mapping disetujui secara terpisah.

## 6A. Bukti inventaris sumber GitHub-only (2026-10-12)

- AMANAH-SEO main commit: 6a9dba3c8d7f424521625955a6ee0c53609a50e7. Repository berisi README, dokumen, audit, dan template; belum berisi plugin runtime Core.
- AMANAH-SEO-AI main commit: cd83b80fc7030651cee020391081279a9a36e27f. Entry point: plugin/amanah-seo-ai.php; lifecycle/install: plugin/includes/class-activator.php dan plugin/includes/class-install.php.
- AMANAH-SEO-ENGINE-V2 main commit: 80e7c38a327baeef1d7ad7b13d006d453d5838b1. Entry point: plugin/amanah-seo-engine.php; bootstrap: plugin/Core/Bootstrap/Plugin.php; lisensi: plugin/Platform/License/.
- Risiko data AI: plugin/includes/class-install.php, fungsi archive_legacy_content_analysis_table(), baris 155–159 pada file SHA bb f923d846a434c047c326fb9aab6186cfd84970 (tanpa spasi: bbf923d846a434c047c326fb9aab6186cfd84970), menjalankan DROP TABLE pada tabel legacy jika tabel arsip sudah ada. Jangan jalankan installer/migrasi sebelum backup dan kebijakan preserve data dibuktikan.
- Risiko secret Engine: plugin/Platform/License/LicenseSecretStore.php, SHA 8982f7a53781ff184a2bcd5f7109c7c2696dd3c7, memakai fallback literal amanah-seo-engine jika AUTH_KEY tidak tersedia. Jangan menyalin secret store ini ke Core tanpa perbaikan desain dan audit.
- Lisensi Engine: Plugin.php membuat LicenseClient dengan product ID Amanah SEO Engine V2; kontrak AI yang setara belum dibuktikan pada inventory awal. Dua lisensi independen adalah keputusan arsitektur, bukan bukti implementasi teknis siap.
- Batas bukti: temuan ini berasal dari GitHub source saja. Target WordPress, database aktif, backup/restore, tes, dan konfigurasi runtime tetap NOT TESTED.
## 7. Gerbang persetujuan sebelum coding

Coding hanya boleh dimulai setelah semua butir ini terpenuhi:

- [ ] Koreksi arsitektur lisensi per modul sudah masuk ke `main` dan referensi satu-lisensi lama tidak lagi menjadi keputusan aktif.
- [ ] SHA terkini repository utama dan baseline terkait sudah dikunci.
- [ ] Inventory source dan entry point telah diverifikasi.
- [ ] Target WordPress testing dan path plugin disetujui pemilik.
- [ ] Backup dan restore clone terbukti.
- [ ] File allowlist ADD/MODIFY telah ditulis persis.
- [ ] Acceptance tests dan recovery drill disepakati.
- [ ] Risiko lisensi dan data yang menyentuh paket pertama dipastikan tidak berubah tanpa izin.
- [ ] Pemilik memberi persetujuan eksplisit untuk paket implementasi dengan scope tersebut.

**Status akhir saat ini: BELUM SIAP CODING.** Keputusan arsitektur lisensi telah disetujui dan masuk main, tetapi G0 masih perlu dikunci untuk implementasi, G1/G2/G5/G6/G7 belum diuji, G3/G4 baru parsial, dan G8 belum disetujui. Langkah berikutnya adalah menuntaskan source/data/license inventory secara baca-saja dan mendapatkan bukti target runtime/backup-restore; jangan mulai menulis kode.

## 8. Catatan kendala sumber

Path aktif `docs/RENCANA_INDUK_PENUNTASAN_PEDOMAN_AMANAH_SEO.md` tidak ditemukan pada `main`; salinan historis tersedia di `docs/archive/RENCANA_INDUK_PENUNTASAN_PEDOMAN_AMANAH_SEO.md` dan secara eksplisit berstatus superseded. Karena itu, gunakan `docs/PEDOMAN_INDUK_AMANAH_SEO.md` sebagai acuan konseptual aktif dan paket implementasi ini untuk gerbang implementasi. Jangan memperlakukan dokumen arsip sebagai checklist operasional terkini tanpa persetujuan eksplisit.



## 6B. Audit teknis lanjutan: lifecycle, penyimpanan lisensi, dan batas migrasi (2026-10-12)

**Jenis bukti:** inspeksi source GitHub pada branch `main`; tidak menjalankan installer, tidak menyentuh database/runtime, dan tidak menguji server lisensi.

### AI — lifecycle dan data
- `plugin/amanah-seo-ai.php` (SHA `73c15f7cbd0f0dc511bd5670bae69dd4824ab68e`) mendaftarkan activation hook ke `AMANAH_SEO_AI_Activator::activate()`, deactivation hook ke `AMANAH_SEO_AI_Deactivator::deactivate()`, dan hook `plugins_loaded` ke `AMANAH_SEO_AI_Install::maybe_upgrade()`.
- `plugin/includes/class-activator.php` (SHA `9f26829e6ddc06d59fcbbd94f2de8b31b71bdd6f`) memanggil `AMANAH_SEO_AI_Install::install()` saat aktivasi dan menambahkan capability pada role administrator/editor. Karena aktivasi Core target tidak boleh menjalankan installer legacy, entry point lama tidak boleh disertakan/dipanggil secara langsung oleh lifecycle Core.
- `plugin/includes/class-deactivator.php` (SHA `ea11888850545d7204b296a955376d5fff39f20b`) saat ini tidak membersihkan data persisten. Ini bukan bukti bahwa semua jalur upgrade/uninstall aman.
- `plugin/includes/class-install.php` (SHA `bbf923d846a434c047c326fb9aab6186cfd84970`) dapat mengganti nama tabel legacy menjadi tabel arsip bila arsip belum ada, tetapi menghapus tabel legacy jika arsip sudah ada. Kebijakan aman sementara: preserve-only; tidak menjalankan jalur tersebut pada target migrasi, tidak menghapus tabel, dan tidak menggabungkan schema sebelum inventaris row/schema serta prosedur restore dibuktikan.

### ENGINE — lisensi dan lifecycle
- `plugin/amanah-seo-engine.php` (SHA `026d6183a12724a6d8d6ef7b1d39a2ede8560379`) mendaftarkan activation/deactivation hook pada `Plugin::activate()` dan `Plugin::deactivate()`; ini menguatkan bahwa lifecycle lama harus diorkestrasi, bukan didaftarkan ulang tanpa isolasi dalam Core.
- `plugin/Core/Bootstrap/Plugin.php` (SHA `22f31ee16af7e568ab37b2366c06409c08ff704f`) adalah bootstrap besar yang meregistrasi banyak layanan dan migrasi; jangan memasukkannya ke Core Shell secara menyeluruh sebelum dependency, hook, cron, route, migration, dan side effect dipetakan.
- `plugin/Platform/License/LicenseClient.php` (SHA `880c056a3aacec3cbc58e2aa71e4e2c6f2a1f9ee`) mengirim `license_key` dan `product` melalui HTTPS ke `/activate` dan `/validate`, menyimpan secret setelah aktivasi berhasil, dan menggunakan cache ketika validasi gagal. Ketika belum ada secret atau endpoint, status menjadi `unconfigured`; ketika request validasi gagal tanpa cache, status menjadi `unknown`.
- `plugin/Platform/License/LicenseCache.php` (SHA `d3e7340581f7d9b0ef873b3aaf535573024ac7df`) menyimpan status lisensi pada option `amanah_seo_license_state`; `LicenseSecretStore.php` (SHA `8982f7a53781ff184a2bcd5f7109c7c2696dd3c7`) menyimpan secret pada option `amanah_seo_license_secret` dengan AES-256-GCM, tetapi derivasi kunci menggunakan literal fallback `amanah-seo-engine` saat `AUTH_KEY` tidak tersedia. Fallback ini tidak dapat diterima sebagai pola final untuk Core.
- `EntitlementResolver.php` (SHA `cfd04728c116bd420e3ec3c95188db606c8a7620`) mengizinkan entitlement pada status aktif; grace khusus `premium_updates` dapat berlaku untuk state cache sesuai policy terpisah. Perilaku grace ini belum boleh digeneralisasi ke entitlement fitur AI/Engine.
- Kontrak server, product ID final, aktivasi ulang/deaktivasi, domain binding, revocation, expiry, renewal, response signature/validation, grace/offline untuk fitur, dan kesetaraan implementasi lisensi AI **belum terbukti** dari bukti yang dibaca. G4 tetap parsial dan menjadi blocker sebelum modul premium diintegrasikan.

### Keputusan teknis sementara yang diperlukan agar aman
1. Core Shell pertama hanya menyediakan slot/kontrak modul dan diagnostik; tidak memuat atau menjalankan installer, migrator, activation hooks, uninstall hooks, cron registration, atau bootstrap legacy AI/Engine.
2. Jangan memindahkan opsi lisensi atau credential antarmodul pada paket pertama. Jangan mengubah nama option yang sudah ada dan jangan menghapus/menggabungkan tabel.
3. Pusat lisensi di UI AMANAH SEO boleh menjadi tujuan arsitektur, tetapi setiap modul tetap memakai product identity dan entitlement terpisah. Implementasi UI belum boleh mengklaim lisensi valid tanpa kontrak API dan uji server yang terverifikasi.
4. Sebelum paket Core Shell coding, masih harus ditutup: SHA/tree baseline terkini; source inventory dan exact file allowlist; target WordPress testing yang disepakati; bukti backup + restore clone; test baseline; kontrak licensing yang tersedia; serta acceptance/recovery tests.
5. Repo AMANAH SEO utama saat ini belum memiliki source plugin runtime. Karena itu belum ada target plugin induk yang aman untuk disinkronkan ke WordPress. `git pull` dan `robocopy` baru layak diberikan setelah implementasi di branch terkontrol, tes sesuai gate, PR ditinjau/di-merge, dan path runtime/destination dikonfirmasi.

### Status setelah audit lanjutan
- G3 Source inventory: **PARSIAL** — entry point/lifecycle dan sebagian risiko sudah diverifikasi; inventory lengkap dependency/hook/route/data ownership dan exact allowlist belum selesai.
- G4 Licensing: **PARSIAL / BLOCKED** — sebagian alur Engine terbaca, namun server contract dan AI equivalent belum terbukti.
- G5 Data ownership: **BLOCKED** — ada destructive legacy-table branch dan belum ada backup/restore evidence.
- G1/G2/G6/G7: **BELUM DIUJI** pada target runtime/clone.
- G8: **BELUM DISETUJUI** untuk scope implementasi Core Shell dengan allowlist final.

Tidak ada kode produk, schema, credential, server lisensi, database, konfigurasi, atau WordPress runtime yang diubah oleh audit ini.
