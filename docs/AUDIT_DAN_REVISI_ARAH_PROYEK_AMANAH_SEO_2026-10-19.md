# Audit Ulang dan Rencana Revisi Arah Proyek AMANAH SEO

**Tanggal audit:** 2026-10-19  
**Repository utama:** `amanahgroup123/AMANAH-SEO`  
**Branch audit:** `controlled/project-reorientation-audit-20261019`  
**Status:** AUDIT AWAL BERBASIS BUKTI GITHUB — BELUM MERUPAKAN PERSETUJUAN IMPLEMENTASI  
**Batas perubahan:** Dokumen audit baru saja; tidak mengubah kode produk, branch sumber, database, runtime, konfigurasi, atau deployment.

## 1. Tujuan

Memulihkan satu arah kerja yang dapat diikuti untuk AMANAH SEO dengan memisahkan:
1. keputusan produk yang sudah disetujui;
2. keadaan source yang benar-benar ada;
3. bukti pengujian otomatis;
4. bukti WordPress/runtime/staging/produksi;
5. pekerjaan yang masih terbuka;
6. konflik dokumentasi atau PR yang perlu direkonsiliasi;
7. keputusan yang memang membutuhkan persetujuan pemilik.

Dokumen ini merupakan hasil audit awal dari sumber GitHub yang dapat diperiksa pada 2026-10-19. Ini bukan klaim bahwa seluruh file dan seluruh baris kode ketiga repository telah diaudit. Bagian yang belum diperiksa ditandai sebagai belum terverifikasi.

## 2. Sumber dan batas verifikasi

### Sumber yang dibaca atau diperiksa

- `README.md` pada `main` di AMANAH-SEO; blob SHA `0f5493eb80cfc2cad137ff6ea338983d9ca52dad`.
- `docs/PEDOMAN_INDUK_AMANAH_SEO.md`; blob SHA `7f836bcf3c21b692a845d79cc06b855fc0a1cfe0`.
- `docs/11_MVP_SCOPE_PROPOSAL.md`; blob SHA `0fbabd6630812988998b89202b4c3250e6034987`.
- `docs/13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md`; blob SHA `ea3ea7f6a47bb4d4e1424ab178a6e1034c90007b`.
- `docs/15_STRUKTUR_MENU_FINAL_AMANAH_SEO.md`; blob SHA `8c660bf1577eafec984893af2bd260d619a7d5ad`.
- Daftar branch pada AMANAH-SEO.
- Daftar PR terbuka/terkini dan metadata PR #58, #60, #61, #62, #66, #68, #71, #72.
- README pada AMANAH-SEO-AI; blob SHA `5ce9b462731b8b71ad335bc46aac35f6b565b7a6`.

### Keterbatasan saat audit awal

- File `docs/RENCANA_INDUK_PENUNTASAN_PEDOMAN_AMANAH_SEO.md` tidak ditemukan pada `main` saat diminta melalui GitHub Contents API. README justru merujuk ke `docs/PEDOMAN_INDUK_AMANAH_SEO.md`. Tidak boleh diklaim bahwa file yang tidak ditemukan telah dibaca.
- Pengambilan README dari repository `amanahgroup123/amanah-seo-engine-v2` menghasilkan 404. Repository terlihat dalam pencarian repository, tetapi keberadaan/path README belum terverifikasi dari pemanggilan tersebut.
- Audit ini belum membaca setiap file implementasi pada seluruh branch, belum mengunduh dan membandingkan seluruh tree, serta tidak menguji WordPress, database, GSC, server lisensi, atau website produksi secara langsung.
- Status CI dan runtime yang ditulis dalam PR adalah bukti yang dicantumkan pada PR; bukan hasil pengujian baru yang dijalankan dalam audit ini.
- Ada ketidakkonsistenan metadata PR #72 pada salah satu respons: status utama dan pemeriksaan berikutnya menyatakan OPEN / belum merge, sementara satu respons menyertakan `merge_commit_sha` yang terisi. Status harus dipastikan dari GitHub sebelum tindakan merge apa pun; dokumen ini memperlakukan PR #72 sebagai OPEN berdasarkan state/merged fields yang berulang kali menyatakan belum merge.

