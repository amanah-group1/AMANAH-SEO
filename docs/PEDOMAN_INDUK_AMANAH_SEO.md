# PEDOMAN INDUK AMANAH SEO
**Versi:** 1.0-candidate  
**Tanggal penyusunan:** 2026-10-10  
**Tanggal konsolidasi keputusan:** 2026-10-11  
**Status:** PEDOMAN KONSEPTUAL SELESAI — pemilik memerintahkan penuntasan pada 2026-10-11; implementasi tetap memerlukan persetujuan terpisah  
**Pemilik keputusan:** Pemilik produk AMANAH SEO

> Dokumen ini adalah titik awal baru untuk konsep dan arah produk AMANAH SEO. Dokumen lama di repository tidak boleh mengubah atau menimpa konsep dalam dokumen ini. Catatan audit lama hanya dipakai sebagai rekam jejak/evidence, bukan sebagai sumber visi produk. AMANAH SEO AI dan AMANAH SEO ENGINE V2 hanya ditinjau baca-saja selama penyusunan pedoman ini.

## 1. Tujuan produk

**AMANAH SEO adalah sistem utama untuk membantu pemilik website mengelola, memelihara, dan meningkatkan kualitas SEO website secara terarah, terukur, dan berkelanjutan.**

AMANAH SEO bukan sekadar pembuat artikel, bukan sekadar kumpulan alat SEO, dan bukan pengganti keputusan pemilik website. Produk ini membantu pemilik memahami kondisi website, menentukan pekerjaan yang paling penting, mengatur pelaksanaan, meminta persetujuan pada titik yang tepat, melihat hasil, lalu menggunakan hasil itu untuk menentukan langkah berikutnya.

Keberhasilan AMANAH SEO dinilai dari kemampuan membantu website dikelola dengan lebih jelas, pekerjaan SEO diprioritaskan dengan alasan yang dapat dipahami, mutu pekerjaan dikendalikan, keputusan tetap berada di tangan pemilik, dan perkembangan dapat ditinjau dari waktu ke waktu. **Tidak ada jaminan peringkat Google atau hasil bisnis tertentu.**

## 2. Masalah yang diselesaikan

Pemilik website sering menghadapi data SEO, ide konten, audit, alat, dan pekerjaan yang terpisah-pisah. Mereka perlu mengetahui:
1. Bagaimana kondisi website saat ini?
2. Masalah, peluang, dan risiko mana yang paling penting?
3. Apa yang sebaiknya dikerjakan berikutnya, dan mengapa?
4. Bagaimana pekerjaan tersebut direncanakan, dikerjakan, diperiksa, dan disetujui?
5. Apa hasilnya setelah pekerjaan dilakukan?
6. Apa tindakan berikutnya berdasarkan hasil tersebut?

AMANAH SEO menyatukan alur pengambilan keputusan dan pengelolaan pekerjaan itu. Integrasi teknis dengan produk lain dilakukan hanya bila tersedia dan telah disepakati; konsep produk tidak boleh bergantung pada fitur integrasi yang belum terbukti.

## 3. Prinsip produk yang tidak boleh dilanggar

