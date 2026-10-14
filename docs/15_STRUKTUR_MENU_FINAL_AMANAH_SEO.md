# STRUKTUR MENU FINAL AMANAH SEO

**Status:** FINAL — struktur menu ditetapkan 2026-10-11; aturan paket tunggal dan lisensi modul diperbarui 2026-10-12  
**Jenis dokumen:** Keputusan produk dan acuan navigasi  
**Acuan induk:** [Pedoman Induk AMANAH SEO](PEDOMAN_INDUK_AMANAH_SEO.md)  
**Batas perubahan:** Dokumentasi dan desain navigasi saja; bukan persetujuan implementasi.

## 1. Keputusan arsitektur navigasi

AMANAH SEO adalah produk utama, satu-satunya plugin yang dipasang pengguna, dan satu-satunya menu induk. Paket plugin yang sama memuat dua modul:

- **AMANAH SEO AI** — kemampuan SEO berbantuan AI.
- **AMANAH SEO ENGINE** — intelligence SEO dan workflow produksi konten.

Pengguna tidak perlu memasang plugin AI atau Engine secara terpisah. Kedua modul tetap mempertahankan tanggung jawab, alur, dan fungsi khususnya di dalam navigasi AMANAH SEO, serta memiliki lisensi/entitlement independen.

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
│   ├── AI Provider & API Credentials
│   ├── Module Licenses & Entitlements
│   ├── Updates
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
│   │   └── Konfigurasi AI
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
        └── System Status