## 3. Arah produk yang sudah tercatat sebagai keputusan

Hal berikut tercantum pada Pedoman Induk dan Lingkup MVP dan tidak perlu diputuskan ulang kecuali pemilik secara eksplisit mengubahnya:

1. AMANAH SEO adalah sistem utama untuk mengelola kondisi website, temuan, prioritas, tugas, persetujuan, hasil, dan langkah berikutnya.
2. Target akhir adalah satu plugin WordPress yang dipasang pengguna: AMANAH SEO.
3. AMANAH SEO dapat diunduh, dipasang, dan diaktifkan tanpa lisensi.
4. AMANAH SEO AI dan AMANAH SEO ENGINE V2 ditargetkan menjadi modul internal dalam plugin induk setelah audit dan migrasi terkontrol; repository sumber tetap baseline sampai migrasi disetujui.
5. Lisensi AI dan ENGINE independen. Lisensi salah satu modul tidak membuka modul lainnya.
6. Pengelolaan lisensi/entitlement modul dipusatkan melalui AMANAH SEO.
7. Core mengelola website, temuan, prioritas, tugas, persetujuan, hasil, dan lifecycle koordinasi; Core tidak boleh menduplikasi mesin AI atau workflow konten khusus Engine.
8. MVP adalah satu siklus: website → kondisi/temuan → prioritas beralasan → tugas → pemeriksaan/keputusan pemilik → hasil → langkah berikutnya.
9. WordPress internal adalah sumber awal untuk struktur/konten; Google Search Console adalah sumber awal metrik pencarian. Pemilihan konseptual tidak membuktikan koneksi telah tersedia.
10. Perubahan website/publikasi yang berdampak memerlukan persetujuan eksplisit.
11. Klaim fitur, keamanan, dan pengujian harus mengikuti bukti. Tidak boleh mengarang metrik atau menjanjikan ranking.
12. Keputusan konsep dan packaging tidak otomatis memberikan izin untuk coding, migrasi, merge, atau deployment.

Sumber utama:
- https://github.com/amanahgroup123/AMANAH-SEO/blob/main/docs/PEDOMAN_INDUK_AMANAH_SEO.md
- https://github.com/amanahgroup123/AMANAH-SEO/blob/main/docs/11_MVP_SCOPE_PROPOSAL.md

## 4. Temuan awal berdasarkan bukti

### FIND-001 — Dokumen rencana induk yang disebut sebelumnya tidak ditemukan pada jalur yang diminta

**Bukti:** Fetch `docs/RENCANA_INDUK_PENUNTASAN_PEDOMAN_AMANAH_SEO.md` pada `main` mengembalikan 404; README mengarahkan ke `docs/PEDOMAN_INDUK_AMANAH_SEO.md`.

**Dampak:** Risiko bekerja dari nama dokumen lama atau salah menganggap dokumen sebagai sumber aktif.

**Tindakan:** Gunakan Pedoman Induk yang benar-benar ada sebagai acuan sementara; cari riwayat/branch untuk mengetahui apakah dokumen rencana lama dipindahkan atau memang tidak ada. Jangan membuat klaim tentang isi dokumen yang tidak ditemukan.

**Status:** TERKONFIRMASI untuk ketidakcocokan path; asal-usul file lama BELUM DIUJI.

### FIND-002 — README menyatakan batas fitur dan bukti runtime secara eksplisit, tetapi belum semua gerbang lulus

**Bukti:** README menyatakan Core MVP memiliki dashboard/workspace, temuan, tugas, persetujuan, riwayat, diagnostik, dan self-test. README juga menyatakan uji runtime 11/11 yang pernah dilaporkan berasal dari versi sebelum PR #48; runtime versi setelah hardening perlu diulang. Backup/restore, GSC, integrasi server lisensi, dan migrasi AI/ENGINE tetap belum teruji/belum tersedia.

