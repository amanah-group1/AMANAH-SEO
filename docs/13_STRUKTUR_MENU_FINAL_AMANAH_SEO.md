# STRUKTUR MENU FINAL AMANAH SEO

**Status:** FINAL — ditetapkan oleh pemilik produk pada 2026-10-11  
**Jenis dokumen:** Keputusan produk dan acuan navigasi  
**Acuan induk:** [Pedoman Induk AMANAH SEO](PEDOMAN_INDUK_AMANAH_SEO.md)  
**Batas perubahan:** Dokumentasi dan desain navigasi saja; bukan persetujuan implementasi.

## 1. Keputusan arsitektur navigasi

AMANAH SEO adalah produk utama dan satu-satunya menu induk. Dua kelompok modul berada di bawahnya:

- **AMANAH SEO AI** — kemampuan SEO berbantuan AI.
- **AMANAH SEO ENGINE** — intelligence SEO dan workflow produksi konten.

AMANAH SEO AI dan AMANAH SEO ENGINE tidak ditampilkan sebagai menu produk induk yang sejajar dengan AMANAH SEO. Masing-masing tetap mempertahankan tanggung jawab, alur, dan fungsi khususnya sebagai kelompok modul di dalam navigasi AMANAH SEO.

## 2. Pohon menu final

```text
AMANAH SEO
├── 1. Dashboard
├── 2. Website & Workspace
├── 3. SEO Intelligence
│   ├── Ringkasan Kondisi SEO
│   ├── Hasil Pemeriksaan Website
│   ├── Temuan dan Masalah SEO
│   ├── Peluang Pertumbuhan
│   └── Sumber Data dan Tingkat Keyakinan
├── 4. Prioritas & Tasks
│   ├── Daftar Prioritas SEO
│   ├── Daftar Tugas
│   ├── Penanggung Jawab dan Status
│   └── Tenggat dan Dependensi
├── 5. Execution & Approval
│   ├── Rencana Pelaksanaan
│   ├── Antrean Persetujuan
│   ├── Riwayat Keputusan
│   └── Catatan Perubahan dan Hasil Eksekusi
├── 6. Monitoring & Results
│   ├── Status Pekerjaan
│   ├── Perubahan Kondisi SEO
│   ├── Hasil Sebelum dan Sesudah
│   └── Siklus Evaluasi Berikutnya
├── 7. Reports
├── 8. Settings & Access
│   ├── Pengaturan Workspace
│   ├── Pengguna dan Hak Akses
│   ├── Integrasi dan Sumber Data
│   └── Preferensi Sistem
├── 9. AMANAH SEO AI
│   ├── Dashboard AI
│   ├── Entity & Authority
│   │   ├── Entity Profile
│   │   └── Authority & Author
│   ├── AI Audit
│   ├── Content AI
│   │   ├── Content Analyzer
│   │   ├── Content Brief
│   │   ├── Article Generation
│   │   ├── FAQ Generator
│   │   └── Content Optimizer
│   ├── Topic Authority
│   ├── Internal Link AI
│   ├── Schema Manager
│   │   ├── Schema Generator
│   │   └── Schema Validation
│   ├── AI Reports
│   ├── AI Settings
│   │   ├── AI Provider & Models
│   │   ├── API Credentials
│   │   └── Konfigurasi AI
│   └── License & Entitlement AI
└── 10. AMANAH SEO ENGINE
    ├── Dashboard ENGINE
    ├── Content Production
    │   ├── Article Workspace
    │   ├── Create New Article
    │   ├── All Articles
    │   └── Published Content
    ├── Keyword & Search Intelligence
    │   ├── Keyword Research
    │   ├── Search Intent
    │   ├── Content Scanning
    │   ├── Keyword Clusters
    │   └── URL Mapping
    ├── Strategy & Planning
    │   ├── Requirements
    │   ├── Content Strategy
    │   ├── Content Plan
    │   ├── Content Brief
    │   └── Content Blueprint
    ├── Content Workflow
    │   ├── Generation
    │   ├── Evaluation
    │   ├── Refinement
    │   ├── Maturity Gate
    │   ├── Human Review
    │   ├── Approval
    │   └── Publishing Preparation
    ├── Quality & Maturity
    │   ├── Evaluation Results
    │   ├── Deficiencies & Refinement
    │   ├── Maturity Status
    │   └── Review & Approval Queue
    ├── SEO Support
    │   ├── Internal Links
    │   ├── Orphan Pages
    │   ├── SEO Issues
    │   ├── Cannibalization
    │   └── Recommendations
    ├── Monitoring
    │   ├── Performance
    │   ├── Keyword Monitoring
    │   └── Site Health
    ├── AI & Usage
    │   ├── AI Configuration
    │   └── Usage & Limits
    └── Administration
        ├── Settings
        ├── Import / Export
        ├── System Status
        └── License & Updates
```

