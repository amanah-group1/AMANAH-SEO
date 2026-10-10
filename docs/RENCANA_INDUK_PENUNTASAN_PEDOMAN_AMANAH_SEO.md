# Rencana Induk Penuntasan Pedoman AMANAH SEO

**Status dokumen:** DRAFT — menunggu tinjauan dan persetujuan pemilik proyek  
**Status operasional:** **WAJIB DIBACA SEBELUM SETIAP PEKERJAAN** terkait AMANAH SEO  
**Versi:** 0.1  
**Tanggal:** 2026-10-10  
**Sumber acuan:** GitHub `amanahgroup123/AMANAH-SEO`, branch `main`  
**Cakupan:** Menuntaskan Pedoman AMANAH SEO (produk induk) saja.

> Instruksi operasional ini berlaku sejak dokumen tersedia di GitHub: sebelum melakukan pekerjaan baru, baca dokumen ini dan periksa status/progres terkini di repository. Status DRAFT berarti isi rencana belum disahkan sebagai kebijakan permanen; status itu tidak membatalkan instruksi untuk membacanya terlebih dahulu.

## 1. Tujuan dan hasil akhir

Menuntaskan Pedoman AMANAH SEO agar menjadi acuan yang konsisten, dapat ditelusuri, dan disetujui untuk produk induk. Pekerjaan tidak boleh berputar tanpa batas atau membuka kembali tahap yang sudah selesai tanpa bukti masalah baru.

**Definisi selesai untuk pekerjaan ini:** dokumentasi AMANAH SEO di GitHub telah diinventarisasi dan ditinjau sesuai cakupan, konflik dan referensi silang telah diselesaikan atau dicatat dengan disposisi eksplisit, keputusan yang memerlukan persetujuan telah disetujui atau secara jelas ditunda, kriteria penerimaan telah diperiksa, dan pemilik proyek telah menyetujui baseline final.

Penyelesaian pedoman **tidak** berarti platform telah diimplementasikan, produk telah lulus runtime, atau integrasi telah siap.

## 2. Batas kerja yang tidak boleh dilanggar

### Termasuk
- Repository `AMANAH-SEO` pada branch `main` sebagai sumber utama pedoman produk induk.
- Audit baca-saja terhadap dokumen, keputusan, roadmap, audit records, dan template yang sudah ada.
- Pemeriksaan terbatas pada GitHub untuk repository `AMANAH-SEO-AI` dan `amanah-seo-engine-v2` hanya untuk memetakan batas produk, dependensi, potensi konflik, serta pertanyaan yang perlu diputuskan oleh AMANAH SEO.
- Perbaikan dokumentasi AMANAH SEO setelah temuan, scope, dan cara perbaikannya jelas.
- Pencatatan progres, keputusan terbuka, bukti, dan hasil penerimaan di GitHub.

### Tidak termasuk
- Mengubah kode, branch, konfigurasi, database, atau runtime AMANAH SEO AI maupun AMANAH SEO ENGINE V2.
- Sinkronisasi repository ke komputer lokal atau WordPress sebelum gerbang penyelesaian dokumentasi dinyatakan lulus.
- Menyatakan runtime/database lulus berdasarkan pemeriksaan GitHub.
- Membangun platform, melaksanakan integrasi, mengubah API, atau memulai pekerjaan implementasi.
- Menambah fase, produk, dokumen wajib, atau fitur baru tanpa alasan berbukti dan persetujuan jika diperlukan.

## 3. Aturan sumber bukti

1. GitHub adalah satu-satunya sumber data untuk pekerjaan ini sesuai arahan pemilik proyek.
2. Repository `AMANAH-SEO` adalah sumber utama keputusan dan pedoman produk induk.
3. Repository AI dan ENGINE hanya sumber pendukung untuk fakta yang berkaitan dengan produk masing-masing; isinya tidak otomatis menjadi keputusan AMANAH SEO.
4. Catatan percakapan, ingatan, dugaan, dan nama file saja bukan bukti teknis yang cukup.
5. Jika sesuatu tidak ditemukan atau tidak dapat diverifikasi, tandai **NOT FOUND**, **NOT VERIFIED**, **NOT TESTED**, atau **BLOCKED** sesuai konteks. Jangan mengisi kekosongan dengan asumsi.
6. Setiap klaim yang relevan harus mengacu pada path GitHub dan, bila tersedia, commit SHA atau laporan audit yang mendukungnya.
7. Hasil GitHub-only tidak boleh digambarkan sebagai hasil runtime/database. Pemeriksaan runtime/database berada di luar cakupan dan dicatat **NOT TESTED** untuk rencana ini.

## 4. Status awal berdasarkan catatan GitHub yang tersedia