1. **Website adalah pusat pengelolaan.** Semua pekerjaan dikaitkan dengan website, tujuannya, kondisinya, prioritasnya, dan hasilnya.
2. **Mulai dari kondisi dan tujuan.** Jangan membuat pekerjaan hanya karena alat atau AI dapat melakukannya.
3. **Prioritas harus beralasan.** Tampilkan masalah/peluang, dampak yang diperkirakan, urgensi, bukti, ketidakpastian, dan alasan urutan kerja.
4. **Manusia tetap memegang kendali.** Pemilik atau pengguna yang berwenang mengendalikan persetujuan, penerbitan, perubahan berisiko, serta tindakan yang berdampak pada website.
5. **Tidak mengarang hasil.** Pisahkan fakta terukur, hasil analisis, perkiraan, rekomendasi, dan hal yang belum diketahui.
6. **Tidak menjanjikan ranking.** Sistem membantu kualitas dan proses SEO; mesin pencari dan hasil bisnis tidak sepenuhnya dapat dikendalikan.
7. **Satu produk utama, satu plugin WordPress.** Pengguna memasang dan mengaktifkan plugin AMANAH SEO saja. AMANAH SEO AI dan AMANAH SEO ENGINE V2 menjadi modul internal dalam paket plugin yang sama, bukan plugin terpisah yang wajib dipasang pengguna.
8. **Modul tetap memiliki batas tanggung jawab.** Core AMANAH SEO mengelola konteks website, temuan, prioritas, tugas, persetujuan, dan pemantauan. Modul AI mempertahankan tanggung jawab AI/SEO khusus; modul Engine mempertahankan workflow SEO/konten terstruktur. Modul tidak boleh diduplikasi atau saling mengambil alih tanpa kontrak internal yang jelas.
9. **Migrasi bertahap dan kompatibel.** Repository AI dan Engine menjadi baseline sumber selama migrasi. Kode tidak boleh dipindahkan secara massal sebelum dependensi, kepemilikan data, lisensi, pengaturan, hook, namespace, aktivasi, dan risiko benturan diaudit. Repository asal tidak dihapus atau diubah secara destruktif sebagai bagian dari migrasi awal.
9. **Bukti sebelum klaim.** Status fitur, keamanan, pengujian, dan kesiapan harus didukung bukti.
10. **Perubahan terkendali.** Tidak ada implementasi sebelum ruang lingkup dan kriteria penerimaan disepakati.
11. **Sederhana dan dapat dipahami.** Dashboard dan pedoman tidak boleh memaksa pemilik mengikuti proses dokumentasi yang berputar-putar tanpa menghasilkan keputusan atau produk yang lebih jelas.
12. **Aman secara default.** Akses mengikuti kewenangan; rahasia dan data pengguna dilindungi; tindakan penting dicatat; kegagalan dijelaskan tanpa membocorkan rahasia.

## 4. Arsitektur produk dan modul

### 4.1 AMANAH SEO — plugin induk dan Core

AMANAH SEO adalah satu-satunya plugin WordPress yang dipasang pengguna. Core bertanggung jawab atas:
- Bootstrap plugin, lifecycle aktivasi/nonaktifkan, pemeriksaan kompatibilitas, dan pemuatan modul.
- Dashboard dan navigasi utama.
- Konteks serta pengelolaan website.
- Temuan, prioritas, tugas, keputusan/persetujuan, riwayat hasil, dan langkah berikutnya.
- Hak akses umum, pengaturan inti, diagnostik, dan kontrak internal modul.
- Pencatatan status modul tanpa mengklaim kemampuan yang belum lulus pengujian.

Core tidak boleh mengasumsikan semua modul siap hanya karena berkas modul ditemukan. Modul yang gagal dimuat harus menghasilkan diagnostik aman dan tidak boleh merusak aktivasi Core bila dapat dihindari.

### 4.2 Modul AMANAH SEO AI

Kemampuan yang saat ini berada di repository AMANAH SEO AI menjadi bagian internal dari paket AMANAH SEO. Area yang perlu dipetakan dan dimigrasikan secara terkendali mencakup, sesuai bukti aktual:
- Entity Profile dan pengelolaan entity.
- Konfigurasi provider/model AI serta pengelolaan kredensial.
- Content Analyzer dan fitur AI/SEO lain yang telah tersedia.
- Prompt/execution, validasi dan sanitasi output, persistence, diagnostik, pengaturan, capability, serta pengujian yang terkait.

Daftar ini adalah peta audit awal, bukan klaim bahwa semua fitur telah lulus runtime atau siap dipindahkan. Kepemilikan API key, pengaturan, tabel, capability, dan data lama harus diputuskan berdasarkan audit sebelum migrasi.

### 4.3 Modul AMANAH SEO Engine

Kemampuan yang saat ini berada di repository AMANAH SEO ENGINE V2 menjadi modul internal dalam paket plugin induk. Area audit awal mencakup workflow keyword/intelligence, requirements, strategy, planning/brief, generation, evaluation, refinement, review, publishing information, dan approval, sesuai implementasi yang benar-benar tersedia.