**Dampak:** Risiko menganggap Core MVP sebagai produk penuh atau menganggap CI sama dengan runtime produksi.

**Tindakan:** Buat matriks status per fitur dengan kolom: implementasi source, CI, WordPress testing, staging, produksi, bukti, SHA, tanggal, dan pemilik pengujian.

**Status:** TERKONFIRMASI sebagai pernyataan README; hasil runtime terkini BELUM DIUJI ulang dalam audit ini.

### FIND-003 — Dokumentasi menu perlu diselaraskan dengan aturan akses lisensi yang berlaku

**Bukti:** `docs/15_STRUKTUR_MENU_FINAL_AMANAH_SEO.md` menyatakan semua menu modul tetap terlihat dan dapat dibuka, dan halaman modul tanpa entitlement menampilkan sales page. Di sisi lain, perubahan navigasi pada PR #72/#71 dan beberapa PR lain memiliki baseline/tujuan yang berbeda. Keputusan akses yang perlu menjadi acuan operasional adalah: menu induk AMANAH SEO membuka Sales Page; Settings & Access tetap dapat dibuka; CORE hanya membuka fitur jika syarat lisensi modul yang berlaku terpenuhi, jika tidak kembali ke Sales Page; AI memerlukan lisensi AI; ENGINE memerlukan lisensi ENGINE; lisensi satu modul tidak membuka modul lain. Aturan ini perlu dinyatakan konsisten dalam dokumentasi dan diuji terhadap source aktif.

**Dampak:** Pengguna bisa melihat perilaku berbeda antara dokumentasi, sidebar, halaman langsung, dan entitlement server-side.

**Tindakan:** Revisi dokumen navigasi menjadi satu matriks eksplisit yang memisahkan:
- visibilitas menu;
- halaman tujuan saat diklik;
- akses direct URL;
- pemeriksaan capability WordPress;
- entitlement modul;
- akses endpoint/aksi;
- tampilan sales page saat lisensi tidak memenuhi syarat.

**Status:** KONFLIK DOKUMENTASI/BASELINE TERIDENTIFIKASI; source runtime aktual dan perilaku live BELUM DIUJI.

### FIND-004 — Ada banyak PR terbuka yang berpotensi saling tumpang tindih atau berbasis commit berbeda

**Bukti daftar PR yang diperiksa:**

- PR #72 — Sales Page; base `main`, head `078d9738b0a2a6220a53cbb2941d84ee69bc4846`; satu file `plugin/includes/class-amanah-seo-admin.php`; masih OPEN menurut state yang diperiksa.
- PR #71 — menghapus registrasi menu Article Generation mandiri dari AI/Core; masih OPEN menurut state yang diperiksa.
- PR #68 — rute fitur tersembunyi dan kartu dashboard; base SHA `fe928d9b668d78e355caea999533787a81a5c13c`; runtime staging belum dinyatakan lulus dalam deskripsi PR.
- PR #66 — sidebar empat item; berbasis PR #62, bukan langsung `main`; CI/runtime belum dikonfirmasi dalam deskripsi PR.
- PR #62 — navigasi induk Sales Page; berbasis PR #61.
- PR #61 — navigasi sederhana; draft, berbasis branch `feature/final-menu-order-20261014`.
- PR #60 — navigasi accordion Core/AI/ENGINE; draft dan berbasis `integration/core-ai-engine-20261012`, bukan `main`; deskripsi mencantumkan CI/lint dan ZIP PASS, tetapi runtime WordPress visual/interaksi BLOCKED.
- PR #58 — integrasi Core/AI dan internalisasi Engine; draft, 163 commits; deskripsi mencantumkan CI dan WordPress integration berbasis mock PASS, tetapi live activation dengan lisensi nyata dan rehearsal upgrade pada salinan database staging tetap menjadi gerbang.

