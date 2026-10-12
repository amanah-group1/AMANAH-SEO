# AMANAH SEO

**AMANAH SEO adalah sistem utama untuk membantu pemilik website mengelola, memelihara, dan meningkatkan kualitas SEO secara terarah, terukur, dan berkelanjutan.**

## Dokumen acuan

1. **[Pedoman Induk AMANAH SEO](docs/PEDOMAN_INDUK_AMANAH_SEO.md)** — sumber utama untuk visi, prinsip, pembagian produk, alur end-to-end, kontrol pemilik, status keputusan, backlog, dan gerbang sebelum implementasi.
2. **[Lingkup MVP AMANAH SEO](docs/11_MVP_SCOPE_PROPOSAL.md)** — definisi MVP dan catatan persetujuan tujuh keputusan dasar.

Dokumen lama yang sudah digantikan dipindahkan ke [arsip dokumentasi](docs/archive/README.md), tetap dipertahankan sebagai rekam jejak historis, dan bukan sumber aturan aktif kecuali diadopsi kembali secara eksplisit. Laporan audit historis tetap terpisah di `docs/audits/` dan tidak otomatis menjadi bukti kondisi runtime saat ini.

## Arsitektur paket dan modul

- **AMANAH SEO** adalah satu-satunya plugin WordPress yang dipasang dan diaktifkan pengguna.
- **AMANAH SEO AI** dan **AMANAH SEO ENGINE** adalah modul di dalam paket AMANAH SEO, bukan plugin yang perlu dipasang terpisah.
- AMANAH SEO menjadi pusat pengelolaan API key/kredensial AI, status lisensi, dan entitlement modul.
- Lisensi AI dan Engine independen; lisensi satu modul tidak mengaktifkan modul lainnya.
- Semua menu modul tetap terlihat dan dapat dibuka. Jika lisensi modul tidak aktif, halaman menampilkan sales page yang menjelaskan kebutuhan lisensi; fitur berlisensi tetap ditolak.
- Pemeriksaan entitlement wajib ditegakkan di sisi server pada setiap jalur eksekusi yang dilindungi.
- Batas fungsi setiap modul tetap dipertahankan. Integrasi dengan sistem eksternal tetap opsional dan harus diverifikasi sebelum dinyatakan tersedia.

## Status pedoman

- Tujuh keputusan dasar MVP disetujui pemilik pada 2026-10-11.
- Lingkup MVP telah dikonsolidasikan: satu siklus **website → kondisi/temuan → prioritas beralasan → tugas → keputusan/pemeriksaan pemilik → hasil → langkah berikutnya**.
- Sumber data SEO spesifik harus dipilih dan diverifikasi sebelum implementasi.
- Paket tunggal dan lisensi modul independen telah ditetapkan sebagai prinsip arsitektur pada 2026-10-12; kemampuan fitur tetap harus diverifikasi sebelum dinyatakan siap.
- Keputusan komersial dan teknis non-pemblokir dipindahkan ke backlog agar tidak menghambat penutupan pedoman.
- Pedoman konseptual dinyatakan **SELESAI** atas instruksi pemilik pada 2026-10-11 dan berada di branch `controlled/rebuild-product-pedoman`. Branch belum digabung ke `main`; implementasi belum disetujui.

## Batas pekerjaan tahap pedoman (tahap ini telah ditutup)

Batas berikut tetap berlaku untuk mencegah perluasan ruang lingkup tanpa persetujuan:
1. Fokus pada penyelesaian dan persetujuan pedoman AMANAH SEO.
2. Pemeriksaan AMANAH SEO AI dan AMANAH SEO ENGINE V2 hanya baca-saja.
3. Jangan mengubah kode, konfigurasi, database, runtime, lisensi, atau WordPress kedua produk pendamping.
4. Jangan menyinkronkan atau menjalankan implementasi sebagai bagian dari pekerjaan pedoman.
5. Pertahankan bukti historis dan jangan menyebut hal yang belum diperiksa sebagai PASS.