Modul Engine tidak boleh mengubah atau menerbitkan konten secara otomatis di luar kebijakan dan persetujuan yang disepakati. Status modul dan fitur harus dibuktikan melalui pengujian; nama atau dokumentasi fitur saja tidak membuktikan implementasi.

### 4.4 Batas internal dan kepemilikan data

- Pengguna memasang satu plugin; tidak ada ketergantungan runtime wajib pada dua plugin pendamping yang terpisah.
- Core, modul AI, dan modul Engine tetap dipisahkan secara internal agar tanggung jawab, pengujian, dan perbaikan dapat dilakukan per modul.
- Modul berinteraksi melalui kontrak internal yang eksplisit, bukan melalui akses langsung yang tidak terdokumentasi ke database atau class modul lain.
- Kepemilikan data, migrasi tabel/options, pengaturan, kredensial, capability, hooks, cron/background jobs, REST/AJAX endpoints, dan namespace harus dipetakan sebelum penggabungan kode.
- Data dan pengaturan instalasi yang sudah ada harus dipertahankan melalui migration/compatibility plan yang dapat diuji. Tidak boleh ada penghapusan atau reset data secara diam-diam.
- Fitur AI/Engine tidak otomatis masuk ke lingkup fungsional MVP hanya karena modulnya berada dalam paket yang sama. Siklus MVP Core harus dapat diuji tanpa bergantung pada layanan AI atau seluruh workflow Engine.
- Repository AI dan Engine tetap menjadi arsip sumber/rujukan selama migrasi dan hanya dapat dipensiunkan setelah pengganti tunggal lulus kriteria penerimaan serta pemilik menyetujui penutupan terpisah.

## 5. Alur kerja utama AMANAH SEO

Alur konseptual dari awal hingga perbaikan berkelanjutan:

1. **Daftarkan dan pahami website.** Catat identitas website, tujuan pemilik, cakupan akses, kondisi awal, sumber data yang tersedia, dan batasan. Jangan mengasumsikan koneksi atau data tersedia sebelum diverifikasi.
2. **Bangun gambaran kondisi.** Kumpulkan sinyal yang dapat diakses, tandai kualitas/keterkinian data, lalu kelompokkan temuan menjadi masalah, peluang, risiko, dan hal yang belum diketahui.
3. **Susun prioritas.** Urutkan pekerjaan berdasarkan tujuan pemilik, dampak yang diharapkan, urgensi, usaha, risiko, dependensi, serta kekuatan bukti. Jelaskan alasan rekomendasi; jangan menyajikan skor sebagai fakta objektif bila itu hanya estimasi.
4. **Buat rencana kerja.** Setiap pekerjaan memiliki tujuan, dasar temuan, hasil yang diharapkan, pemilik/penanggung jawab, dependensi, risiko, status, dan kriteria selesai.
5. **Pilih cara pelaksanaan.** Kerjakan secara manual atau melalui modul internal yang tersedia dan telah lulus pemeriksaan. Jangan menganggap semua modul/fitur selalu tersedia atau menjadikan AI/Engine sebagai prasyarat untuk siklus MVP.
6. **Periksa hasil.** Validasi hasil terhadap persyaratan, mutu, bukti, dan risiko. Kegagalan harus dapat dilihat, didiagnosis, dicoba ulang secara aman, atau dialihkan ke penanganan manual.
7. **Minta persetujuan bila diperlukan.** Perubahan penting dan publikasi tidak boleh dilakukan tanpa persetujuan dari pengguna yang berwenang, kecuali kebijakan otomatisasi yang eksplisit telah disetujui dan dibatasi dengan aman.
8. **Laksanakan dan catat.** Setelah diizinkan, lakukan tindakan melalui komponen yang bertanggung jawab. Simpan status, waktu, pelaksana, hasil, dan referensi perubahan yang relevan.
9. **Pantau hasil.** Bandingkan hasil dengan kondisi awal dan tujuan menggunakan data yang benar-benar tersedia. Pisahkan korelasi dari sebab-akibat dan jelaskan keterbatasan atribusi.
10. **Perbarui prioritas.** Gunakan hasil terbaru untuk mempertahankan, mengubah, atau menutup pekerjaan. Siklus berulang tanpa menghapus jejak keputusan sebelumnya.