**Dampak:** Memilih PR hanya dari nomor terbesar atau nama branch dapat menggabungkan perubahan dari garis keturunan berbeda, menghidupkan kembali keputusan lama, atau melewatkan dependensi.

**Tindakan:** Buat matriks PR berisi base/head SHA, file yang berubah, tujuan, status CI, status runtime, konflik dengan PR lain, dependensi, keputusan pemilik, dan rekomendasi KEEP / REVISE / CLOSE / DEFER. Jangan merge hanya berdasarkan urutan nomor PR.

**Status:** Daftar PR dan metadata utama terverifikasi; diff penuh semua PR BELUM DIAUDIT.

### FIND-005 — Integrasi Core + AI + ENGINE mempunyai bukti otomatis, tetapi masih ada gerbang eksternal

**Bukti:** Deskripsi PR #58 mencantumkan hasil regression, PHP syntax, dan WordPress runtime integration dengan HTTP licensing mock. Deskripsi yang sama mensyaratkan aktivasi lisensi AI nyata pada staging, konfirmasi identifier entitlement, serta rehearsal upgrade menggunakan salinan database aktif dan verifikasi status API lisensi ENGINE.

**Dampak:** Mock sukses tidak membuktikan server eksternal, key pelanggan, konfigurasi entitlement, atau migrasi data produksi sudah benar.

**Tindakan:** Pertahankan PR #58 sebagai draft sampai setiap gerbang eksternal disertai bukti nyata. Jangan menganggap deskripsi PR sebagai hasil audit live oleh sesi ini.

**Status:** Gerbang yang tertulis dalam PR terkonfirmasi; status terbaru live service BELUM DIUJI dalam audit ini.

### FIND-006 — Status repository AI/ENGINE harus menjadi bukti pendukung, bukan pengganti pedoman induk

**Bukti:** README AI menyatakan file kontrol proyek AI sendiri (`AI_PROJECT_START.md`, `PROJECT_RULES.md`, `PROJECT_STATUS.md`, `NEXT_TASK.md`). README Engine belum berhasil diambil melalui path `README.md` dalam audit ini.

**Dampak:** Instruksi proyek turunan berisiko dianggap mengalahkan keputusan produk AMANAH SEO atau asumsi dari repo yang tidak terbaca dianggap benar.

**Tindakan:** Audit source AI/ENGINE secara baca-saja untuk kontrak, data ownership, lisensi, migration, routes/hooks, dan dependencies. Rekam hasil sebagai evidence migrasi; jangan mengubah repository tersebut pada tahap revisi pedoman.

**Status:** AI README terbaca; audit penuh source AI/ENGINE dan isi README Engine BELUM SELESAI.

### FIND-007 — Sumber kebenaran dan status dokumen perlu dibakukan

**Bukti:** README merujuk Pedoman Induk, Lingkup MVP, Paket Tunggal, Paket Implementasi Terkontrol, dan Struktur Menu Final; dokumen lama diarsipkan. Beberapa PR mengubah dokumentasi menu, sementara baseline PR berbeda.

**Dampak:** Status “final”, “draft”, “historis”, “belum diuji”, dan “siap merge” berpotensi digunakan tidak konsisten.

**Tindakan:** Tetapkan urutan otoritas dokumen:
1. keputusan pemilik yang eksplisit dan tercatat;
2. Pedoman Induk;
3. Lingkup MVP;
4. dokumen keputusan khusus (misalnya struktur menu);
5. paket implementasi dan acceptance criteria;
6. laporan audit sebagai bukti/status;
7. README sebagai indeks ringkas yang harus konsisten dengan dokumen di atas;
8. arsip sebagai riwayat, bukan sumber keadaan terkini.

Jika dua dokumen bertentangan, jangan memilih secara diam-diam; catat konflik dan selesaikan melalui revisi terkontrol.

**Status:** Kebutuhan rekonsiliasi teridentifikasi; audit silang semua dokumen BELUM SELESAI.

## 5. Status proyek saat audit awal