## 3. Tanggung jawab tiap kelompok

### AMANAH SEO — pusat kendali utama

Menghubungkan kondisi website dengan temuan, prioritas, tugas, keputusan, hasil pelaksanaan, dan siklus monitoring berikutnya. Dashboard utama harus membantu pengguna memahami apa yang perlu diperhatikan, mengapa, statusnya, dan keputusan apa yang dibutuhkan.

### AMANAH SEO AI — kemampuan AI

Menampung fitur entity dan authority, audit AI, analisis serta pembuatan/optimasi konten, topic authority, internal linking berbantuan AI, schema, laporan, konfigurasi provider/kredensial, serta entitlement AI sesuai kemampuan yang benar-benar tersedia.

### AMANAH SEO ENGINE — intelligence dan workflow

Menampung riset keyword dan search intelligence, strategi/perencanaan, workspace produksi konten, generation, evaluation, refinement, maturity gate, human review, approval, persiapan publikasi, dukungan SEO, monitoring, penggunaan AI, dan administrasi ENGINE.

## 4. Aturan implementasi navigasi

1. Seluruh kelompok berada di bawah satu menu induk **AMANAH SEO**.
2. Jangan membuat AMANAH SEO AI atau AMANAH SEO ENGINE menjadi menu induk sejajar yang terpisah.
3. Pertahankan nama dan fungsi khusus AI/ENGINE dalam kelompok masing-masing; hindari memindahkan tanggung jawab mesin khusus ke modul inti hanya demi menyamakan menu.
4. Menu yang belum diimplementasikan atau belum lulus pengujian tidak boleh ditampilkan seolah-olah sudah berfungsi. Catat statusnya sebagai belum tersedia, belum diuji, atau terhambat sesuai bukti.
5. Kemampuan integrasi AI/ENGINE bersifat opsional untuk MVP AMANAH SEO. Jangan mengasumsikan koneksi, sinkronisasi, atau pertukaran data telah berjalan tanpa kontrak dan bukti uji.
6. Perubahan website dan publikasi memerlukan persetujuan eksplisit dari pengguna yang berwenang. Struktur menu tidak memberikan izin untuk otomatisasi atau publikasi mandiri.
7. Hak akses harus diterapkan pada sisi server sesuai fungsi; menyembunyikan menu saja bukan kontrol otorisasi yang memadai.
8. Penamaan teknis, slug, capability, urutan registrasi menu, dan detail implementasi ditetapkan dalam rencana implementasi terpisah setelah audit kode dan persetujuan ruang lingkup.

## 5. Batas status keputusan

Struktur dan pengelompokan menu pada dokumen ini berstatus **FINAL** sebagai keputusan produk. Dokumen ini tidak menyatakan bahwa seluruh fitur telah tersedia, tidak mengubah kode, tidak menyetujui integrasi, dan tidak memberikan persetujuan untuk mulai implementasi. Pekerjaan implementasi tetap mengikuti gerbang dan persetujuan yang ditetapkan dalam Pedoman Induk AMANAH SEO.