Setiap tahap dapat berhenti bila prasyarat tidak terpenuhi. Status minimum yang harus dibedakan: **Belum dimulai, Siap, Berjalan, Menunggu persetujuan, Terhambat, Gagal, Selesai, Dibatalkan**. Status tidak boleh dimajukan tanpa bukti yang sesuai.

## 6. Dashboard dan pengalaman pengguna

Dashboard utama harus membantu pengguna menjawab secara cepat:
- Website mana yang memerlukan perhatian?
- Apa perubahan kondisi terbaru dan seberapa dapat dipercaya datanya?
- Apa tiga sampai lima prioritas berikutnya, serta mengapa?
- Pekerjaan mana yang berjalan, tertahan, gagal, atau menunggu persetujuan?
- Tindakan apa yang memerlukan keputusan saya?
- Apa yang selesai dan bukti hasilnya?
- Apa langkah selanjutnya?

Dashboard bukan hanya kumpulan angka atau tautan produk. Informasi yang belum tersedia harus ditandai sebagai belum tersedia, bukan diganti dengan angka rekaan. Detail teknis dapat tersedia di halaman lanjutan agar tampilan utama tetap mudah dipahami.

## 7. Kontrol, keamanan, dan tanggung jawab

Persyaratan konseptual minimum:
- Identitas pengguna dan hak akses diverifikasi pada sisi server.
- Hak untuk melihat data dipisahkan dari hak untuk mengubah, menyetujui, menerbitkan, atau mengelola lisensi.
- Aksi sensitif dilindungi dari pemalsuan permintaan, validasi input yang tidak aman, dan akses lintas pengguna/website.
- Rahasia seperti API key, token, dan kredensial layanan tidak ditampilkan dalam log atau pesan kesalahan.
- Data website hanya diakses sesuai izin yang diberikan; koneksi dapat dicabut.
- Perubahan penting, persetujuan, publikasi, kegagalan, dan perubahan hak akses memiliki jejak audit yang sesuai.
- Backup, pemulihan, retensi/penghapusan data, privasi, dan penanganan insiden harus ditetapkan sebelum peluncuran produksi.
- Lisensi produk, entitlement fitur, aktivasi website, kredensial integrasi, dan API key AI adalah konsep berbeda; jangan mencampuradukkannya.

Detail teknis, kebijakan retensi, yurisdiksi, dan model komersial belum dianggap final hanya karena dicantumkan sebagai kebutuhan.

## 8. Ukuran keberhasilan

Ukuran keberhasilan dipilih sesuai tujuan website dan data yang tersedia, dapat meliputi:
- Cakupan website dan kualitas/keterkinian data yang berhasil dipantau.
- Jumlah temuan terverifikasi dan proporsi prioritas yang memiliki alasan serta pemilik.
- Waktu dari temuan sampai keputusan dan penyelesaian.
- Tingkat pekerjaan yang lulus pemeriksaan dan jumlah regresi.
- Kejelasan status pekerjaan, persetujuan, dan kegagalan.
- Perubahan indikator SEO/performa yang relevan dari sumber data terverifikasi.
- Pengurangan pekerjaan manual berulang atau pekerjaan yang tidak berdampak.

Tidak semua metrik wajib dipakai untuk semua website. Baseline, definisi, sumber, interval pengukuran, dan keterbatasan harus ditetapkan sebelum menyatakan peningkatan.

## 9. Batas ruang lingkup tahap pedoman

Selama tahap penyusunan pedoman (tahap konseptual kini telah ditutup):
- Fokus pekerjaan tahap tersebut hanya pada konsep, batas produk, alur kerja, kebutuhan, keputusan, dan kriteria kesiapan AMANAH SEO.
- Audit repositori AMANAH SEO AI dan AMANAH SEO ENGINE V2 hanya baca-saja dan hanya untuk memeriksa kemampuan nyata serta risiko integrasi.
- Dilarang mengubah kode, branch, konfigurasi, database, runtime, lisensi, atau WordPress dari kedua produk pendamping.
- Jangan melakukan sinkronisasi lokal/WordPress sebagai bagian dari tahap ini.
- Jangan mulai implementasi AMANAH SEO sebelum lingkup awal dan kriteria penerimaannya disetujui.
- Jangan membuka fase baru hanya karena ada dokumen tambahan. Dokumen baru dibuat hanya jika menjawab keputusan atau kebutuhan nyata yang belum tercakup di sini.