| Area | Status audit awal | Catatan |
|---|---|---|
| Visi produk dan prinsip | DOKUMENTASINYA TERSEDIA | Pedoman Induk sudah memuat tujuan dan batas produk. |
| Keputusan MVP | DOKUMENTASINYA TERSEDIA | Tujuh keputusan dasar disebut disetujui pada 2026-10-11. |
| Core MVP source | SUDAH ADA DI MAIN MENURUT README | Jangan samakan dengan penerimaan produk penuh. |
| PHP/Regression CI | DILAPORKAN PASS DI README/PR | Bukti tiap run perlu ditautkan dan diverifikasi per commit sebelum release. |
| WordPress runtime terbaru | MENUNGGU VERIFIKASI | Runtime 11/11 sebelumnya tidak otomatis berlaku setelah perubahan. |
| Backup dan restore | BELUM DIUJI menurut README/paket | Gerbang sebelum migrasi data. |
| WordPress internal content data | SOURCE TERBATAS TERSEDIA | Perlu uji pada target yang benar dan cek hasil aktual. |
| Google Search Console | BELUM TERBUKTI | Tidak boleh mengklaim metrik GSC aktif tanpa bukti akses dan query nyata. |
| Lisensi AI nyata | GERBANG EKSTERNAL TERBUKA dalam PR #58 | Mock CI tidak membuktikan aktivasi live. |
| Lisensi ENGINE nyata | PERLU DIVERIFIKASI | Kontrak, API state, entitlement, dan staging perlu bukti. |
| Migrasi penuh AI/ENGINE | BELUM DISETUJUI SEBAGAI RELEASE | Butuh inventory, ownership, migration plan, backup/restore, dan approval. |
| Sales Page PR #72 | OPEN menurut state yang diperiksa | Verifikasi state/diff/CI/runtime sebelum merge. |
| Navigasi PR #60–#71 | BEBERAPA PR OPEN DARI BASELINE BERBEDA | Perlu matriks konflik sebelum memilih satu jalur. |
| Production deployment | TIDAK DIOTORISASI OLEH AUDIT INI | Tidak ada perubahan runtime/hosting yang dilakukan. |

## 6. Keputusan yang tidak boleh diubah diam-diam

1. Produk akhir tetap satu plugin WordPress AMANAH SEO.
2. AI dan ENGINE tetap modul internal target akhir dengan lisensi independen.
3. Aktivasi plugin Core tidak membutuhkan lisensi.
4. Entitlement AI dan ENGINE tidak saling membuka.
5. Settings & Access harus dapat dijangkau untuk mengelola lisensi.
6. Fitur berlisensi harus dilindungi di server-side; menyembunyikan tombol/menu saja tidak cukup.
7. Sales Page dan aturan akses tanpa lisensi harus konsisten di menu, direct URL, dan aksi/endpoint.
8. Produksi, database, server lisensi, serta source repository AI/ENGINE tidak diubah oleh audit dokumen ini.
9. Jangan menyatakan fitur/runtime lulus tanpa bukti.
10. Jangan menyatakan proyek selesai sebelum seluruh kriteria penerimaan yang berlaku dipenuhi dan pemilik memberikan persetujuan akhir.

## 7. Rencana revisi dokumentasi

Pengerjaan dilakukan secara bertahap dan dokumentasi saja terlebih dahulu.

### Tahap A — Rekonsiliasi sumber kebenaran

- Verifikasi SHA terkini `main`, branch `RB1`, `RB2`, serta branch integrasi.
- Inventarisasi semua dokumen aktif dan arsip yang dirujuk README.
- Catat status tiap dokumen, pemilik keputusan, tanggal, bukti, dan konflik.
- Temukan apakah dokumen rencana penuntasan lama berada di branch lain atau tidak ada.
- Hasil: manifest dokumen dan daftar konflik, tanpa mengubah keputusan produk.

### Tahap B — Rekonsiliasi navigasi dan lisensi

