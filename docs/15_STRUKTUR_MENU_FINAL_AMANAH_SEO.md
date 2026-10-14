# STRUKTUR MENU AMANAH SEO — NAVIGASI RINGKAS

**Status:** Implementasi usulan — menunggu verifikasi runtime WordPress  
**Tanggal:** 2026-10-14  
**Berlaku untuk:** satu plugin dan satu menu induk WordPress **AMANAH SEO**  
**Prinsip:** penyederhanaan tampilan tidak menghapus halaman, slug, callback, capability, atau fitur yang sudah terdaftar.

## 1. Struktur navigasi ringkas

Sidebar memakai tiga kategori yang dapat dibuka/tutup. Di dalamnya ada 12 kelompok fungsi:

```text
AMANAH SEO
├── CORE
│   ├── Dashboard
│   ├── Website & Workspace
│   ├── SEO & Opportunities
│   ├── Tasks & Workflow
│   └── Settings & Access
├── AI
│   ├── Entity & Authority
│   ├── Content Analysis
│   └── AI Content Creation
└── ENGINE
    ├── Keyword Intelligence
    ├── Strategy & Planning
    ├── Content Workflow
    └── SEO Monitoring
```

Angka 12 merujuk pada kelompok fungsi di sidebar, bukan jumlah seluruh halaman. Halaman yang telah terdaftar tetap menjadi tautan anak dalam kelompok yang sesuai.

## 2. Isi kelompok

### CORE
- **Dashboard:** ringkasan utama.
- **Website & Workspace:** identitas website dan workspace.
- **SEO & Opportunities:** temuan, diagnostik, pemeriksaan konten, peluang, dan informasi SEO terkait.
- **Tasks & Workflow:** pekerjaan, riwayat, persetujuan, dan catatan pelaksanaan yang telah tersedia.
- **Settings & Access:** pengaturan inti, API credentials, lisensi/entitlement modul, dan administrasi sistem yang telah tersedia.

### AI
- **Entity & Authority:** Entity Profile dan Authority & Author.
- **Content Analysis:** Content Analyzer, audit, dan halaman analisis/optimasi terkait.
- **AI Content Creation:** Article Generation dan alur pembuatan konten berbantuan AI yang sudah terdaftar.

### ENGINE
- **Keyword Intelligence:** riset keyword, search intent, clusters, URL mapping, dan content scanning.
- **Strategy & Planning:** requirements, strategy, content plan, brief, dan blueprint.
- **Content Workflow:** workspace produksi dan tahapan generation, evaluation, refinement, maturity, review, approval, serta persiapan publikasi.
- **SEO Monitoring:** performance, keyword monitoring, site health, internal links, orphan pages, cannibalization, dan isu SEO.

Pengelompokan halaman berdasarkan slug dan label menu merupakan aturan presentasi. Halaman yang belum memiliki implementasi tetap tidak boleh digambarkan sebagai fitur yang sudah berfungsi.

## 3. Aturan perilaku

1. Hanya satu kategori (CORE, AI, atau ENGINE) dibuka pada satu waktu.
2. Saat halaman tertentu dibuka, kategori dan kelompok yang menaunginya otomatis dibuka.
3. Pengguna dapat membuka/tutup kelompok untuk mengurangi kepadatan visual.
4. Tautan asli, URL, callback, dan capability tidak diganti oleh kontrol kategori/kelompok.
5. Halaman yang belum cocok dengan pola pengelompokan tetap dimasukkan ke kelompok aman dalam kategori terkait; tidak boleh dibuang atau dibuat tidak terjangkau.
6. Pengaturan provider/API credentials dan lisensi modul tetap dipusatkan di CORE. API key tidak sama dengan lisensi.
7. AI dan ENGINE tetap modul berbeda dengan entitlement independen.
8. Navigasi yang lebih ringkas tidak mengubah izin untuk menjalankan fitur, menyetujui, atau menerbitkan konten.
9. Implementasi tetap perlu diuji pada WordPress staging, terutama akses capability, penandaan halaman aktif, dan semua tautan anak.

## 4. Batas perubahan

Perubahan ini hanya menyederhanakan tampilan sidebar dan memperbarui dokumentasi. Tidak ada penghapusan halaman, migrasi database, perubahan lisensi, merge, atau deployment produksi yang menjadi bagian dari pekerjaan ini.