## 10. Urutan penyelesaian pedoman — satu jalur, tanpa putaran tak berujung

**Langkah 1 — Tetapkan dasar produk.** Konfirmasi atau koreksi tujuan, peran tiga produk, prinsip kemandirian, dan kendali pemilik.  
**Langkah 2 — Tetapkan alur pemilik website.** Lengkapi input, output, status, persetujuan, kondisi gagal, dan akhir tiap tahap.  
**Langkah 3 — Tetapkan lingkup MVP AMANAH SEO.** Pisahkan fungsi wajib versi pertama, fungsi setelah MVP, dan hal di luar cakupan.  
**Langkah 4 — Cocokkan dengan kemampuan nyata AI dan ENGINE.** Baca-saja; beri label tersedia-terverifikasi, tersedia-belum-teruji, belum ada, atau belum diketahui.  
**Langkah 5 — Putuskan arsitektur dan integrasi minimum.** Hanya berdasarkan kebutuhan MVP dan hasil langkah 4; tidak perlu mendesain semua kemungkinan masa depan.  
**Langkah 6 — Putuskan keamanan, akses, data, lisensi, serta komersial yang diperlukan untuk MVP.** Hal yang belum diperlukan untuk MVP tetap dicatat, bukan dijadikan penghalang tanpa alasan.  
**Langkah 7 — Tetapkan kriteria penerimaan dan kesiapan.** Tentukan bukti apa yang membuktikan tiap fungsi berjalan dan batas klaim yang diperbolehkan.  
**Langkah 8 — Tutup pedoman.** Terbitkan satu daftar keputusan final, satu daftar keputusan terbuka yang benar-benar memblokir MVP, lingkup MVP, dan acceptance criteria. Setelah pemilik menyetujui, pedoman dinyatakan selesai. Implementasi baru dimulai melalui persetujuan ruang lingkup terpisah.

Setiap langkah menghasilkan artefak/keputusan yang dapat ditinjau. Jangan mengulang langkah yang telah ditutup kecuali ada bukti baru yang mengubah keputusan. Temuan yang tidak memblokir MVP masuk backlog, bukan alasan untuk menunda penutupan.

## 11. Status keputusan dan backlog

### 11.1 Keputusan dasar MVP yang sudah disetujui

Ketujuh keputusan MVP-01 sampai MVP-07 disetujui pemilik pada 2026-10-11. Rincian, alasan, dan batas persetujuan tercatat di [Usulan Lingkup MVP AMANAH SEO](11_MVP_SCOPE_PROPOSAL.md). Persetujuan ini menetapkan WordPress sebagai platform pertama, cakupan satu website per ruang kerja/pengaturan, prinsip sumber data terkecil yang dapat diverifikasi, persetujuan manusia untuk perubahan/publikasi, model akses awal owner/admin, integrasi AI/ENGINE yang opsional, dan kriteria keberhasilan berbasis siklus inti.

### 11.2 Satu gerbang yang wajib diselesaikan sebelum implementasi

- **GATE-DATA-01 — Verifikasi sumber data awal.** Sebelum kode MVP dirancang untuk mengonsumsi sumber tertentu, pilih dan buktikan satu sumber data yang dapat diakses secara sah, memiliki data yang relevan, dan dapat diuji. Catat pemilik akses, cakupan data, keterkinian, batas penggunaan, metode pengambilan, kondisi gagal, dan bukti pengujian. Ini adalah gerbang implementasi, bukan penghalang penutupan pedoman konsep.
- **GATE-CAPABILITY-01 — Verifikasi kemampuan produk pendamping bila integrasi diusulkan.** Jangan mengandalkan AI atau ENGINE sebagai dependensi MVP. Jika suatu integrasi kelak diusulkan, audit repository/versi yang tepat, kontrak, autentikasi/otorisasi, alur data, penanganan gagal, dan tes terlebih dahulu. Sampai bukti tersebut tersedia, status kemampuan integrasi adalah **BELUM TERVERIFIKASI**.