- Bandingkan source menu/admin dan deskripsi PR #58/#60/#61/#62/#66/#68/#71/#72.
- Kunci matriks menu, destination, sales page, Settings & Access, direct URL, capability, entitlement, dan server-side denial.
- Pastikan perbedaan antara lisensi Core, lisensi AI, lisensi ENGINE, dan entitlement fitur tidak kabur.
- Hasil: revisi dokumen menu dan tabel acceptance; tidak mengubah kode.

### Tahap C — Rekonsiliasi implementasi dan pengujian

- Buat matriks fitur → file/komponen → status CI → WordPress runtime → staging → production → evidence SHA.
- Pisahkan PASS otomatis, PASS runtime, BELUM DIUJI, BLOCKED, dan NOT IMPLEMENTED.
- Rekam gerbang backup/restore, GSC, lisensi live, dan migrasi.
- Hasil: status implementasi yang tidak mencampur klaim dan bukti.

### Tahap D — Matriks keputusan PR

- Catat PR terbuka, base/head SHA, file berubah, dependensi, overlap, CI, runtime, dan keputusan yang diperlukan.
- Tentukan rekomendasi per PR: KEEP, REVISE, CLOSE, atau DEFER, dengan alasan yang dapat ditelusuri.
- Jangan merge/close PR selama audit dokumentasi kecuali pemilik secara terpisah mengizinkan tindakan tersebut.

### Tahap E — Satu rencana kerja yang dapat dieksekusi

- Urutkan blocker berdasarkan dependensi dan risiko, bukan berdasarkan nomor PR.
- Setiap item mempunyai tujuan, file allowlist, prasyarat, tes, bukti penerimaan, recovery, dan kondisi stop.
- Satu item dikerjakan pada satu waktu.
- Hasil: rencana yang menunjukkan langkah berikutnya saja setelah setiap gate terpenuhi.

## 8. Acceptance criteria audit dan revisi

Audit dokumentasi dianggap cukup untuk meminta persetujuan langkah implementasi berikutnya hanya jika:

- [ ] SHA dan baseline aktif tiap branch penting terverifikasi.
- [ ] Dokumen otoritatif, dokumen historis, dan dokumen yang hilang/berpindah telah dibedakan.
- [ ] Keputusan produk yang telah disetujui tidak dibuka ulang tanpa alasan.
- [ ] Konflik aturan menu/licensing sudah dicatat dan diselesaikan dalam dokumen.
- [ ] PR terbuka telah dipetakan terhadap base/head SHA dan overlap file.
- [ ] Klaim pengujian mempunyai bukti spesifik dan status yang benar.
- [ ] Semua gerbang eksternal yang belum terbukti tetap ditandai belum lulus.
- [ ] Daftar kerja memiliki urutan, file scope, tes, recovery, dan stop condition.
- [ ] Perubahan dokumentasi dilakukan pada branch terisolasi dan dapat direview.
- [ ] Tidak ada kode, konfigurasi, database, runtime, atau deployment yang berubah.

Kriteria ini hanya menilai kesiapan dokumentasi/perencanaan. Ini bukan kriteria bahwa produk AMANAH SEO telah selesai.

## 9. Langkah berikutnya yang direkomendasikan

**Langkah berikutnya saja:** lanjutkan Tahap A — rekonsiliasi sumber kebenaran. Verifikasi commit SHA aktual `main`, `RB1`, `RB2`, dan branch integrasi, lalu inventarisasi dokumen aktif/arsip beserta statusnya. Jangan mengubah dokumen otoritatif, kode, PR, atau runtime sebelum peta konflik tersebut selesai.

## 10. Catatan perubahan audit ini

- File ini ditambahkan sebagai dokumen audit terkontrol pada branch terpisah.
- Tidak mengubah `main`, RB1, RB2, PR yang sudah ada, kode plugin, database, server lisensi, atau website aktif.
- Temuan di atas harus diperlakukan sebagai hasil audit awal; status yang belum diverifikasi harus tetap ditandai demikian.
