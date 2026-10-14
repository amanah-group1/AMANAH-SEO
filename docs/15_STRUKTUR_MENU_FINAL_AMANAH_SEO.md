# STRUKTUR MENU FINAL AMANAH SEO

**Status:** Implementasi pada branch — menunggu pemeriksaan otomatis dan uji runtime WordPress
**Tanggal:** 2026-10-14
**Berlaku untuk:** satu plugin WordPress bernama AMANAH SEO

## 1. Struktur sidebar final

Hanya ada satu menu induk untuk seluruh area AMANAH SEO. Ketika pengguna mengeklik menu induk, halaman yang terbuka adalah Sales Page.

Urutan menu yang terlihat:

1. **🚀 AMANAH SEO** — membuka Sales Page; tidak memiliki submenu Sales Page.
2. **⚙️ Settings & Access** — AI provider, model, API key, serta pengelolaan lisensi modul AI dan ENGINE.
3. **🧩 CORE** — membuka dashboard CORE berbasis kartu.
4. **🤖 AMANAH SEO AI** — membuka dashboard AI berbasis kartu.
5. **🔍 AMANAH SEO ENGINE** — membuka dashboard ENGINE berbasis kartu.

Empat item di bawah AMANAH SEO merupakan submenu dari menu induk tersebut. Sales Page bukan submenu dan tidak ditampilkan sebagai item terpisah.

Menu bawaan WordPress seperti Dashboard, Posts, Media, Pages, Comments, Appearance, Plugins, Users, Tools, dan Settings tetap tersedia.

## 2. Dashboard CORE

CORE dibuka melalui submenu **🧩 CORE** dan menampilkan kartu responsif, termasuk kartu **Dashboard**. Dashboard bukan menu sidebar tersendiri.

Kartu yang ditampilkan:

- **Dashboard** — ringkasan temuan, pekerjaan, dan tindakan berikutnya; mengarah ke halaman ringkasan yang sudah ada.
- **Website & Workspace** — identitas website dan konteks kerja utama.
- **SEO Intelligence** — placeholder sampai implementasi diverifikasi.
- **Tasks & Workflow** — pekerjaan dan tindak lanjut yang tercatat.
- **Execution & Approval** — placeholder untuk pelaksanaan dan persetujuan.
- **Monitoring & Results** — placeholder untuk pemantauan dan hasil terverifikasi.
- **Reports** — placeholder laporan.

Pengaturan AI provider/model, API key, dan lisensi diakses melalui submenu **⚙️ Settings & Access**, bukan melalui kartu atau submenu ganda di CORE.

Lisensi AI dan ENGINE tetap terpisah satu sama lain dan dikelola melalui AMANAH SEO. CORE sendiri tidak membutuhkan lisensi untuk dipasang.

## 3. Dashboard AMANAH SEO AI

Dashboard AI menggunakan copywriting dan kartu berikut. Status fitur tetap **Belum diimplementasikan** sampai integrasi benar-benar diverifikasi.

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
| Technical SEO | Schema Manager | Akses ke generator dan validasi schema. | `amanah-seo-ai-schema-generator` plus tautan Validasi ke `amanah-seo-ai-schema-validation` |

Semua tautan harus tetap menuju slug yang sesuai. Placeholder harus menyatakan bahwa integrasi belum tersedia dan tidak menjalankan proses AI tanpa implementasi nyata.

## 4. Dashboard AMANAH SEO ENGINE

Dashboard ENGINE menggunakan kartu responsif dengan status **Belum diimplementasikan** sampai integrasi diverifikasi:

1. **Keyword & Search Intelligence** — riset keyword, search intent, dan pengelompokan peluang.
2. **Requirements** — kebutuhan dan batasan sebelum strategi disusun.
3. **Strategy & Planning** — rencana SEO dan prioritas kerja.
4. **Content Brief & Blueprint** — kerangka konten sebelum produksi.
5. **Generation & Evaluation** — tahap produksi dan evaluasi konten.
6. **Refinement & Maturity** — penyempurnaan dan kesiapan konten.
7. **Human Review & Approval** — pemeriksaan manusia sebelum publikasi.
8. **Publishing Preparation** — persiapan publikasi tanpa menerbitkan otomatis.

Kartu tidak boleh menyiratkan bahwa ENGINE sudah melakukan produksi atau publikasi.

## 5. Aturan implementasi

1. Hanya satu menu induk AMANAH SEO yang menaungi Settings & Access, CORE, AI, dan ENGINE.
2. Mengeklik **🚀 AMANAH SEO** membuka Sales Page secara langsung.
3. Tidak ada submenu Sales Page.
4. Tidak ada menu CORE, AI, ENGINE, Article Generation, atau fitur AI internal sebagai menu sidebar terpisah.
5. Dashboard berada sebagai kartu di halaman CORE, bukan menu sidebar tersendiri.
6. Submenu fitur internal tidak ditampilkan satu per satu; halaman fitur tetap terdaftar dan dapat diakses dari kartu dashboard yang sesuai.
7. Gunakan capability `manage_amanah_seo`; perubahan navigasi tidak boleh melemahkan pemeriksaan akses.
8. Pertahankan slug halaman yang digunakan oleh kartu agar tautan tidak rusak.
9. Placeholder harus jelas dan tidak membuat hasil, data, atau status palsu.
10. API key tidak sama dengan lisensi. Pengelolaan lisensi AI dan ENGINE dilakukan dari Settings & Access; lisensi keduanya independen, sedangkan CORE tidak dibatasi lisensi.
11. Perubahan navigasi tidak menghapus data, menjalankan migrasi, mengaktifkan layanan eksternal, membuat konten, atau menerbitkan konten.
12. Validasi PHP/CI tidak menggantikan uji runtime WordPress. Sebelum dinyatakan selesai, verifikasi bahwa Sales Page terbuka dari menu induk, urutan submenu benar, kartu Dashboard tersedia di CORE, dan tidak ada menu ganda.

## 6. Status validasi

Kode navigasi dan dokumentasi telah diperbarui pada branch implementasi. Status belum boleh disebut selesai sampai pemeriksaan otomatis dan uji runtime WordPress staging memberikan hasil yang sesuai.