### 11.3 Keputusan non-pemblokir yang dipindahkan ke backlog

Hal-hal berikut tidak perlu diputuskan untuk menutup pedoman atau membuktikan siklus MVP awal. Tidak ada nilai atau pilihan yang boleh dianggap disetujui hanya karena dicantumkan di sini.

| ID backlog | Topik | Kapan diputuskan |
|---|---|---|
| BL-01 | Model komersial, harga, paket, trial, renewal, bundling, batas lisensi | Sebelum penawaran/komersialisasi yang relevan |
| BL-02 | Kebijakan retensi, ekspor, penghapusan, backup, pemulihan, dan kepatuhan spesifik | Sebelum menyimpan data produksi atau meluncurkan layanan |
| BL-03 | Framework, topologi deployment, database, dan arsitektur rinci | Setelah kebutuhan MVP dan verifikasi lingkungan diselesaikan, sebelum implementasi teknis |
| BL-04 | Protokol integrasi, product manifest, identity federation, versioning, dan kontrak teknis AI/ENGINE | Hanya jika integrasi tertentu dipilih; sebelum integrasi dibangun |
| BL-05 | Banyak website per workspace, organisasi/tim kompleks, customer portal | Setelah kebutuhan penggunaan nyata melampaui MVP |
| BL-06 | Target numerik KPI dan periode evaluasi | Setelah baseline dan sumber pengukuran yang dapat dipercaya tersedia |
| BL-07 | Otomatisasi terbatas di masa depan | Hanya melalui persetujuan perubahan ruang lingkup dan kontrol risiko tersendiri |

Model akses awal owner/admin dan larangan tindakan website otonom pada MVP sudah diputuskan; backlog ini hanya mencakup perluasan atau rincian yang tidak dibutuhkan untuk lingkup awal.



## 12. Cara mengelola dokumen lama

- Dokumen ini menjadi sumber utama untuk visi, tujuan, prinsip, pembagian produk, alur utama, dan proses penyelesaian pedoman.
- Dokumen lama yang bertentangan tidak berlaku sebagai aturan aktif sampai isinya ditinjau dan secara eksplisit dimasukkan kembali.
- Laporan audit historis dipertahankan sebagai rekam jejak; laporan tersebut tidak otomatis menjadi keputusan produk.
- Dokumen lama yang digantikan telah dipindahkan ke `docs/archive/` dan diberi penanda historis **SUPERSEDED**. Tautan menuju pedoman aktif harus tetap valid dari lokasi arsip. Jangan menghapus bukti audit atau riwayat keputusan yang dibutuhkan untuk keterlacakan.
- README dan indeks harus mengarahkan pembaca ke dokumen ini. Jangan mempertahankan beberapa sumber kebenaran yang saling bertentangan.

## 13. Definisi selesai

Pedoman dinyatakan **SIAP DITUTUP** ketika:
1. Tujuan, prinsip, batas tiga produk, dan larangan klaim yang tidak terbukti ditetapkan dengan jelas.
2. Alur end-to-end, status, titik persetujuan, penanganan kegagalan, dan siklus perbaikan telah dijelaskan.
3. Lingkup MVP dan hal di luar MVP tercatat dalam dokumen yang ditautkan.
4. Bukti produk pendamping ditinjau dengan label yang jujur; hal yang belum diverifikasi tidak dinyatakan tersedia atau siap integrasi.
5. Semua keputusan yang memblokir definisi MVP diselesaikan; gerbang sebelum implementasi dan backlog dicatat terpisah.
6. Kriteria penerimaan, keamanan minimum, dan batas klaim ditulis.
7. Satu dokumen induk menjadi sumber acuan, keputusan MVP memiliki catatan persetujuan, dan dokumen lama yang bertentangan ditandai superseded.
8. Pemilik memberi instruksi eksplisit untuk menutup pedoman; instruksi penuntasan diberikan pada 2026-10-11.