Status berikut adalah titik awal yang harus dikonfirmasi lagi saat pekerjaan dimulai; bukan klaim bahwa seluruh audit telah ditutup.

- README mencatat inventaris 28 artefak: 1 README, 11 dokumen inti, 12 catatan audit, dan 4 template.
- Roadmap `docs/09_ROADMAP_AND_GATES.md` berstatus **APPROVED** untuk roadmap saja. Persetujuan itu tidak sama dengan persetujuan seluruh pedoman.
- Assessment kesiapan Phase 2/Gate 2 berstatus **BLOCKED / NOT PASS** dalam laporan audit yang tercatat.
- CDR-011 masih tercatat terbuka dan memerlukan disposisi lanjutan.
- Audit GitHub AMANAH SEO AI dan AMANAH SEO ENGINE V2 berstatus parsial pada cakupan yang dilaporkan. Temuan dan keterbatasannya harus dirujuk, bukan diperlakukan sebagai hasil runtime.
- Local sync dan runtime test tidak menjadi prasyarat untuk menyelesaikan pedoman GitHub-only ini; keduanya tetap ditunda sampai gerbang dokumentasi diselesaikan dan ada arahan berikutnya.

## 5. Urutan kerja wajib

### Tahap A — Kunci baseline dan inventaris
- Baca dokumen ini terlebih dahulu.
- Periksa branch `main`, commit baseline, struktur repository, README, seluruh 11 dokumen inti, 12 catatan audit yang terdaftar, dan 4 template.
- Cocokkan daftar di README dengan isi GitHub yang benar-benar tersedia.
- Catat item yang hilang, duplikat, usang, atau tidak dapat diakses. Jangan langsung membuat dokumen tambahan untuk setiap celah.

**Keluaran:** inventaris tervalidasi dengan path, status, bukti, dan gap.

### Tahap B — Audit konsistensi pedoman
- Periksa konflik antar dokumen, istilah, status dokumen/keputusan/audit, rujukan silang, batas produk, source of truth, keamanan, lisensi/entitlement, integrasi, governance, roadmap, dan open questions.
- Bedakan temuan yang dapat diperbaiki secara editorial dari keputusan substantif yang memerlukan pemilik proyek.
- Jangan mengubah isi saat audit masih mengumpulkan bukti, kecuali perbaikan administratif kecil yang sudah jelas diizinkan dalam scope yang disepakati.

**Keluaran:** daftar temuan bernomor, bukti, dampak, rekomendasi, dan status persetujuan yang diperlukan.

### Tahap C — Rekonsiliasi keputusan
- Periksa setiap pertanyaan terbuka dan keputusan yang saling bertentangan.
- Tetapkan mana yang telah disetujui dengan bukti, mana yang hanya usulan, dan mana yang memerlukan keputusan pemilik proyek.
- Jangan mengubah keputusan komersial, arsitektur, atau kontrak produk menjadi final berdasarkan dugaan.
- Untuk keputusan yang belum bisa dibuat, tuliskan pilihan, dampak, dan status **OPEN / DEFERRED** secara eksplisit agar tidak menghambat hal yang tidak bergantung padanya.

**Keluaran:** Decision Register dan Open Questions yang konsisten dengan bukti dan persetujuan.

### Tahap D — Perbaiki dokumen secara terkendali
- Perbaiki hanya temuan yang telah memiliki dasar dan berada dalam scope.
- Pertahankan riwayat Git dan gunakan commit yang menjelaskan perubahan.
- Sinkronkan referensi silang dan status dokumen yang terdampak.
- Hindari duplikasi sumber kebenaran; perbarui dokumen kanonis dan rujuk ke sana.
- Jangan mengubah repository AI/ENGINE.

**Keluaran:** perubahan dokumentasi AMANAH SEO yang terukur dan dapat direview.

### Tahap E — Verifikasi dan gerbang penutupan
- Ulangi pemeriksaan inventaris dan seluruh temuan yang ditargetkan.
- Verifikasi referensi silang, status, link/path, dan bahwa keputusan belum disetujui tidak ditampilkan sebagai final.
- Periksa bahwa batas antara pedoman, target arsitektur, implementasi aktual, dan kesiapan runtime tetap jelas.
- Catat temuan yang tersisa, alasan penundaan, dan siapa yang perlu mengambil keputusan.
- Minta persetujuan eksplisit pemilik proyek atas baseline final; jangan menyimpulkan persetujuan dari diam atau dari status APPROVED dokumen lain.

**Keluaran:** laporan penutupan dengan checklist penerimaan, bukti GitHub, pengecualian, dan status persetujuan.

## 6. Kriteria penerimaan akhir

Pedoman hanya dapat dinyatakan **SELESAI MENURUT CAKUPAN GITHUB-ONLY** apabila seluruh butir berikut dipenuhi atau pengecualian yang tersisa disetujui secara eksplisit:

- [ ] Inventaris file yang diwajibkan sudah cocok dengan repository `main`; item hilang/di luar cakupan dicatat.
- [ ] Semua dokumen inti dan catatan audit yang relevan telah ditinjau sesuai scope.
- [ ] Konflik dan rujukan silang telah diperbaiki atau memiliki disposisi yang jelas.
- [ ] Status dokumen dibedakan dari status keputusan dan hasil audit.
- [ ] Semua keputusan yang belum disetujui tetap ditandai OPEN/PROPOSED/DEFERRED sesuai aturan.
- [ ] Batas AMANAH SEO, AMANAH SEO AI, dan AMANAH SEO ENGINE V2 jelas.
- [ ] Temuan pendukung dari AI/ENGINE ditautkan ke bukti GitHub dan tidak dianggap sebagai persetujuan atau hasil runtime.
- [ ] Tidak ada klaim runtime/database/test PASS yang tidak didukung; aspek di luar cakupan ditandai NOT TESTED.
- [ ] Seluruh temuan penutupan memiliki status dan alasan yang dapat ditinjau.
- [ ] Ada commit baseline dokumentasi final dan persetujuan eksplisit pemilik proyek atas cakupan tersebut.

**Pemisahan gerbang:** kelulusan checklist ini hanya menutup pekerjaan pedoman GitHub-only. Ini tidak otomatis meluluskan Roadmap Gate 1/2, audit produk, runtime, atau kesiapan integrasi.

## 7. Aturan wajib pada setiap sesi

Sebelum mulai:
1. Baca dokumen ini dari GitHub `main`.
2. Baca status/progres terbaru dan temuan terbuka yang relevan.
3. Sebutkan secara singkat: tahap aktif, hasil terakhir yang terbukti, hambatan yang benar-benar tersisa, dan **satu langkah berikutnya**.
4. Tetap dalam scope pedoman AMANAH SEO. Jangan mengulang tahap yang sudah selesai tanpa bukti baru.

Selama bekerja:
- Audit sebelum revisi.
- Jangan mengubah kode atau runtime produk.
- Jangan membuat perubahan ke AI/ENGINE.
- Jangan menganggap usulan sebagai keputusan.
- Jangan memperluas scope diam-diam.
- Catat progres pada GitHub agar sesi berikutnya dapat melanjutkan tanpa memulai ulang.

Sebelum menyatakan selesai:
- Tunjukkan checklist penerimaan, bukti GitHub, sisa pengecualian, dan status persetujuan.
- Jika masih ada butir wajib yang belum terpenuhi, statusnya **IN PROGRESS**, **BLOCKED**, atau **PARTIAL**, bukan selesai.

## 8. Register progres

| Tahap | Status awal | Bukti/hasil yang diperlukan untuk menutup |
|---|---|---|
| A — Baseline dan inventaris | IN PROGRESS — perlu revalidasi | Inventaris GitHub tervalidasi |
| B — Audit konsistensi | PARTIAL — berdasarkan audit tercatat | Temuan konsistensi lengkap dan terdisposisi |
| C — Rekonsiliasi keputusan | BLOCKED/PARTIAL — keputusan terbuka masih ada | Decision Register dan Open Questions konsisten |
| D — Revisi terkendali | NOT STARTED dalam rencana ini | Commit perubahan yang didukung temuan |
| E — Verifikasi dan penutupan | NOT STARTED dalam rencana ini | Checklist final, bukti, dan persetujuan eksplisit |

Perbarui tabel ini setelah ada bukti baru; jangan menandai tahap selesai hanya karena pekerjaan telah dibahas.

## 9. Catatan sesi wajib

Setiap sesi kerja harus memperbarui catatan progres di GitHub dengan:
- Tanggal dan baseline commit yang diperiksa.
- Dokumen/path yang dibaca.
- Pemeriksaan yang benar-benar dilakukan.
- Temuan yang ditutup dan bukti.
- Temuan yang masih terbuka serta alasan.
- Keputusan yang dibutuhkan dari pemilik proyek.
- Satu langkah berikutnya.

Jangan membuat laporan progres baru yang menduplikasi sumber yang ada kecuali format baru memang diperlukan; utamakan memperbarui register atau catatan kanonis yang tepat.

## 10. Riwayat perubahan

| Versi | Status | Perubahan |
|---|---|---|
| 0.1 | DRAFT | Membentuk rencana induk, batas scope, aturan wajib baca, urutan kerja, dan kriteria penutupan. |

---
**Penting:** dokumen ini dibuat untuk mengakhiri pekerjaan secara terarah, bukan membuka proyek baru tanpa batas. Prioritas tunggal sampai gerbang dokumentasi terpenuhi adalah menuntaskan Pedoman AMANAH SEO.