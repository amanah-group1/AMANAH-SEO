# Product Audit Checklist — [PRODUCT-NAME]

**Status:** NOT STARTED  
**Tanggal audit:** [YYYY-MM-DD]  
**Auditor:** [Name or Role]  
**Repository:** [Repository path or URL]  
**Branch:** [Branch name]  
**Commit:** [Exact commit hash]

## Petunjuk

Tandai setiap item berdasarkan bukti aktual. Gunakan status:

- PASS
- PARTIAL
- FAIL
- BLOCKED
- NOT TESTED
- NOT APPLICABLE

Status NOT APPLICABLE harus disertai alasan. Jangan menandai PASS hanya karena suatu file, halaman, atau konfigurasi ditemukan.

## 1. Identitas dan Baseline Repository

- [ ] Repository yang benar telah diverifikasi.
- [ ] Branch aktif telah dicatat.
- [ ] Commit awal telah dicatat.
- [ ] Status working tree telah diperiksa.
- [ ] Perubahan lokal dan file tidak terlacak telah dicatat.
- [ ] Dokumentasi master dan aturan proyek telah dibaca.
- [ ] Struktur direktori dan entry point telah dipetakan.
- [ ] Dependensi dan persyaratan runtime telah diidentifikasi.

**Bukti dan catatan:**

[Commit, perintah pemeriksaan, file, dan hasil aktual]

## 2. Arsitektur dan Source Code

- [ ] Modul dan tanggung jawabnya telah dipetakan.
- [ ] Alur request utama telah ditelusuri.
- [ ] Controller, service, model, dan persistence telah ditinjau jika relevan.
- [ ] Validasi input dan output telah diperiksa.
- [ ] Penanganan error telah ditinjau.
- [ ] Batas antarmodul telah diidentifikasi.
- [ ] Ketergantungan terhadap layanan eksternal telah dicatat.
- [ ] Temuan didukung referensi source code.

**Bukti dan catatan:**

[Path, simbol, nomor baris, dan commit]

## 3. Database dan Persistence

- [ ] Skema database yang diharapkan telah ditemukan.
- [ ] Skema aktual telah diperiksa jika akses tersedia.
- [ ] Instalasi, aktivasi, dan migrasi telah ditinjau.
- [ ] Operasi baca dan tulis telah ditelusuri.
- [ ] Constraint, indeks, dan relasi telah ditinjau jika relevan.
- [ ] Penanganan kegagalan persistence telah diperiksa.
- [ ] Backup dan pemulihan telah dievaluasi jika relevan.
- [ ] Perbedaan skema dan data dicatat tanpa mengekspos informasi sensitif.

**Bukti dan catatan:**

[Skema, migrasi, hasil query yang disamarkan, dan hasil tes]

## 4. Runtime dan Konfigurasi

- [ ] Lingkungan target telah diverifikasi.
- [ ] Versi source code yang terpasang telah dicatat.
- [ ] Versi runtime dan dependensi relevan telah dicatat.
- [ ] Konfigurasi yang memengaruhi fitur telah diperiksa.
- [ ] Status aktivasi produk telah diverifikasi jika relevan.
- [ ] Alur pengguna utama telah direproduksi.
- [ ] Hasil aktual dibandingkan dengan hasil yang diharapkan.
- [ ] Log dan error telah ditinjau secara aman.

**Bukti dan catatan:**

[Lingkungan, waktu pemeriksaan, langkah reproduksi, dan hasil]

## 5. Autentikasi dan Otorisasi

- [ ] Identitas pengguna atau sistem diverifikasi sesuai mekanisme produk.
- [ ] Capability, role, dan permission telah dipetakan.
- [ ] Operasi sensitif melakukan pemeriksaan akses di sisi server.
- [ ] Pengguna tanpa hak akses ditolak.
- [ ] Pemeriksaan akses tidak hanya bergantung pada penyembunyian UI.
- [ ] Perlindungan CSRF diperiksa untuk operasi yang relevan.
- [ ] Session dan pencabutan akses ditinjau jika relevan.

**Bukti dan catatan:**

[Skenario akses sah dan tidak sah serta hasil aktual]

## 6. Lisensi, Entitlement, dan Aktivasi

- [ ] Model lisensi aktual telah ditemukan atau ditandai belum tersedia.
- [ ] Pemeriksaan entitlement telah ditelusuri jika relevan.
- [ ] Aktivasi dan pencabutan telah diperiksa jika relevan.
- [ ] Fitur terbatas menolak akses tanpa entitlement yang valid.
- [ ] Kegagalan layanan lisensi tidak memberikan akses tanpa izin.
- [ ] Perbedaan license, entitlement, activation, authentication, dan authorization jelas.
- [ ] Batas penggunaan dan perilaku saat offline telah ditinjau jika relevan.

**Bukti dan catatan:**

