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
7. PR #34 yang sebelumnya menetapkan satu lisensi telah digabungkan ke `main` melalui commit `eab46a05f6e923de9b8511383e42ac89e6dd713d`; prinsip tersebut kini dikoreksi oleh paket dokumentasi ini dan tidak boleh dipakai sebagai dasar implementasi sampai koreksi lisensi per modul disetujui/masuk `main`. Perubahan dokumentasi bukan persetujuan coding.

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
| G3 Source inventory | Daftar entry point, autoload/bootstrap, hooks, routes, admin pages, capabilities, tests, config/build files | TERHAMBAT — inventory lengkap belum tersedia pada pemeriksaan ini | File allowlist belum bisa ditetapkan |
| G4 Lisensi per modul | Audit kontrak lisensi AI dan Engine secara independen: client/server API, product ID, aktivasi/deaktivasi, entitlement, expiry, renewal, offline/grace, secret storage, domain binding, update path, cache, dan error states | BELUM DIAUDIT ULANG pada SHA terkini | Kontrak lisensi salah satu modul tidak jelas atau belum kompatibel dengan pusat pengelolaan |
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

**Belum dapat ditetapkan secara jujur pada versi draft ini.** Inventory file aktual dan entry point repository utama belum berhasil diverifikasi melalui sumber yang tersedia dalam pemeriksaan ini. Menebak path entry point atau menulis allowlist berdasarkan asumsi akan melanggar kontrol perubahan.

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

**Status akhir saat ini: BELUM SIAP CODING.** PR dokumentasi telah digabung, tetapi G0, G1, G2, G3, G4, G5, G6, G7, dan G8 belum semuanya lulus. Langkah berikutnya adalah menyelesaikan inventory repository dan bukti target runtime, bukan mulai menulis kode.

## 8. Catatan kendala sumber

Path `docs/RENCANA_INDUK_PENUNTASAN_PEDOMAN_AMANAH_SEO.md` yang sebelumnya ditetapkan sebagai dokumen kerja tidak ditemukan pada ref `main` ketika diperiksa pada 2026-10-12 (GitHub mengembalikan 404). Jangan menganggap dokumen itu telah dibaca pada pemeriksaan ini. Pemilik/maintainer perlu memastikan path yang benar atau memulihkan dokumen sebelum mengandalkannya sebagai checklist status.

