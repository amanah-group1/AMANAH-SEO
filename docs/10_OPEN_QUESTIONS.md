# AMANAH SEO — Open Questions

**Status Dokumen:** APPROVED — daftar acuan pertanyaan disetujui; status setiap pertanyaan tetap OPEN sampai diputuskan  
**Tujuan:** Mencatat keputusan yang diperlukan sebelum arsitektur dan implementasi difinalisasi

## 1. Aturan Penggunaan Dokumen

- Pertanyaan di sini bukan keputusan final.
- Opsi yang disebutkan merupakan kandidat untuk dievaluasi.
- Keputusan hanya dianggap final setelah dicatat dan disetujui.
- Pertanyaan yang bergantung pada audit harus menunggu bukti audit.
- Setiap pertanyaan yang selesai harus diperbarui dengan keputusan, tanggal, dan referensi terkait.

## 2. Identitas dan Akun

### Q-001 — Model identitas
Bagaimana pengguna akan masuk ke platform induk?

Opsi untuk dievaluasi:
- Akun lokal platform.
- Penyedia identitas eksternal.
- Model hibrida.

**Status:** OPEN  
**Ketergantungan:** audit kebutuhan pengguna, keamanan, dan deployment.

### Q-002 — Hubungan akun dan organisasi
Apakah satu akun dapat menjadi anggota beberapa organisasi atau workspace?

**Status:** OPEN  
**Ketergantungan:** model data, isolasi data, dan kebutuhan bisnis.

### Q-003 — Role dan permission
Role apa yang dibutuhkan dan bagaimana permission ditetapkan?

**Status:** OPEN  
**Ketergantungan:** audit operasi administratif dan batas tanggung jawab produk.

## 3. Produk dan Integrasi

### Q-004 — Product registry
Metadata apa yang wajib dimiliki setiap produk yang terdaftar?

Kandidat:
- Identitas dan versi produk.
- Status produk.
- URL atau entry point.
- Kontrak integrasi.
- Kapabilitas yang dinyatakan.
- Persyaratan kompatibilitas.

**Status:** OPEN  
**Ketergantungan:** audit AMANAH SEO AI dan AMANAH SEO ENGINE V2.

### Q-005 — Model komunikasi antarproduk
Bagaimana platform induk akan berkomunikasi dengan produk?

Opsi untuk dievaluasi:
- HTTP API.
- Integrasi lokal melalui kontrak internal.
- Adapter khusus untuk tiap produk.
- Kombinasi beberapa mekanisme.

**Status:** OPEN  
**Ketergantungan:** arsitektur aktual, deployment, keamanan, dan biaya pemeliharaan.

### Q-006 — Kepemilikan data
Data apa yang dikelola platform induk dan data apa yang tetap dikelola setiap produk?

**Status:** OPEN  
**Ketergantungan:** audit database dan model integrasi.

### Q-007 — Navigasi produk
Apakah navigasi akan membuka halaman dalam aplikasi yang sama, menggunakan deep link, atau mengarahkan pengguna ke aplikasi terpisah?

**Status:** OPEN  
**Ketergantungan:** topologi deployment, session, dan kontrak integrasi.

## 4. License, Entitlement, dan Activation

### Q-008 — Model lisensi
Bagaimana lisensi akan diterbitkan, diperpanjang, ditangguhkan, dan dicabut?

**Status:** OPEN  
**Ketergantungan:** kebutuhan komersial dan model operasional.

### Q-009 — Paket komersial
Apakah paket seperti Personal, Business, atau Agency akan digunakan?

**Status:** OPEN  
**Catatan:** nama paket, harga, kuota, serta fitur setiap paket belum disetujui.

### Q-010 — Batas penggunaan
Apakah produk membutuhkan batas jumlah pengguna, situs, instalasi, atau penggunaan fitur?

**Status:** OPEN  
**Ketergantungan:** kebutuhan bisnis, audit produk, dan biaya layanan.

### Q-011 — Aktivasi
Bagaimana aktivasi instalasi atau lingkungan akan dicatat dan dibatasi?

**Status:** OPEN  
**Ketergantungan:** arsitektur deployment, model lisensi, dan persyaratan offline.

### Q-012 — Verifikasi entitlement
Apakah pemeriksaan entitlement dilakukan secara online, lokal, atau melalui mekanisme hibrida?

**Status:** OPEN  
**Ketergantungan:** keamanan, ketersediaan layanan, dan risiko penyalahgunaan.

### Q-013 — Kegagalan layanan lisensi
Bagaimana produk berperilaku jika layanan lisensi sementara tidak tersedia?