**Status saat ini:** pedoman konseptual dinyatakan selesai berdasarkan instruksi pemilik tanggal 2026-10-11. Butir 1–3, 5–7 telah dikonsolidasikan di branch ini. Butir 4 terpenuhi pada tingkat pencatatan evidence dan batas klaim, bukan audit runtime menyeluruh: dokumentasi AMANAH SEO AI tersedia secara baca-saja, tetapi status/runtime terkini dan kemampuan integrasi belum diverifikasi; dokumen sumber utama ENGINE V2 belum berhasil diidentifikasi dari path yang dapat diakses pada pemeriksaan ini. Kekurangan bukti dicatat secara eksplisit dan tidak dipakai untuk mengklaim integrasi tersedia. Karena integrasi bersifat opsional, kekurangan ini bukan penghalang penutupan pedoman konseptual.

Persetujuan pedoman bukan persetujuan otomatis untuk mengubah kode, menggabungkan pull request, mengubah runtime, atau menerbitkan produk. Implementasi membutuhkan ruang lingkup dan persetujuan terpisah.



## 14. Lingkup MVP dan keputusan pemilik

Gunakan [Usulan Lingkup MVP AMANAH SEO](11_MVP_SCOPE_PROPOSAL.md) sebagai daftar tunggal fungsi MVP dan keputusan MVP-01 sampai MVP-07. Ketujuh keputusan dasar tersebut disetujui pemilik pada 2026-10-11. Keputusan non-pemblokir yang tidak dibutuhkan untuk menetapkan siklus utama, keamanan minimum, atau kriteria penerimaan telah dipindahkan ke backlog pada Bagian 11.

**Status penutupan:** pedoman konseptual dinyatakan selesai atas instruksi pemilik tanggal 2026-10-11. Sumber data tertentu tetap harus diverifikasi sebelum implementasi; kemampuan integrasi AI/ENGINE tetap belum terverifikasi dan tidak menjadi prasyarat MVP mandiri.

Persetujuan penutupan pedoman tidak berarti persetujuan implementasi, sinkronisasi runtime, atau penggabungan pull request. Semua itu memerlukan persetujuan terpisah.


## 15. Keputusan arsitektur satu plugin — 2026-10-11

Pemilik menyetujui arah arsitektur: **AMANAH SEO menjadi satu plugin WordPress; AMANAH SEO AI dan AMANAH SEO Engine menjadi modul internal di dalam paket plugin tersebut.** Keputusan ini menggantikan bagian pedoman lama yang menyatakan ketiganya harus tetap menjadi produk/plugin terpisah untuk instalasi pengguna.

Keputusan ini menetapkan target produk dan packaging, bukan bukti bahwa kode telah digabungkan. Implementasi masih memerlukan paket migrasi terkontrol yang memuat audit baseline ketiga repository, pemetaan file/dependensi, kontrak modul, strategi data dan pengaturan, kompatibilitas/lisensi, urutan migrasi, tes regresi, kriteria penerimaan, dan rollback. Sampai paket tersebut disetujui, tidak ada perubahan kode, database, runtime, atau WordPress yang dilakukan.

Target penerimaan arsitektur akhir:
1. Pengguna hanya perlu memasang dan mengaktifkan plugin AMANAH SEO.
2. Core dapat diaktifkan tanpa kegagalan ketika modul AI/Engine tidak aktif atau tidak tersedia, sesuai kebijakan modul yang disepakati.
3. Modul berkomunikasi melalui batas internal yang terdokumentasi.
4. Data, pengaturan, lisensi/entitlement, capability, dan perilaku fitur yang ada tidak hilang atau berubah tanpa rencana migrasi dan persetujuan.
5. Seluruh pengujian unit, integrasi, keamanan, migrasi, regresi, dan WordPress runtime yang relevan lulus sebelum penggantian plugin lama dinyatakan selesai.
6. Tidak ada klaim PASS sebelum bukti pengujian tersedia.
