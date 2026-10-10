> **STATUS: DOKUMEN LAMA / TIDAK BERLAKU SEBAGAI PEDOMAN AKTIF.** Mulai 2026-10-10, visi, tujuan, batas produk, dan alur utama ditentukan oleh [Pedoman Induk AMANAH SEO](PEDOMAN_INDUK_AMANAH_SEO.md). Dokumen ini dipertahankan untuk penelusuran historis dan tidak boleh dipakai untuk mengesampingkan pedoman induk baru. Jika ada isi yang masih berguna, adopsi kembali secara eksplisit setelah ditinjau; status keputusan lama tidak otomatis menjadi keputusan aktif.

---

﻿# AMANAH SEO — Audit and Readiness

**Status:** APPROVED — metodologi dan kriteria audit disetujui; status audit tetap ditentukan oleh bukti aktual  
**Bukti persetujuan baseline:** [commit b4436c358b3f89aa52d232c125c4694405cdd3c6](https://github.com/amanahgroup123/AMANAH-SEO/commit/b4436c358b3f89aa52d232c125c4694405cdd3c6) (2026-10-09).  
**Cakupan:** Dokumentasi, produk, integrasi, keamanan, dan kesiapan implementasi AMANAH SEO

## 1. Tujuan

Dokumen ini menetapkan metode audit dan gerbang kesiapan sebelum AMANAH SEO dikembangkan atau diintegrasikan dengan produk yang sudah ada.

Dokumen ini bukan bukti bahwa repository, aplikasi, database, atau runtime telah diaudit.

## 2. Prinsip Audit

- Audit dilakukan sebelum implementasi.
- Kesimpulan harus didukung bukti yang dapat diperiksa.
- Temuan harus memiliki ruang lingkup yang jelas.
- Kondisi yang belum diperiksa tidak boleh dianggap lulus.
- Rekomendasi harus dibedakan dari fakta yang ditemukan.
- Perbaikan tidak boleh melampaui ruang lingkup yang disetujui.
- Fitur yang sudah terbukti berjalan harus dipertahankan kecuali ada alasan dan persetujuan untuk mengubahnya.

## 3. Status Hasil Audit

Gunakan status berikut:

- **PASS:** persyaratan telah diperiksa dan bukti memenuhi kriteria.
- **PARTIAL:** sebagian persyaratan terpenuhi, tetapi masih ada kekurangan atau cakupan yang belum selesai.
- **BLOCKED:** audit atau pengujian tidak dapat dilanjutkan karena prasyarat atau akses tidak tersedia.
- **FAIL:** bukti menunjukkan persyaratan tidak terpenuhi.
- **NOT TESTED:** belum ada pengujian yang memadai.
- **NOT APPLICABLE:** persyaratan tidak berlaku; alasan harus dicatat.

Status keseluruhan tidak boleh PASS jika terdapat kegagalan kritis atau persyaratan wajib yang belum diverifikasi.

## 4. Tahap Audit

### Tahap A — Audit Dokumentasi

Periksa:
- Project Charter dan batas cakupan.
- Decision Register serta keputusan yang belum final.
- Architecture Blueprint.
- Product Boundaries.
- Integration Contracts.
- License and Entitlements.
- Security and Secrets.
- Open Questions.
- Konsistensi istilah, status, dan referensi silang.

Keluaran: daftar konflik, keputusan terbuka, asumsi, dan dokumen yang perlu diperbaiki.

### Tahap B — Audit Repository Produk

Lakukan secara terpisah untuk setiap produk:

1. Identifikasi repository dan branch yang benar.
2. Catat commit, working tree, serta file yang berubah.
3. Baca dokumentasi utama dan aturan proyek.
4. Petakan struktur source code dan entry point.
5. Identifikasi modul, dependensi, konfigurasi, serta pengujian.
6. Telusuri alur fitur yang relevan dari entry point sampai persistence dan tampilan.
7. Catat fitur yang sudah terbukti berjalan dan risiko regresinya.

Keluaran: laporan kondisi aktual dengan bukti path, simbol kode, commit, dan hasil tes bila tersedia.

### Tahap C — Audit Database

Periksa:
- Skema dan versi database.
- Tabel, kolom, indeks, relasi, dan constraint.
- Mekanisme instalasi, migrasi, serta aktivasi.
- Perbedaan antara skema yang diharapkan dan keadaan aktual.
- Kepemilikan data serta batas isolasi antarproduk.
- Strategi backup dan pemulihan.

Jangan menganggap skema aktual sama dengan dokumentasi tanpa verifikasi.

### Tahap D — Audit Runtime

Periksa:
- Aplikasi dan lingkungan yang benar-benar aktif.
- Versi source code yang terpasang.
- Konfigurasi runtime yang relevan.
- Status aktivasi produk.
- Alur pengguna yang dapat direproduksi.
- Log dan pesan kesalahan yang sudah disamarkan.
- Hasil positif dan negatif dari pengujian.

Catat URL atau nama lingkungan, waktu pemeriksaan, versi kode, langkah reproduksi, dan hasil aktual. Jangan memasukkan password, API key, atau token ke laporan.

### Tahap E — Audit Integrasi

Periksa:
- Identitas pemanggil.
- Autentikasi dan otorisasi.
- Product manifest atau identifikasi produk jika kontrak tersebut disetujui.
- License, entitlement, dan activation checks.
- Struktur request dan response.
- Penanganan timeout, retry, dan kegagalan.
- Versi kontrak dan kompatibilitas.
- Pencatatan diagnostik dan audit.
- Perilaku saat salah satu produk tidak tersedia.

Keluaran: daftar kesenjangan kontrak dan keputusan integrasi yang dibutuhkan.

### Tahap F — Audit Keamanan dan Operasional

Periksa:
- Autentikasi, authorization, dan batas role.
- Penanganan secret.
- CSRF, validasi input, dan output encoding.
- Penanganan kesalahan dan log.
- Backup, restore, serta prosedur pemulihan.
- Pengujian hak akses dan penolakan akses.
- Kesiapan deployment dan rollback.

Audit ini tidak menggantikan penetration test formal apabila pengujian tersebut diperlukan.

## 5. Format Temuan

Setiap temuan harus mencakup:

- ID temuan.
- Judul.
- Status dan tingkat risiko.
- Produk serta komponen yang terdampak.
- Commit atau versi yang diperiksa.
- Bukti dan langkah reproduksi.
- Kondisi yang diharapkan.
- Kondisi aktual.
- Dampak.
- Akar masalah jika telah terbukti.
- Rekomendasi.
- Kriteria penerimaan.
- Status perbaikan dan hasil pengujian ulang.

Jangan menyebut penyebab sebagai fakta sebelum didukung bukti.

## 6. Tingkat Risiko

Gunakan klasifikasi awal berikut:

- **CRITICAL:** risiko langsung terhadap kontrol akses utama, rahasia penting, atau integritas data yang memerlukan penghentian rilis.
- **HIGH:** risiko besar terhadap keamanan, data, lisensi, atau operasi utama.
- **MEDIUM:** risiko terbatas yang membutuhkan perbaikan terencana.
- **LOW:** dampak terbatas atau masalah minor.
- **INFO:** observasi, klarifikasi, atau peluang peningkatan.

Tingkat risiko harus disertai alasan, bukan hanya label.

## 7. Readiness Gates

### Gate 0 — Dokumentasi Awal

**Tujuan:** memastikan baseline dokumentasi dapat ditinjau secara konsisten. Gate 0 tidak dapat dinyatakan PASS hanya karena file tersedia.

Kriteria yang harus dibuktikan:
- [ ] Seluruh 11 dokumen inti dan 4 template yang dicantumkan secara eksplisit di README tersedia pada path yang benar.
- [ ] Status dokumen menggunakan kosakata governance; status keputusan dan audit dipisahkan.
- [ ] Batas platform induk, AMANAH SEO AI, dan AMANAH SEO ENGINE V2 dijelaskan tanpa klaim implementasi yang belum dibuktikan.
- [ ] Semua pertanyaan yang belum diputuskan tercatat sebagai OPEN dan tidak dipresentasikan sebagai keputusan final.
- [ ] Referensi silang ke dokumen dan template yang disebutkan dapat ditemukan.
- [ ] Struktur repository dan nama file cocok dengan daftar di README.
- [ ] Hasil pemeriksaan dicatat, termasuk setiap item yang BLOCKED atau NOT TESTED.

**Exit criteria:** Gate 0 berstatus PASS hanya jika seluruh kriteria wajib di atas telah diperiksa dan lulus dengan bukti yang dapat ditinjau. Jika ada kriteria wajib yang gagal, belum diperiksa, atau tidak memiliki bukti, gunakan PARTIAL, FAIL, BLOCKED, atau NOT TESTED sesuai keadaan; jangan menyimpulkan PASS.

### Gate 1 — Baseline Produk
- [ ] Repository dan branch telah diverifikasi.
- [ ] Dokumentasi master produk telah diperiksa.
- [ ] Commit dan kondisi working tree dicatat.
- [ ] Struktur source dan pengujian dipetakan.

### Gate 2 — Kontrak dan Risiko
- [ ] Alur produk yang relevan telah diaudit.
- [ ] Database dan runtime diperiksa bila diperlukan.
- [ ] Kesenjangan kontrak diidentifikasi.
- [ ] Risiko keamanan dan regresi dicatat.

### Gate 3 — Persetujuan Rencana
- [ ] Temuan telah ditinjau.
- [ ] Ruang lingkup koreksi ditentukan.
- [ ] Rencana implementasi dan pengujian tersedia.
- [ ] Kriteria penerimaan jelas.
- [ ] Persetujuan implementasi tercatat.

### Gate 4 — Verifikasi Implementasi
- [ ] Perubahan sesuai ruang lingkup.
- [ ] Pengujian yang relevan dijalankan.
- [ ] Tidak ada regresi yang diketahui tanpa penanganan.
- [ ] Bukti sebelum dan sesudah tersedia.

### Gate 5 — Release Readiness
- [ ] Pemeriksaan keamanan yang relevan lulus.
- [ ] Migrasi dan pemulihan ditinjau.
- [ ] Dokumentasi dan versi konsisten.
- [ ] Prosedur deployment dan rollback tersedia.
- [ ] Keputusan rilis tercatat.

Gate yang belum diperiksa harus tetap berstatus NOT TESTED atau BLOCKED sesuai kondisinya.

## 8. Bukti dan Penyimpanan

Bukti audit dapat berupa:
- Commit hash dan diff.
- Hasil pengujian.
- Potongan source code dengan path dan nomor baris.
- Struktur skema database yang telah disamarkan.
- Screenshot runtime yang tidak menampilkan rahasia.
- Log yang telah disamarkan.
- Langkah reproduksi dan hasil aktual.

Bukti harus cukup untuk ditinjau ulang, tetapi tidak boleh mengekspos kredensial atau data sensitif yang tidak diperlukan.

## 9. Aturan Sebelum Koreksi

Sebelum koreksi kode:
1. Selesaikan audit pada ruang lingkup terkait.
2. Bedakan fakta, dugaan, dan pertanyaan terbuka.
3. Tentukan koreksi paling kecil yang memenuhi kebutuhan.
4. Identifikasi risiko regresi.
5. Tentukan tes sebelum perubahan.
6. Minta persetujuan rencana.
7. Baru lakukan implementasi terkontrol.

Jika ditemukan masalah di luar cakupan, dokumentasikan sebagai temuan terpisah dan jangan otomatis memperluas perubahan.

## 10. Riwayat Perubahan

| Versi | Status | Perubahan |
|---|---|---|
| 0.1 | DRAFT | Kerangka audit dan readiness awal |

---
**Peringatan:** seluruh status audit dan gerbang harus ditetapkan berdasarkan bukti aktual, bukan keberadaan dokumen ini.