**Status:** OPEN  
**Ketergantungan:** model lisensi, kebutuhan operasional, dan keputusan keamanan.

## 5. Kredensial AI

### Q-014 — Kepemilikan API key
Apakah pengguna menyediakan API key sendiri, platform menyediakan kredensial bersama, atau keduanya didukung?

**Status:** OPEN  
**Ketergantungan:** audit AMANAH SEO AI, biaya, privasi, dan model komersial.

### Q-015 — Penyimpanan secret
Bagaimana API key dan kredensial integrasi disimpan, dienkripsi, diakses, serta dirotasi?

**Status:** OPEN  
**Ketergantungan:** deployment dan audit keamanan.

### Q-016 — Pembebanan biaya AI
Jika kredensial disediakan platform, bagaimana penggunaan dan biayanya dihitung atau dibatasi?

**Status:** OPEN  
**Ketergantungan:** model bisnis dan mekanisme pengukuran penggunaan.

## 6. Arsitektur Teknis

### Q-017 — Teknologi platform induk
Framework, bahasa, dan struktur aplikasi apa yang akan digunakan?

**Status:** OPEN  
**Ketergantungan:** kebutuhan produk, keahlian pemeliharaan, dan hasil audit.

### Q-018 — Topologi deployment
Apakah platform dan produk akan berjalan dalam satu lingkungan atau lingkungan terpisah?

**Status:** OPEN  
**Ketergantungan:** kebutuhan isolasi, keamanan, biaya, dan operasional.

### Q-019 — Database
Apakah platform induk memiliki database sendiri dan bagaimana batas data antarproduk diterapkan?

**Status:** OPEN  
**Ketergantungan:** audit skema, integrasi, dan strategi migrasi.

### Q-020 — API versioning
Bagaimana versi kontrak dan kompatibilitas antarkomponen akan dikelola?

**Status:** OPEN  
**Ketergantungan:** desain kontrak dan siklus rilis.

## 7. Keamanan dan Operasional

### Q-021 — Standar keamanan
Standar atau baseline keamanan apa yang wajib dipenuhi sebelum rilis?

**Status:** OPEN  
**Ketergantungan:** profil risiko dan persyaratan operasional.

### Q-022 — Audit event dan retensi
Peristiwa apa yang dicatat, siapa yang dapat melihatnya, dan berapa lama data disimpan?

**Status:** OPEN  
**Ketergantungan:** kebutuhan diagnosis, privasi, dan kapasitas penyimpanan.

### Q-023 — Backup dan pemulihan
Berapa target kehilangan data dan waktu pemulihan yang dapat diterima?

**Status:** OPEN  
**Ketergantungan:** kebutuhan bisnis, infrastruktur, dan biaya.

### Q-024 — Monitoring dan respons insiden
Siapa yang bertanggung jawab atas monitoring, eskalasi, dan penanganan insiden?

**Status:** OPEN  
**Ketergantungan:** model operasional dan kesiapan rilis.

## 8. Produk dan Prioritas Bisnis

### Q-025 — Urutan integrasi
Produk mana yang diprioritaskan untuk integrasi pertama?

**Status:** OPEN  
**Ketergantungan:** hasil audit, kebutuhan bisnis, dan kompleksitas teknis.

### Q-026 — Kriteria MVP
Kemampuan minimum apa yang wajib tersedia sebelum platform induk dianggap berguna?

**Status:** OPEN  
**Ketergantungan:** prioritas pengguna dan estimasi implementasi.

### Q-027 — Strategi rilis
Bagaimana versi beta, rilis stabil, dan kompatibilitas produk akan dikelola?

**Status:** OPEN  
**Ketergantungan:** proses pengujian, deployment, dan dukungan.

## 9. Format Penyelesaian Pertanyaan

Setiap pertanyaan yang diputuskan harus memiliki:
- ID pertanyaan.
- Keputusan yang disetujui.
- Alasan dan alternatif yang dipertimbangkan.
- Dampak teknis dan bisnis.
- Pihak yang menyetujui.
- Tanggal keputusan.
- Referensi Decision Register atau ADR.
- Dokumen lain yang perlu diperbarui.

Jika suatu pertanyaan ditunda, catat alasannya, dampaknya, dan kondisi yang diperlukan untuk membukanya kembali.

## 10. Riwayat Perubahan

| Versi | Status | Perubahan |
|---|---|---|
| 0.1 | OPEN | Daftar pertanyaan awal untuk keputusan arsitektur, produk, dan operasional |

---
**Peringatan:** semua pertanyaan berstatus OPEN sampai ada keputusan yang dapat ditelusuri dan disetujui.
