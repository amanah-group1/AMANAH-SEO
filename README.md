# AMANAH SEO

**AMANAH SEO adalah sistem utama untuk membantu pemilik website mengelola, memelihara, dan meningkatkan kualitas SEO secara terarah, terukur, dan berkelanjutan.**

## Dokumen acuan

1. **[Pedoman Induk AMANAH SEO](docs/PEDOMAN_INDUK_AMANAH_SEO.md)** — acuan utama untuk tujuan produk, prinsip, pembagian produk, alur kerja, keputusan, dan gerbang sebelum implementasi.
2. **[Lingkup MVP AMANAH SEO](docs/11_MVP_SCOPE_PROPOSAL.md)** — fungsi MVP, batas yang tidak termasuk, keputusan dasar, dan kriteria penerimaan.
3. **[Paket Kesiapan Pengerjaan MVP](docs/12_MVP_IMPLEMENTATION_PACKAGE.md)** — rancangan persiapan, urutan kerja, dan bukti penerimaan. Dokumen ini masih draft sampai disetujui pemilik.

Dokumen lama yang sudah digantikan berada di [arsip dokumentasi](docs/archive/README.md). Dokumen arsip dan laporan audit historis merupakan rekam jejak, bukan bukti otomatis bahwa keadaan kode atau runtime saat ini sudah lulus.

## Arsitektur produk

- **AMANAH SEO** adalah satu-satunya plugin WordPress yang dipasang pengguna.
- **AMANAH SEO AI** menjadi modul internal untuk fitur AI/SEO yang telah diaudit.
- **AMANAH SEO Engine** menjadi modul internal untuk workflow SEO/konten yang telah diaudit.
- Core, modul AI, dan modul Engine tetap terpisah secara internal dengan kontrak, pengujian, dan batas kepemilikan data yang jelas.

Keputusan arsitektur satu plugin disetujui pemilik pada 2026-10-11. Ini adalah target arsitektur, **belum berarti kode telah digabungkan**. Repository AI dan Engine tetap menjadi baseline sumber selama audit/migrasi. Fitur modul tidak otomatis menjadi bagian fungsional MVP dan tidak boleh diklaim siap sebelum diverifikasi.

## Status dokumen dan persetujuan

- Pedoman Induk dan Lingkup MVP tersedia di branch `main`. Perubahan pedoman dikonsolidasikan melalui [PR #23](https://github.com/amanahgroup123/AMANAH-SEO/pull/23), yang sudah digabung.
- Tujuh keputusan dasar MVP disetujui pemilik pada 2026-10-11.
- Lingkup MVP adalah satu siklus: **website → kondisi/temuan → prioritas beralasan → tugas → pemeriksaan/keputusan pemilik → hasil → langkah berikutnya**.
- Sumber data tertentu harus dibuktikan sebelum fitur yang bergantung padanya dibuat atau dinyatakan berfungsi.
- Paket kesiapan pengerjaan MVP sedang diajukan melalui [PR #24](https://github.com/amanahgroup123/AMANAH-SEO/pull/24). PR tersebut masih draft dan belum digabung.
- Penyesuaian target arsitektur satu plugin sedang disusun melalui branch `controlled/architecture-single-plugin-modules`; belum ada perubahan kode produk atau runtime.
- Website WordPress testing dapat dipakai untuk menguji siklus dasar tanpa website resmi atau Google Search Console. Data yang belum tersedia harus ditandai jelas; sistem tidak boleh mengarang metrik.
- Pedoman konseptual selesai bukan berarti implementasi, sinkronisasi runtime, deployment, atau merge berikutnya otomatis disetujui.

## Aturan kerja

1. Kerjakan hanya AMANAH SEO, kecuali pemilik secara eksplisit menyetujui cakupan lain.
2. Audit kondisi nyata sebelum implementasi dan jangan menyatakan PASS tanpa bukti.
3. Gunakan bahasa yang mudah dipahami pada penjelasan dan antarmuka.
4. Jangan mengubah website, database, runtime, konfigurasi, atau produk pendamping selama tahap persiapan dokumen.
5. Mulai coding hanya setelah paket, ruang lingkup, daftar perubahan, kriteria penerimaan, rencana rollback, dan persetujuan pemilik jelas.
