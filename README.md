# AMANAH SEO

**AMANAH SEO adalah sistem utama untuk membantu pemilik website mengelola, memelihara, dan meningkatkan kualitas SEO secara terarah, terukur, dan berkelanjutan.**

## Dokumen acuan

1. **[Pedoman Induk AMANAH SEO](docs/PEDOMAN_INDUK_AMANAH_SEO.md)** — acuan utama untuk tujuan produk, prinsip, pembagian produk, alur kerja, keputusan, dan gerbang sebelum implementasi.
2. **[Lingkup MVP AMANAH SEO](docs/11_MVP_SCOPE_PROPOSAL.md)** — fungsi MVP, batas yang tidak termasuk, keputusan dasar, dan kriteria penerimaan.
3. **[Paket Tunggal Kesiapan Implementasi dan Migrasi Modul](docs/13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md)** — satu paket untuk audit WordPress, bukti WordPress/GSC, audit source AI/Engine, risiko database/kredensial/lisensi, migrasi, dan acceptance criteria.
4. [Paket Kesiapan MVP versi sebelumnya](docs/12_MVP_IMPLEMENTATION_PACKAGE.md) dipertahankan sebagai riwayat; gunakan paket tunggal versi 1.0 sebagai acuan kerja.
5. **[Paket Implementasi Terkontrol Core AMANAH SEO](docs/14_CONTROLLED_IMPLEMENTATION_PACKAGE_AMANAH_SEO.md)** — rencana persiapan implementasi pertama, gerbang teknis, batas scope, acceptance tests, dan recovery. Statusnya perencanaan saja; belum menjadi izin coding.
6. **[Struktur Menu Final AMANAH SEO](docs/15_STRUKTUR_MENU_FINAL_AMANAH_SEO.md)** — keputusan navigasi satu plugin, urutan kelompok Core/AI/ENGINE, dan perilaku menu ketika entitlement modul tidak aktif.

Dokumen lama yang sudah digantikan berada di [arsip dokumentasi](docs/archive/README.md). Dokumen arsip dan laporan audit historis merupakan rekam jejak, bukan bukti otomatis bahwa keadaan kode atau runtime saat ini sudah lulus.

## Status implementasi teknis

- Core MVP saat ini mencakup dashboard, profil satu website/workspace, temuan dengan status dan catatan penyelesaian, tugas dengan tenggat/dependensi, persetujuan, riwayat, diagnostik aman, serta uji runtime satu-klik yang membersihkan catatan uji.
- GitHub Actions PHP syntax dan regression tests PASS pada PHP 7.4 dan 8.2. Uji runtime WordPress pada situs lokal belum dijalankan; backup/restore, GSC, integrasi server lisensi, serta migrasi modul AI/ENGINE tetap BELUM DIUJI/belum tersedia.
- Jangan menganggap Core MVP sudah memenuhi seluruh produk atau menyalinnya ke produksi. Paket perubahan: [Core MVP](https://github.com/amanahgroup123/AMANAH-SEO/pull/40), [perbaikan regresi](https://github.com/amanahgroup123/AMANAH-SEO/pull/41), [workspace dan status temuan](https://github.com/amanahgroup123/AMANAH-SEO/pull/42), [tenggat dan dependensi](https://github.com/amanahgroup123/AMANAH-SEO/pull/43), [diagnostik](https://github.com/amanahgroup123/AMANAH-SEO/pull/44), dan [uji runtime satu-klik](https://github.com/amanahgroup123/AMANAH-SEO/pull/45).

## Arsitektur produk

- **AMANAH SEO** adalah satu-satunya plugin WordPress yang dipasang pengguna pada target akhir.
- AMANAH SEO dapat diunduh, dipasang, dan diaktifkan tanpa lisensi. Dari plugin AMANAH SEO, pengguna mengelola dua lisensi modul yang terpisah: lisensi AMANAH SEO AI dan lisensi AMANAH SEO ENGINE V2. Status dan entitlement keduanya independen; lisensi hanya diperlukan untuk akses modul terkait. Plugin AI/Engine tidak dipasang terpisah pada target akhir.
- Kemampuan dari **AMANAH SEO AI** dan **AMANAH SEO ENGINE V2** ditata sebagai modul internal setelah audit dan migrasi terkontrol; kedua repository tetap baseline sumber sampai paket migrasi disetujui.
- Core, modul AI, dan modul Engine tetap memiliki batas tanggung jawab, kontrak, pengujian, dan kepemilikan data yang jelas. Fitur modul tidak otomatis menjadi bagian MVP fungsional.
- AMANAH SEO Lifecycle Manager menjadi koordinator tunggal lifecycle komponen internal. Keputusan packaging ini bukan izin untuk memindahkan kode atau mengubah runtime.

## Status dokumen dan persetujuan

- Pedoman Induk dan Lingkup MVP sudah dikonsolidasikan. Paket gabungan audit kesiapan dan migrasi modul tersedia di [Paket Tunggal versi 1.0](docs/13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md); dokumen ini tidak menyatakan runtime lulus atau memberi izin deployment.
- Tujuh keputusan dasar MVP disetujui pemilik pada 2026-10-11.
- Lingkup MVP adalah satu siklus: **website → kondisi/temuan → prioritas beralasan → tugas → pemeriksaan/keputusan pemilik → hasil → langkah berikutnya**.
- Sumber awal konseptual yang dipilih adalah data internal WordPress (struktur/konten) dan Google Search Console (performa pencarian); akses teknis masing-masing harus dibuktikan sebelum fitur terkait dinyatakan berfungsi.
- Paket kesiapan sebelumnya digantikan sebagai acuan kerja oleh [Paket Tunggal versi 1.0](docs/13_SINGLE_PACKAGE_IMPLEMENTATION_READINESS_AND_MODULE_MIGRATION.md).
- Website WordPress testing dapat dipakai untuk menguji siklus dasar tanpa koneksi GSC, menggunakan data WordPress yang benar-benar terbaca dan/atau temuan manual berlabel jelas. Fitur metrik pencarian tetap menunggu bukti akses GSC. Data yang belum tersedia harus ditandai jelas; sistem tidak boleh mengarang metrik.
- Pedoman konseptual selesai bukan berarti implementasi, sinkronisasi runtime, deployment, atau merge berikutnya otomatis disetujui.

## Aturan kerja

1. Kerjakan hanya AMANAH SEO, kecuali pemilik secara eksplisit menyetujui cakupan lain.
2. Audit kondisi nyata sebelum implementasi dan jangan menyatakan PASS tanpa bukti.
3. Gunakan bahasa yang mudah dipahami pada penjelasan dan antarmuka.
4. Jangan mengubah website, database, runtime, konfigurasi, atau repository sumber AI/Engine selama tahap persiapan dokumen.
5. Implementasi berjalan melalui paket terkontrol dengan ruang lingkup, kriteria penerimaan, dan rencana pemulihan yang jelas; jangan mengubah source AI/ENGINE, server lisensi, atau data produksi tanpa gerbang dan persetujuan khusus.


**Penutupan pedoman:** keputusan konseptual yang diperlukan untuk menetapkan arah produk dan MVP telah dikonsolidasikan. Bukti akses WordPress/GSC dan pengujian penerimaan adalah gerbang teknis sebelum implementasi/fitur terkait, bukan alasan untuk membuka kembali keputusan konsep. Implementasi tetap memerlukan persetujuan terpisah.
