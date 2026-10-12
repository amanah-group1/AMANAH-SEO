# AMANAH SEO

**AMANAH SEO adalah sistem utama untuk membantu pemilik website mengelola, memelihara, dan meningkatkan kualitas SEO secara terarah, terukur, dan berkelanjutan.**

## Dokumen acuan

1. **[Pedoman Induk AMANAH SEO](docs/PEDOMAN_INDUK_AMANAH_SEO.md)** — acuan utama untuk tujuan produk, prinsip, pembagian produk, alur kerja, keputusan, dan gerbang sebelum implementasi.
2. **[Lingkup MVP AMANAH SEO](docs/11_MVP_SCOPE_PROPOSAL.md)** — fungsi MVP, batas yang tidak termasuk, keputusan dasar, dan kriteria penerimaan.
3. **[Paket Tunggal Kesiapan Implementasi dan Migrasi Modul](docs/13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md)** — satu paket untuk audit WordPress, bukti WordPress/GSC, audit source AI/Engine, risiko database/kredensial/lisensi, migrasi, dan acceptance criteria.
5. **[Struktur Menu Final AMANAH SEO](docs/13_STRUKTUR_MENU_FINAL_AMANAH_SEO.md)** — seluruh menu berada di bawah AMANAH SEO dengan aturan tampilan dan lisensi modul.
4. [Paket Kesiapan MVP versi sebelumnya](docs/12_MVP_IMPLEMENTATION_PACKAGE.md) dipertahankan sebagai riwayat; gunakan paket tunggal versi 1.0 sebagai acuan kerja.

Dokumen lama yang sudah digantikan berada di [arsip dokumentasi](docs/archive/README.md). Dokumen arsip dan laporan audit historis merupakan rekam jejak, bukan bukti otomatis bahwa keadaan kode atau runtime saat ini sudah lulus.

## Arsitektur produk

- **AMANAH SEO** adalah satu-satunya plugin WordPress yang dipasang pengguna pada target akhir.
- Kemampuan dari **AMANAH SEO AI** dan **AMANAH SEO ENGINE V2** ditata sebagai modul internal setelah audit dan migrasi terkontrol; kedua repository tetap baseline sumber sampai paket migrasi disetujui.
- Core, modul AI, dan modul Engine tetap memiliki batas tanggung jawab, kontrak, pengujian, dan kepemilikan data yang jelas. Fitur modul tidak otomatis menjadi bagian MVP fungsional.
- AMANAH SEO Lifecycle Manager menjadi koordinator tunggal lifecycle komponen internal.
- Lisensi/entitlement AI dan Engine independen; semua menu modul tetap terlihat, halaman tanpa entitlement menampilkan sales page, dan operasi berlisensi ditolak di sisi server.
- AMANAH SEO menjadi pusat pengelolaan API key/kredensial AI serta status lisensi dan entitlement. Keputusan packaging ini bukan izin untuk memindahkan kode atau mengubah runtime.

## Status dokumen dan persetujuan

- Pedoman Induk dan Lingkup MVP sudah dikonsolidasikan. Paket gabungan audit kesiapan dan migrasi modul tersedia di [Paket Tunggal versi 1.0](docs/13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md); dokumen ini tidak menyatakan runtime lulus atau memberi izin deployment.
- Tujuh keputusan dasar MVP disetujui pemilik pada 2026-10-11.
- Lingkup MVP adalah satu siklus: **website → kondisi/temuan → prioritas beralasan → tugas → pemeriksaan/keputusan pemilik → hasil → langkah berikutnya**.
- Sumber awal konseptual yang dipilih adalah data internal WordPress (struktur/konten) dan Google Search Console (performa pencarian); akses teknis masing-masing harus dibuktikan sebelum fitur terkait dinyatakan berfungsi.
- Paket kesiapan sebelumnya digantikan sebagai acuan kerja oleh [Paket Tunggal versi 1.0](docs/13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md).
- Website WordPress testing dapat dipakai untuk menguji siklus dasar tanpa koneksi GSC, menggunakan data WordPress yang benar-benar terbaca dan/atau temuan manual berlabel jelas. Fitur metrik pencarian tetap menunggu bukti akses GSC. Data yang belum tersedia harus ditandai jelas; sistem tidak boleh mengarang metrik.
- Prinsip paket tunggal dan lisensi modul independen ditetapkan pada 2026-10-12. Pedoman konseptual selesai bukan berarti implementasi, sinkronisasi runtime, deployment, atau merge berikutnya otomatis disetujui.

## Aturan kerja

1. Kerjakan hanya AMANAH SEO, kecuali pemilik secara eksplisit menyetujui cakupan lain.
2. Audit kondisi nyata sebelum implementasi dan jangan menyatakan PASS tanpa bukti.
3. Gunakan bahasa yang mudah dipahami pada penjelasan dan antarmuka.
4. Jangan mengubah website, database, runtime, konfigurasi, atau repository sumber AI/Engine selama tahap persiapan dokumen.
5. Mulai coding hanya setelah paket, ruang lingkup, daftar perubahan, kriteria penerimaan, rencana rollback, dan persetujuan pemilik jelas.


**Penutupan pedoman:** keputusan konseptual yang diperlukan untuk menetapkan arah produk dan MVP telah dikonsolidasikan. Bukti akses WordPress/GSC dan pengujian penerimaan adalah gerbang teknis sebelum implementasi/fitur terkait, bukan alasan untuk membuka kembali keputusan konsep. Implementasi tetap memerlukan persetujuan terpisah.
