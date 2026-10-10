> **STATUS: DOKUMEN LAMA / TIDAK BERLAKU SEBAGAI PEDOMAN AKTIF.** Mulai 2026-10-10, visi, tujuan, batas produk, dan alur utama ditentukan oleh [Pedoman Induk AMANAH SEO](PEDOMAN_INDUK_AMANAH_SEO.md). Dokumen ini dipertahankan untuk penelusuran historis dan tidak boleh dipakai untuk mengesampingkan pedoman induk baru. Jika ada isi yang masih berguna, adopsi kembali secara eksplisit setelah ditinjau; status keputusan lama tidak otomatis menjadi keputusan aktif.

---

﻿# AMANAH SEO — Document Governance

**Status:** APPROVED — aturan tata kelola dokumentasi disetujui  
**Versi aktif:** 0.1 (status lifecycle saat ini: APPROVED)  
**Bukti persetujuan:** commit [`76cc6ab16b99a69148ced4a21a23cc6964669ead`](https://github.com/amanahgroup123/AMANAH-SEO/commit/76cc6ab16b99a69148ced4a21a23cc6964669ead), “docs: approve AMANAH SEO foundation guidance” (2026-10-09).  
**Catatan riwayat:** baris 0.1 berstatus DRAFT pada §14 merekam status awal saat penyusunan; tidak menggantikan status lifecycle saat ini.
**Cakupan:** Dokumentasi, keputusan arsitektur, audit, dan perubahan proyek AMANAH SEO

## 1. Tujuan

Dokumen ini menetapkan aturan pengelolaan dokumentasi agar keputusan, persyaratan, audit, dan rencana implementasi dapat ditelusuri serta tidak menimbulkan klaim yang tidak didukung bukti.

Dokumen ini tidak memberikan izin otomatis untuk mengubah kode, database, konfigurasi, atau runtime produk.

## 2. Prinsip Tata Kelola

1. Dokumentasi harus mencerminkan keputusan yang benar-benar disetujui.
2. Usulan tidak boleh ditulis sebagai keputusan final.
3. Hasil audit harus berdasarkan bukti yang dapat diperiksa.
4. Perubahan harus memiliki alasan, ruang lingkup, dan kriteria penerimaan.
5. Dokumen produk yang terpisah tidak boleh digabungkan tanpa alasan dan persetujuan yang jelas.
6. Perubahan harus dapat ditelusuri ke keputusan, persyaratan, implementasi, dan pengujian terkait.
7. Jika terdapat konflik dokumen, konflik harus dicatat dan diselesaikan secara eksplisit.

## 3. Status Dokumen

Gunakan status dokumen berikut secara konsisten di seluruh repository:

- **DRAFT:** masih disusun atau belum disetujui.
- **IN REVIEW:** sedang ditinjau oleh pihak yang berwenang.
- **APPROVED:** telah mendapat persetujuan yang tercatat.
- **SUPERSEDED:** digantikan oleh versi atau dokumen lain.
- **ARCHIVED:** disimpan sebagai rekaman historis.

Jangan gunakan status keputusan (`CONFIRMED`, `PROPOSED`, `OPEN`, `ASSUMPTION`, `SUPERSEDED`) atau status audit (`PASS`, `PARTIAL`, `BLOCKED`, `FAIL`, `NOT TESTED`, `NOT APPLICABLE`) sebagai status dokumen. `VERIFIED` adalah keterangan untuk klaim/evidence yang diverifikasi, bukan status dokumen.

Status APPROVED tidak berarti implementasi telah selesai atau lulus pengujian.

## 4. Jenis Dokumen

Dokumen proyek dapat mencakup:

- Project Charter.
- Decision Register.
- Architecture Blueprint.
- Product Boundaries.
- Integration Contracts.
- License and Entitlements.
- Security and Secrets.
- Document Governance.
- Audit and Readiness.
- Roadmap and Gates.
- Open Questions.
- Architecture Decision Records (ADR).
- Audit Findings.
- Implementation Plans.
- Test Evidence dan Release Records.

Daftar ini dapat berkembang melalui proses perubahan yang terkendali.

## 5. Source of Truth

Aturan source of truth yang disetujui untuk tata kelola dokumentasi:

- Repository `AMANAH-SEO` menjadi sumber utama dokumentasi platform induk.
- Repository `AMANAH-SEO-AI` tetap menjadi sumber kode dan dokumentasi proyek AMANAH SEO AI.
- Repository `amanah-seo-engine-v2` tetap menjadi sumber kode dan dokumentasi AMANAH SEO ENGINE V2.
- Bukti runtime harus menyebut lingkungan, waktu pemeriksaan, versi kode, dan langkah reproduksi yang relevan.
- Catatan percakapan atau ingatan tidak menggantikan bukti kode dan pengujian untuk klaim teknis.

Aturan ini menetapkan lokasi acuan dokumentasi dan bukti; aturan ini tidak menyatakan bahwa isi produk atau implementasinya telah diaudit.

## 6. Decision Register dan ADR

Decision Register mencatat keputusan penting beserta statusnya.

Gunakan ADR untuk keputusan arsitektur yang berdampak besar atau sulit dibalik. Setiap ADR idealnya memuat:

- ID dan judul.
- Status.
- Konteks dan masalah.
- Opsi yang dipertimbangkan.
- Keputusan.
- Konsekuensi dan risiko.
- Alternatif yang ditolak.
- Pihak yang menyetujui.
- Tanggal keputusan.
- Dokumen dan implementasi yang terdampak.

Keputusan yang belum disetujui harus tetap ditandai sebagai usulan.

## 7. Change Control

Sebelum perubahan substantif:

1. Identifikasi masalah atau kebutuhan.
2. Periksa dokumen yang berlaku.
3. Tentukan ruang lingkup dan komponen yang terdampak.
4. Catat kondisi awal dan bukti.
5. Siapkan rencana perubahan.
6. Tentukan kriteria penerimaan dan risiko.
7. Dapatkan persetujuan sesuai kewenangan.
8. Implementasikan dalam perubahan yang dapat ditinjau.
9. Jalankan pengujian yang relevan.
10. Catat hasil, commit, dan status akhir.

Perubahan di luar ruang lingkup yang disetujui harus dihentikan sampai mendapatkan persetujuan yang sesuai.

## 8. Aturan Audit

Setiap klaim audit harus mengidentifikasi:

- Objek yang diperiksa.
- Versi atau commit yang diperiksa.
- Lingkungan dan konfigurasi yang relevan.
- Langkah audit.
- Bukti yang dikumpulkan.
- Temuan dan dampak.
- Status serta keterbatasan pengujian.
- Rekomendasi atau tindakan lanjutan.

Jangan menyatakan audit menyeluruh apabila hanya sebagian komponen yang diperiksa.

## 9. Pengelolaan Versi

- Gunakan Git untuk merekam perubahan dokumen.
- Perubahan substantif harus memiliki pesan commit yang menjelaskan maksudnya.
- Jangan menghapus riwayat keputusan hanya untuk membuat dokumen tampak konsisten.
- Dokumen yang digantikan harus ditandai SUPERSEDED dan merujuk ke penggantinya jika diketahui.
- Hindari duplikasi sumber keputusan yang dapat menimbulkan konflik.
- Perubahan versi dokumen tidak otomatis berarti versi produk atau API ikut berubah.

## 10. Batas Repository

Repository dokumentasi platform induk tidak boleh menjadi alasan untuk:

- Menyalin seluruh source code produk tanpa kebutuhan yang disetujui.
- Mengubah branch, kode, database, atau runtime produk yang sudah ada.
- Mengganti kontrak produk berdasarkan asumsi yang belum diaudit.
- Mengklaim fitur produk tersedia tanpa verifikasi.
- Mengubah kredensial atau konfigurasi deployment tanpa prosedur yang disetujui.

Integrasi baru harus didahului audit dan kontrak yang disepakati.

## 11. Persetujuan

Persetujuan harus dapat ditelusuri, misalnya melalui catatan keputusan, komentar review, issue, atau pull request.

Catatan persetujuan idealnya mencantumkan:
- Pihak yang menyetujui.
- Ruang lingkup yang disetujui.
- Versi dokumen atau commit yang diperiksa.
- Batasan dan syarat persetujuan.
- Tanggal persetujuan.

Persetujuan terhadap dokumentasi tidak otomatis menjadi persetujuan terhadap implementasi.

## 12. Definition of Done Dokumentasi

Dokumen dapat dianggap selesai untuk tahapnya jika:

- [ ] Tujuan dan cakupan jelas.
- [ ] Status dokumen tercantum.
- [ ] Istilah konsisten.
- [ ] Keputusan final dibedakan dari usulan.
- [ ] Referensi silang relevan dan valid.
- [ ] Risiko serta pertanyaan terbuka dicatat.
- [ ] Tidak ada klaim audit atau implementasi tanpa bukti.
- [ ] Perubahan dapat ditinjau melalui Git.
- [ ] Persetujuan dicatat jika diperlukan.

## 13. Penanganan Konflik

Jika dua dokumen bertentangan:

1. Catat konflik dan bagian yang terpengaruh.
2. Jangan memilih salah satu berdasarkan dugaan.
3. Periksa sumber keputusan dan bukti yang tersedia.
4. Minta keputusan dari pihak yang berwenang bila konflik belum terselesaikan.
5. Perbarui dokumen terkait secara konsisten.
6. Simpan jejak perubahan.

## 14. Riwayat Perubahan

| Versi | Status | Perubahan |
|---|---|---|
| 0.1 | DRAFT | Tata kelola dokumentasi awal |

---
**Batas persetujuan:** tata kelola dokumentasi dalam dokumen ini telah disetujui. Persetujuan ini tidak mengesahkan usulan arsitektur, keputusan komersial yang masih terbuka, rencana implementasi, atau kesiapan produk.