[Kontrak, alur kode, konfigurasi, dan hasil tes]

## 7. Integrasi dan Layanan Eksternal

- [ ] Identitas pihak pemanggil telah ditentukan.
- [ ] Kontrak request dan response telah diperiksa.
- [ ] Validasi payload telah ditinjau.
- [ ] Penanganan timeout dan retry telah diperiksa.
- [ ] Perilaku saat layanan tidak tersedia telah diuji atau ditandai belum diuji.
- [ ] Versi kontrak dan kompatibilitas telah ditinjau.
- [ ] Data yang dikirim ke layanan eksternal telah diidentifikasi.
- [ ] Error eksternal tidak membocorkan secret atau informasi sensitif.

**Bukti dan catatan:**

[Kontrak, mock atau tes integrasi, serta hasil aktual]

## 8. AI Provider (Jika Berlaku)

- [ ] Pemilihan provider dan model telah ditelusuri.
- [ ] Kepemilikan API key telah diidentifikasi.
- [ ] Penyimpanan dan pengambilan kredensial telah diperiksa.
- [ ] Kegagalan autentikasi provider ditangani dengan aman.
- [ ] Respons AI divalidasi sebelum digunakan.
- [ ] Output AI yang tidak valid tidak merusak persistence atau tampilan.
- [ ] Log diagnostik tidak menampilkan API key.
- [ ] Timeout, retry, dan batas penggunaan telah ditinjau jika relevan.

**Bukti dan catatan:**

[Alur prompt hingga persistence atau hasil yang ditampilkan]

## 9. Keamanan dan Privasi

- [ ] Secret tidak disimpan di source code atau repository.
- [ ] Password dan token ditangani dengan mekanisme yang sesuai.
- [ ] Input dan output dilindungi dari penyalahgunaan yang relevan.
- [ ] Akses data dibatasi sesuai hak pengguna.
- [ ] Log menghindari secret dan data sensitif yang tidak diperlukan.
- [ ] Penanganan error tidak mengungkap detail internal yang tidak semestinya.
- [ ] Risiko dependency dan konfigurasi telah dipertimbangkan.
- [ ] Temuan keamanan memiliki severity dan bukti.

**Bukti dan catatan:**

[Kontrol yang diperiksa, skenario pengujian, dan hasil aktual]

## 10. Pengujian dan Regresi

- [ ] Tes yang sudah ada telah diidentifikasi.
- [ ] Tes yang relevan telah dijalankan.
- [ ] Hasil dan lingkungan pengujian telah dicatat.
- [ ] Skenario positif telah diperiksa.
- [ ] Skenario negatif telah diperiksa.
- [ ] Kegagalan layanan atau dependensi telah diperiksa jika relevan.
- [ ] Fitur yang sebelumnya terbukti PASS telah diuji ulang jika terdampak.
- [ ] Tes yang tidak dapat dijalankan memiliki alasan yang tercatat.

**Bukti dan catatan:**

[Perintah, hasil, jumlah tes, dan kegagalan]

## 11. Temuan dan Risiko

| ID | Temuan | Severity | Status | Referensi bukti |
|---|---|---|---|---|
| FINDING-001 | [Description] | [Severity] | [Status] | [Reference] |

Tambahkan baris sesuai kebutuhan.

## 12. Kesiapan Integrasi

- [ ] Tanggung jawab produk telah dipetakan.
- [ ] Kontrak integrasi sesuai kemampuan yang telah diverifikasi.
- [ ] Ketergantungan lintas produk telah dicatat.
- [ ] Risiko kompatibilitas telah ditinjau.
- [ ] Persyaratan autentikasi dan entitlement telah ditetapkan.
- [ ] Scope perubahan dan komponen yang dilarang diubah telah ditentukan.
- [ ] Kriteria penerimaan integrasi dapat diuji.

**Bukti dan catatan:**

[Dokumen kontrak, hasil audit, dan keputusan terkait]

## 13. Ringkasan Hasil Audit

- **Status keseluruhan:** [PASS / PARTIAL / FAIL / BLOCKED / NOT TESTED]
- **Temuan kritis terbuka:** [Count or unknown]
- **Temuan high terbuka:** [Count or unknown]
- **Pengujian wajib yang belum dilakukan:** [List]
- **Risiko tersisa:** [Summary]
- **Batas audit:** [What was not inspected]
- **Rekomendasi:** [Next action]

## 14. Persetujuan dan Penutupan

- **Auditor:** [Name or Role]
- **Reviewer:** [Name or Role]
- **Tanggal review:** [YYYY-MM-DD]
- **Keputusan:** [ACCEPT / REWORK / BLOCKED]
- **Referensi laporan audit:** [Reference]

---
**Aturan:** checklist ini adalah instrumen audit, bukan bukti bahwa produk telah lulus audit. Setiap status harus didukung bukti yang sesuai.
