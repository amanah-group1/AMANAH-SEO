# Paket Implementasi Terkontrol: Core AMANAH SEO

**Versi:** 0.1-draft-controlled  
**Tanggal:** 2026-10-12  
**Status:** IMPLEMENTASI FONDASI CORE MVP — PHP LINT PASS; RUNTIME BELUM DIUJI  
**Produk:** AMANAH SEO (produk utama)  
**Acuan:** [Pedoman Induk](PEDOMAN_INDUK_AMANAH_SEO.md), [Lingkup MVP](11_MVP_SCOPE_PROPOSAL.md), [Paket Tunggal Kesiapan dan Migrasi](13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md)

 > Pemilik memberikan instruksi eksplisit pada 2026-10-12 untuk menuntaskan target teknis AMANAH SEO. Instruksi ini mengizinkan implementasi terkontrol bertahap; bukan izin untuk mengubah database/runtime WordPress tanpa backup, atau menyatakan fitur belum teruji sebagai PASS.

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
## 7. Gerbang sebelum migrasi modul dan deployment

Instruksi pemilik pada 2026-10-12 mengizinkan implementasi fondasi Core MVP pada branch terkontrol. Persetujuan itu tidak mengizinkan migrasi kode AI/ENGINE atau perubahan runtime/database tanpa bukti dan pemeriksaan tersendiri.

- [x] Arsitektur lisensi dua modul independen telah disetujui dan tercatat pada `main`.
- [x] Fondasi plugin Core dibuat di branch terkontrol; PHP syntax check dijalankan melalui GitHub Actions.
- [ ] Runtime WordPress testing dan path tujuan final diverifikasi.
- [ ] Backup dan restore clone dibuktikan.
- [ ] Acceptance test WordPress, otorisasi/nonce, database, serta recovery drill lulus.
- [ ] Kontrak API lisensi server dan perilaku lisensi AI/ENGINE dibuktikan.
- [ ] Source inventory lengkap, dependency map, dan allowlist migrasi modul ditinjau.
- [ ] GSC property/access dan perilaku kegagalan diuji sebelum integrasi GSC dinyatakan berfungsi.
- [ ] Persetujuan terpisah untuk migrasi modul dan deployment diberikan setelah hasil di atas tersedia.

**Status setelah paket fondasi Core MVP (2026-10-12):** plugin entry point, tabel milik Core, dashboard, temuan, tugas, persetujuan, bukti penyelesaian, dan riwayat telah ditambahkan pada PR #40. GitHub Actions PHP syntax check PASS. Ini belum memenuhi penerimaan MVP: runtime WordPress, backup/restore, pengujian nonce/capability, GSC, kontrak lisensi server, integrasi internal AI/ENGINE, dan pemeriksaan dependency/migrasi masih terbuka. Tidak ada runtime/database yang disentuh.

## 8. Catatan kendala sumber

Path aktif `docs/RENCANA_INDUK_PENUNTASAN_PEDOMAN_AMANAH_SEO.md` tidak ditemukan pada `main`; salinan historis tersedia di `docs/archive/RENCANA_INDUK_PENUNTASAN_PEDOMAN_AMANAH_SEO.md` dan secara eksplisit berstatus superseded. Karena itu, gunakan `docs/PEDOMAN_INDUK_AMANAH_SEO.md` sebagai acuan konseptual aktif dan paket implementasi ini untuk gerbang implementasi. Jangan memperlakukan dokumen arsip sebagai checklist operasional terkini tanpa persetujuan eksplisit.

