# AMANAH SEO

**AMANAH SEO adalah sistem utama untuk membantu pemilik website mengelola, memelihara, dan meningkatkan kualitas SEO secara terarah, terukur, dan berkelanjutan.**

## Dokumen acuan

1. **[Pedoman Induk AMANAH SEO](docs/PEDOMAN_INDUK_AMANAH_SEO.md)** — sumber utama untuk visi, prinsip, pembagian produk, alur end-to-end, kontrol pemilik, status keputusan, backlog, dan gerbang sebelum implementasi.
2. **[Lingkup MVP AMANAH SEO](docs/11_MVP_SCOPE_PROPOSAL.md)** — definisi MVP dan catatan persetujuan tujuh keputusan dasar.

Dokumen lama yang bertanda **SUPERSEDED** dipertahankan sebagai rekam jejak historis dan bukan sumber aturan aktif kecuali diadopsi kembali secara eksplisit. Laporan audit historis tidak otomatis menjadi bukti kondisi runtime saat ini.

## Batas tiga produk

- **AMANAH SEO** — produk utama yang mengelola konteks website, kondisi, prioritas pekerjaan, tugas, persetujuan, status, dan pemantauan hasil.
- **AMANAH SEO AI** — produk AI/SEO tersendiri; bukan dependensi wajib.
- **AMANAH SEO ENGINE V2** — produk workflow SEO/konten terstruktur tersendiri; bukan dependensi wajib.

Integrasi produk pendamping bersifat opsional. Jangan menyatakan fitur atau integrasi tersedia sampai repositori/versi, kontrak, dan perilakunya benar-benar diverifikasi.

## Status pedoman

- Tujuh keputusan dasar MVP disetujui pemilik pada 2026-10-11.
- Lingkup MVP telah dikonsolidasikan: satu siklus **website → kondisi/temuan → prioritas beralasan → tugas → keputusan/pemeriksaan pemilik → hasil → langkah berikutnya**.
- Sumber data SEO spesifik harus dipilih dan diverifikasi sebelum implementasi.
- Kemampuan integrasi AI/ENGINE belum dinyatakan terverifikasi; integrasi tidak wajib untuk MVP.
- Keputusan komersial dan teknis non-pemblokir dipindahkan ke backlog agar tidak menghambat penutupan pedoman.
- Pedoman berada di branch `controlled/rebuild-product-pedoman` sebagai **DRAFT FINAL UNTUK PERSETUJUAN PENUTUPAN**. Belum digabung ke `main`.

## Batas kerja

Sebelum pedoman ditutup dan implementasi disetujui secara terpisah:
1. Fokus pada penyelesaian dan persetujuan pedoman AMANAH SEO.
2. Pemeriksaan AMANAH SEO AI dan AMANAH SEO ENGINE V2 hanya baca-saja.
3. Jangan mengubah kode, konfigurasi, database, runtime, lisensi, atau WordPress kedua produk pendamping.
4. Jangan menyinkronkan atau menjalankan implementasi sebagai bagian dari pekerjaan pedoman.
5. Pertahankan bukti historis dan jangan menyebut hal yang belum diperiksa sebagai PASS.
