# STRUKTUR MENU FINAL AMANAH SEO

**Status:** Implementasi pada PR — menunggu validasi otomatis dan uji runtime WordPress  
**Tanggal:** 2026-10-14  
**Berlaku untuk:** satu plugin WordPress bernama AMANAH SEO  
**Tujuan:** sidebar admin hanya memiliki tiga menu utama; masing-masing membuka halaman dashboard berbasis kartu.

## 1. Tiga menu utama

Urutan menu utama harus tepat seperti berikut:

1. **CORE**
2. **AMANAH SEO AI**
3. **AMANAH SEO ENGINE**

Menu induk lama **AMANAH SEO** tidak ditampilkan lagi sebagai menu utama. Dengan demikian, tidak ada menu utama keempat atau menu AI/ENGINE duplikat.

Setiap menu utama membuka halaman dashboard tersendiri. Dashboard menampilkan kartu responsif dengan kategori, judul, deskripsi singkat, status implementasi, dan tombol **Buka area**. Kartu tidak boleh menyatakan fitur telah tersedia jika integrasinya belum selesai.

## 2. Dashboard CORE

Halaman CORE menggunakan pola kartu yang sama seperti dashboard AMANAH SEO AI. Kartu yang direncanakan:

- **Website & Workspace** — identitas website dan konteks kerja utama. Mengarah ke halaman workspace yang sudah tersedia.
- **SEO Intelligence** — area intelijen dan peluang SEO; placeholder sampai implementasi diverifikasi.
- **Tasks & Workflow** — pekerjaan dan tindak lanjut yang tercatat.
- **Execution & Approval** — placeholder untuk pelaksanaan dan persetujuan.
- **Monitoring & Results** — placeholder untuk pemantauan dan hasil terverifikasi.
- **Reports** — placeholder laporan.
- **AI Provider & API Key** — area pengaturan provider/kredensial AI. Jangan mengklaim integrasi aktif sebelum implementasi diuji.
- **Settings & Access** — halaman pengaturan akses dan lisensi modul yang dikelola melalui CORE.

Lisensi AI dan ENGINE tetap terpisah satu sama lain, dan pengelolaannya dilakukan melalui AMANAH SEO. CORE sendiri tidak membutuhkan lisensi untuk dipasang.

## 3. Dashboard AMANAH SEO AI

Gunakan copywriting dan susunan kartu berikut. Status setiap kartu tetap **Belum diimplementasikan** sampai integrasi fiturnya benar-benar diverifikasi.

### Copywriting

**Jika AI belum mengenal bisnis Anda, bagaimana calon pelanggan akan menemukannya?**

Pencarian berkembang melampaui daftar tautan. Identitas bisnis yang jelas, otoritas yang dapat dipercaya, dan konten yang relevan membantu mesin pencari serta sistem AI memahami siapa Anda dan apa yang Anda kuasai.

**AMANAH SEO AI dirancang untuk mendukung fondasi tersebut sebagai bagian dari satu sistem SEO terpadu — bukan sekadar menghasilkan konten.**

**Kemampuan AI**

Pilih area kerja yang dibutuhkan. Halaman yang belum terintegrasi akan menampilkan status placeholder dengan jelas.

### Kartu dan tujuan

| Kelompok | Kartu | Deskripsi | Slug tujuan |
|---|---|---|---|
| Entity & Authority | Entity Profile | Identitas dan konteks utama bisnis. | `amanah-entity-profile` |
| Entity & Authority | Authority & Author | Kredibilitas penulis dan keahlian. | `amanah-authority` |
| Intelligence | AI Audit | Area audit SEO berbantuan AI. | `amanah-seo-ai-audit` |
| Content AI | Content Analyzer | Analisis kualitas dan konteks konten. | `amanah-content-analyzer` |
| Content AI | Content Brief | Kerangka konten sebelum penulisan. | `amanah-seo-ai-content-brief` |
| Content AI | Article Generation | Alur pembuatan artikel terarah. | `amanah-content-generation` |
| Content AI | FAQ Generator | Perencanaan pertanyaan dan jawaban. | `amanah-seo-ai-faq-generator` |
| Content AI | Content Optimizer | Penyempurnaan konten yang sudah ada. | `amanah-seo-ai-content-optimizer` |
| Authority | Topic Authority | Cakupan topik dan kedalaman keahlian. | `amanah-seo-ai-topic-authority` |
| Technical SEO | Internal Link AI | Dukungan perencanaan tautan internal. | `amanah-seo-ai-internal-links` |
| Technical SEO | Schema Manager | Akses ke generator dan validasi schema. | `amanah-seo-ai-schema-generator` plus tautan **Validasi** ke `amanah-seo-ai-schema-validation` |

Semua tautan harus tetap menuju slug yang sesuai. Halaman placeholder harus menyatakan bahwa integrasi belum tersedia dan tidak menjalankan proses AI tanpa implementasi yang nyata.

## 4. Dashboard AMANAH SEO ENGINE

Halaman ENGINE menggunakan pola kartu responsif yang sama seperti dashboard AI. Kartu tahap yang ditampilkan:

1. **Keyword & Search Intelligence** — riset keyword, search intent, dan pengelompokan peluang.
2. **Requirements** — kebutuhan dan batasan sebelum strategi disusun.
3. **Strategy & Planning** — rencana SEO dan prioritas kerja.
4. **Content Brief & Blueprint** — kerangka konten sebelum produksi.
5. **Generation & Evaluation** — tahap produksi dan evaluasi konten.
6. **Refinement & Maturity** — penyempurnaan dan kesiapan konten.
7. **Human Review & Approval** — pemeriksaan manusia sebelum publikasi.
8. **Publishing Preparation** — persiapan publikasi tanpa menerbitkan otomatis.

Setiap kartu menampilkan status **Belum diimplementasikan** dan tombol **Buka area** menuju placeholder yang jelas. Kartu atau tombol tidak boleh menyiratkan bahwa ENGINE sudah melakukan produksi atau publikasi.

## 5. Aturan implementasi

1. Hanya tiga menu utama yang terlihat: CORE, AMANAH SEO AI, dan AMANAH SEO ENGINE.
2. Ketiganya menggunakan capability `manage_amanah_seo`; pengubahan tampilan tidak boleh melemahkan pemeriksaan akses.
3. Dashboard tiap menu menampilkan kartu responsif, dengan gaya konsisten mengikuti dashboard AI.
4. Pertahankan slug halaman internal yang sudah digunakan oleh kartu AI agar tautan tidak rusak.
5. Placeholder harus jelas dan tidak membuat hasil, data, atau status palsu.
6. Pengaturan provider/API key dan lisensi modul dikelola dari CORE; API key tidak sama dengan lisensi.
7. Lisensi AI dan ENGINE independen; CORE tidak dibatasi lisensi.
8. Perubahan ini tidak menghapus data, menjalankan migrasi, mengaktifkan layanan eksternal, membuat konten, atau menerbitkan konten.
9. Validasi PHP/CI tidak menggantikan uji runtime WordPress. Sebelum dianggap selesai, periksa bahwa sidebar hanya memiliki tiga menu utama dan masing-masing menu membuka dashboard kartu yang benar.

## 6. Cakupan PR

Perubahan mencakup struktur menu utama dan dashboard kartu CORE, AI, dan ENGINE serta dokumentasi ini. Status tetap **menunggu validasi runtime WordPress** sampai halaman diuji pada staging.