```

## 3. Tanggung jawab tiap kelompok

### AMANAH SEO — pusat kendali utama

Menghubungkan kondisi website dengan temuan, prioritas, tugas, keputusan, hasil pelaksanaan, dan siklus monitoring berikutnya. Dashboard utama harus membantu pengguna memahami apa yang perlu diperhatikan, mengapa, statusnya, dan keputusan apa yang dibutuhkan.

### AMANAH SEO AI — modul kemampuan AI

Menampung fitur entity dan authority, audit AI, analisis serta pembuatan/optimasi konten, topic authority, internal linking berbantuan AI, schema, laporan, dan konfigurasi AI sesuai kemampuan yang benar-benar tersedia. Pengaturan provider/API credentials dan pengelolaan lisensi/entitlement berada di pusat pengaturan AMANAH SEO; halaman modul tidak menjadi sumber lisensi terpisah.

### AMANAH SEO ENGINE — modul intelligence dan workflow

Menampung riset keyword dan search intelligence, strategi/perencanaan, workspace produksi konten, generation, evaluation, refinement, maturity gate, human review, approval, persiapan publikasi, dukungan SEO, monitoring, penggunaan AI, dan administrasi ENGINE. Lisensi/entitlement Engine dikelola secara terpusat di AMANAH SEO dan tidak diaktifkan otomatis oleh lisensi AI.

## 4. Aturan implementasi navigasi

1. Seluruh kelompok berada di bawah satu menu induk **AMANAH SEO**; pengguna cukup memasang satu plugin.
2. Jangan membuat AMANAH SEO AI atau AMANAH SEO ENGINE menjadi plugin/menu induk terpisah.
3. Semua menu modul tetap terlihat dan dapat dibuka, baik lisensi aktif maupun belum aktif.
4. Jika lisensi/entitlement modul tidak aktif, halaman modul menampilkan sales page yang menjelaskan nama modul, manfaat, status lisensi, dan langkah mendapatkan/mengaktifkan lisensi. Jangan menampilkan halaman error generik untuk kondisi lisensi normal.
5. Lisensi AI dan Engine independen. Lisensi satu modul tidak memberi akses ke fitur modul lainnya.
6. Pengelolaan API key/kredensial AI, lisensi modul, entitlement, dan pembaruan dipusatkan pada AMANAH SEO. API key bukan lisensi dan tidak boleh dicampuradukkan dengan entitlement.
7. Fitur berlisensi harus ditolak di sisi server jika entitlement tidak aktif, termasuk pada aksi formulir, AJAX/REST API, proses latar belakang, dan jalur eksekusi terkait. Menyembunyikan tombol atau menu saja tidak cukup.
8. Pertahankan batas fungsi khusus AI/ENGINE; satu paket tidak berarti semua fungsi harus digabung menjadi satu mesin.
9. Menu yang belum diimplementasikan atau belum lulus pengujian tidak boleh ditampilkan seolah-olah sudah berfungsi. Catat statusnya sebagai belum tersedia, belum diuji, atau terhambat sesuai bukti.
10. Perubahan website dan publikasi memerlukan persetujuan eksplisit dari pengguna yang berwenang. Struktur menu tidak memberikan izin untuk otomatisasi atau publikasi mandiri.
11. Penamaan teknis, slug, capability, mekanisme lisensi, urutan registrasi menu, dan detail implementasi ditetapkan dalam rencana implementasi terpisah setelah audit kode dan persetujuan ruang lingkup.

## 5. Batas status keputusan

Struktur dan pengelompokan menu pada dokumen ini berstatus **FINAL** sebagai keputusan produk. Bagian 6 mencatat keputusan visual yang lebih baru dan persetujuan pengguna untuk mengimplementasikan perilaku sidebar tiga kategori. Persetujuan tersebut hanya mencakup navigasi; bukan bukti bahwa seluruh fitur tersedia, bukan persetujuan untuk merge/deploy, dan tidak menggantikan gerbang pengujian integrasi serta lisensi dalam Pedoman Induk AMANAH SEO.

---

## 6. Keputusan navigasi visual — 2026-10-14

**Status: DISETUJUI PENGGUNA — menggantikan tampilan pohon datar pada bagian 2 untuk perilaku sidebar.**

Di bawah satu menu induk WordPress **AMANAH SEO**, sidebar menampilkan tepat tiga kategori yang dapat dibuka/tutup:

```text
AMANAH SEO
├── ▼ CORE
│   ├── Dashboard
│   ├── Website & Workspace
│   ├── SEO Intelligence
│   ├── Priorities & Tasks
│   ├── Execution & Approval
│   ├── Monitoring & Results
│   ├── Reports
│   └── Settings & Access
├── ▶ AI
│   ├── Entity Profile
│   ├── Authority & Author
│   ├── Content Analyzer
│   ├── Article Generation
│   └── Pengaturan khusus AI (jika diperlukan)
└── ▶ ENGINE
    ├── ENGINE Workflow
    ├── Keyword Research
    ├── Strategy & Plan
    ├── Requirements
    ├── Brief & Blueprint
    ├── Generation & Evaluation
    ├── Refinement & Maturity
    └── Review & Publishing
```

Perilaku wajib:

1. Hanya satu kategori terbuka pada satu waktu; membuka kategori lain menutup kategori sebelumnya.
2. CORE terbuka sebagai pilihan awal bila tidak ada kategori halaman aktif yang dapat ditentukan.
3. Saat halaman AI atau ENGINE dibuka, kategorinya otomatis terbuka dan tautan halaman aktif tetap mengikuti status WordPress.
4. Memilih kategori hanya mengubah tampilan sidebar; kategori bukan pengganti halaman dan tidak mengubah URL, callback, capability, atau handler fitur yang sudah terdaftar.
5. Semua submenu yang telah ada harus tetap dapat dijangkau. Submenu lama yang tidak muncul pada ringkasan visual tetap dipertahankan dalam kategori yang sesuai; penyederhanaan navigasi tidak boleh menghapus fitur.
6. Pengaturan inti, API credentials, hak akses, dan lisensi/entitlement modul dikelola dari CORE. Pengaturan khusus AI hanya ditampilkan bila memang memiliki fungsi khusus yang terpisah.
7. Tetap satu plugin WordPress dan satu menu induk AMANAH SEO; CORE, AI, dan ENGINE bukan menu induk WordPress terpisah dan bukan plugin terpisah.

