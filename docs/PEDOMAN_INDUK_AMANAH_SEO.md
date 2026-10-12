# PEDOMAN INDUK AMANAH SEO
**Versi:** 1.0-final-konseptual  
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
7. **Satu plugin runtime, dua modul internal.** Pengguna cukup memasang dan mengaktifkan satu plugin WordPress, yaitu AMANAH SEO. AMANAH SEO AI dan AMANAH SEO ENGINE V2 berada sebagai modul internal dalam paket tersebut, tetap memiliki fungsi dan batas tanggung jawab masing-masing. Integrasi dengan sistem eksternal tetap opsional dan tidak boleh dianggap berhasil tanpa bukti.
8. **Jangan menduplikasi mesin khusus.** AMANAH SEO mengatur konteks website, prioritas, pekerjaan, persetujuan, dan pemantauan. Kemampuan AI khusus tetap milik AMANAH SEO AI; mesin workflow konten terstruktur tetap milik AMANAH SEO ENGINE V2.
9. **Bukti sebelum klaim.** Status fitur, keamanan, pengujian, dan kesiapan harus didukung bukti.
10. **Perubahan terkendali.** Tidak ada implementasi sebelum ruang lingkup dan kriteria penerimaan disepakati.
11. **Sederhana dan dapat dipahami.** Dashboard dan pedoman tidak boleh memaksa pemilik mengikuti proses dokumentasi yang berputar-putar tanpa menghasilkan keputusan atau produk yang lebih jelas.
12. **Aman secara default.** Akses mengikuti kewenangan; rahasia dan data pengguna dilindungi; tindakan penting dicatat; kegagalan dijelaskan tanpa membocorkan rahasia.

## 4. Arsitektur produk dan batas modul

**Target packaging yang disetujui:** pengguna memasang satu plugin WordPress bernama **AMANAH SEO**. Kemampuan yang berasal dari AMANAH SEO AI dan AMANAH SEO ENGINE V2 ditata sebagai **modul internal** di dalam plugin induk setelah audit dan migrasi terkontrol; keduanya bukan plugin runtime tambahan yang dipasang berdampingan pada target akhir ini.

### 4.1 Core AMANAH SEO — pengelola utama

Tanggung jawab core:
- Pengalaman utama, navigasi, dan dashboard pengelolaan website.
- Ringkasan kondisi website dan status pekerjaan.
- Daftar masalah, peluang, dan prioritas beserta alasan/buktinya.
- Pengelolaan tugas, status, ketergantungan, tenggat, dan penanggung jawab.
- Alur pemeriksaan, persetujuan, dan keputusan pengguna.
- Gambaran hasil, perubahan kondisi, dan rekomendasi langkah berikutnya.
- Kepemilikan kontrak modul, hak akses lintas modul, lifecycle produk yang terkoordinasi, serta pusat pengelolaan API key/kredensial AI dan lisensi/entitlement modul.
- Navigasi tunggal untuk seluruh menu core dan modul; semua menu modul tetap terlihat terlepas dari status lisensinya.
- Pemeriksaan entitlement dan penolakan operasi berlisensi yang tidak sah di sisi server.

Core tidak boleh menyalin atau menggandakan mesin AI atau workflow konten. Core memanggil kontrak modul yang disetujui jika diperlukan dan tersedia; siklus MVP dasar tetap dapat diuji tanpa mengandalkan fitur AI/Engine yang belum dipindahkan atau diverifikasi.

### 4.2 Modul AI — baseline AMANAH SEO AI

Modul ini dapat menyediakan kemampuan SEO berbantuan AI, pemahaman entity, analisis, dan fungsi AI lain yang benar-benar tersedia dan teruji pada baseline sumbernya. Repository AMANAH SEO AI tetap baseline audit/migrasi sampai perubahan terkontrol disetujui.

Batasan:
- Hak penggunaan fitur AI ditentukan oleh lisensi/entitlement AI yang independen dari lisensi Engine.
- Menu AI tetap terlihat dan dapat dibuka. Jika entitlement AI tidak aktif, halaman fitur AI yang dilindungi menampilkan sales page yang menjelaskan kebutuhan lisensi dan cara mengaktifkannya; operasi berlisensi tetap ditolak.
- Fitur modul tidak otomatis menjadi bagian MVP fungsional hanya karena kode modul disertakan.
- Konfigurasi, kredensial, lisensi, data, provider/fallback, dan perilaku kegagalan harus diaudit sebelum migrasi; kontrak yang telah dikunci tidak boleh diubah tanpa keputusan terkontrol.
- Jangan menjalankan lifecycle plugin lama secara paralel setelah migrasi ke satu plugin induk.

### 4.3 Modul Engine — baseline AMANAH SEO ENGINE V2

Modul ini dapat menyediakan workflow SEO/konten terstruktur, dari kebutuhan dan strategi hingga generasi, evaluasi, refinement, review, dan kesiapan publikasi sesuai kemampuan baseline yang terbukti. Repository AMANAH SEO ENGINE V2 tetap baseline audit/migrasi sampai perubahan terkontrol disetujui.

Batasan:
- Hak penggunaan fitur Engine ditentukan oleh lisensi/entitlement Engine yang independen dari lisensi AI.
- Menu Engine tetap terlihat dan dapat dibuka. Jika entitlement Engine tidak aktif, halaman fitur Engine yang dilindungi menampilkan sales page yang menjelaskan kebutuhan lisensi dan cara mengaktifkannya; operasi berlisensi tetap ditolak.
- Fitur modul tidak otomatis menjadi bagian MVP fungsional hanya karena kode modul disertakan.
- Model data, migrasi, cron/jobs, route/menu, kapabilitas, lisensi, dan uninstall harus diaudit sebelum migrasi; rollback tidak boleh diasumsikan jika tidak ada kontrak teruji.
- Jangan menyalin registrasi plugin dan lifecycle lama secara apa adanya ke plugin induk.

### 4.4 Kontrak packaging dan lifecycle

- **Satu plugin runtime:** hanya plugin AMANAH SEO yang dipasang dan diaktifkan pada target akhir. Modul AI dan Engine memiliki batas internal yang jelas, bukan registrasi plugin mandiri tambahan.
- **Lisensi modul independen:** lisensi AI tidak mengaktifkan Engine, dan lisensi Engine tidak mengaktifkan AI. Menu modul selalu terlihat; tanpa entitlement aktif, halaman modul menampilkan sales page yang informatif, sementara operasi berlisensi ditolak.
- **Kontrol lisensi dan kredensial terpusat:** AMANAH SEO menjadi tempat pengelolaan API key/kredensial AI dan lisensi/entitlement setiap modul. API key adalah kredensial layanan, bukan lisensi produk.
- **Pemeriksaan sisi server:** entitlement diperiksa sebelum operasi yang dilindungi, termasuk akses halaman fitur, submit form, AJAX/REST API, proses latar belakang, dan jalur eksekusi lain yang relevan. Menyembunyikan menu atau tombol saja tidak cukup.
- **Satu koordinator lifecycle:** AMANAH SEO Lifecycle Manager mengoordinasikan aktivasi, upgrade, migrasi, deaktivasi, uninstall, dan cleanup komponen internal yang hanya perlu satu instance. Modul mempertahankan logika migrasi/cleanup domainnya dan versi database masing-masing, tetapi koordinator memanggilnya dengan urutan, status, logging, dan pemulihan yang terkendali.
- **Migrasi aman:** jangan menjalankan installer/lifecycle lama yang destruktif sebelum schema, tabel legacy/arsip, kredensial, cron, dan strategi backup/restore diaudit. Data pengguna dipertahankan secara default saat uninstall; penghapusan data memerlukan kebijakan eksplisit dan perlindungan rahasia.
- **Batas fase:** keputusan packaging satu plugin tidak memberi izin untuk memindahkan kode sekarang. Source repositories AI/Engine, runtime, database, lisensi, dan konfigurasi tidak diubah pada tahap penutupan pedoman. Migrasi dilakukan satu paket terkontrol setelah scope, file, tes, dan rollback disetujui.

## 5. Alur kerja utama AMANAH SEO

Alur konseptual dari awal hingga perbaikan berkelanjutan. Alur ini adalah milik AMANAH SEO sebagai pengelola pekerjaan; ia tidak menyalin mesin produksi konten atau workflow khusus milik produk pendamping.

1. **Daftarkan dan pahami website.** Catat identitas website, tujuan pemilik, cakupan akses, kondisi awal, dan kewenangan yang diberikan.
2. **Hubungkan dan verifikasi sumber data awal.** Gunakan data internal WordPress untuk struktur website, URL, judul, konten, dan hubungan konten yang benar-benar dapat dibaca dengan izin yang tersedia. Gunakan Google Search Console (GSC) untuk kueri, klik, tayangan, CTR, posisi, dan halaman pencarian ketika properti serta akses baca-saja telah diverifikasi. Keduanya adalah sumber awal yang dipilih secara konseptual; koneksi aktual tidak boleh diasumsikan.
3. **Bangun gambaran kondisi.** Tandai asal, waktu, cakupan, keterkinian, dan keterbatasan data. Kelompokkan temuan menjadi masalah, peluang, risiko, dan hal yang belum diketahui. Bedakan hasil pengukuran, masukan manual, analisis, dan perkiraan.
4. **Susun prioritas.** Urutkan pekerjaan menurut tujuan pemilik, dampak yang diperkirakan, urgensi, usaha, risiko, dependensi, serta kekuatan bukti. Jelaskan alasan; skor atau dampak estimasi bukan fakta objektif.
5. **Buat rencana kerja.** Setiap tugas memiliki tujuan, dasar temuan, hasil yang diharapkan, penanggung jawab/pemilik keputusan, dependensi, risiko, status, dan kriteria selesai.
6. **Pilih cara pelaksanaan.** Pekerjaan dapat dilakukan manual atau melalui integrasi yang secara khusus disetujui dan telah diverifikasi. AMANAH SEO AI dan AMANAH SEO ENGINE V2 tetap produk terpisah dan opsional; jangan menganggap koneksi, fitur, atau keberhasilan integrasinya tersedia tanpa bukti.
7. **Periksa hasil.** Bandingkan hasil dengan kriteria selesai, mutu, bukti, dan risiko. Kegagalan harus terlihat, dapat didiagnosis, dicoba ulang secara aman, atau dialihkan ke penanganan manual.
8. **Minta dan catat persetujuan.** Tindakan yang mengubah website dan publikasi memerlukan persetujuan eksplisit pengguna yang berwenang. Persetujuan terkait versi/isi tertentu; perubahan material setelah persetujuan membutuhkan pemeriksaan dan persetujuan ulang.
9. **Laksanakan tindakan yang diizinkan.** Pada MVP dasar, AMANAH SEO mengelola tugas dan keputusan, bukan mengubah atau menerbitkan website secara otonom. Setiap tindakan yang kelak disetujui harus mencatat pelaksana, waktu, versi/target, hasil, dan referensi bukti.
10. **Pantau hasil dan perbarui prioritas.** Bandingkan kondisi dengan baseline memakai data yang benar-benar tersedia. Pisahkan korelasi dari sebab-akibat; gunakan hasil untuk mempertahankan, mengubah, atau menutup tugas tanpa menghapus riwayat.

### 5.1 Kontrak sumber data awal

| Sumber | Penggunaan | Syarat bukti | Jika tidak tersedia |
|---|---|---|---|
| WordPress internal | Struktur website, URL, judul, konten dan hubungan konten yang dapat diakses | Izin dan metode baca terbukti; cakupan serta waktu pembacaan dicatat | Tandai tidak tersedia/gagal; gunakan masukan manual bila sesuai dan beri label sumber |
| Google Search Console | Kueri, klik, tayangan, CTR, posisi, dan halaman pencarian | Properti benar, akses baca-saja sah, data contoh terbaca, rentang tanggal dan keterkinian dicatat | Jangan mengarang metrik atau mengubah data hilang menjadi nol; fitur GSC berstatus belum terhubung/terhambat |
| Masukan manual pengguna | Temuan atau konteks yang belum dapat diperoleh otomatis | Identitas sumber, waktu masukan, alasan, dan label manual | Jangan tampilkan sebagai hasil pengukuran otomatis |

Sumber eksternal lain ditunda sampai kebutuhan nyata, nilai guna, biaya, izin, keamanan, dan dampaknya diaudit serta disetujui. Pemilihan sumber secara konseptual tidak sama dengan kelulusan gerbang akses teknis.

### 5.2 Status, transisi, dan pemulihan

Status minimum tugas: **Belum dimulai, Siap, Berjalan, Menunggu persetujuan, Terhambat, Gagal, Selesai, Dibatalkan**. Setiap perubahan status harus memiliki waktu, pelaku/sumber, dan alasan yang sesuai. Status **Selesai** memerlukan bukti terhadap kriteria selesai; status **Menunggu persetujuan** tidak boleh diperlakukan sebagai izin; **Terhambat** membutuhkan alasan dan langkah berikutnya; **Gagal** harus mempertahankan diagnostik yang aman dan hasil parsial; **Dibatalkan** membutuhkan alasan dan tidak boleh menghapus riwayat.

- Prasyarat atau akses tidak terpenuhi: hentikan tahap yang bergantung padanya, tampilkan sebab dan langkah pemulihan, serta jangan memalsukan keberhasilan.
- Data kosong, usang, parsial, atau gagal diambil: bedakan tiap keadaan dan pertahankan waktu pemeriksaan terakhir yang berhasil.
- Percobaan ulang hanya dilakukan jika aman/idempoten atau telah dipastikan tidak menggandakan perubahan; tindakan yang hasilnya tidak pasti harus diverifikasi sebelum diulang.
- Penolakan persetujuan: kembalikan pekerjaan untuk revisi atau tandai dibatalkan sesuai keputusan pemilik; jangan melanjutkan tindakan.
- Perubahan material setelah persetujuan: batalkan kelayakan persetujuan untuk versi lama dan minta persetujuan ulang.
- Kegagalan satu integrasi opsional tidak boleh membuat seluruh produk berpura-pura gagal/sukses; isolasi dampak, tampilkan status koneksi, dan pertahankan jalur manual jika aman.

Setiap tahap dapat berhenti bila prasyarat tidak terpenuhi. Status tidak boleh dimajukan tanpa bukti yang sesuai.

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
- Lisensi produk/modul, entitlement fitur, aktivasi website, kredensial integrasi, dan API key AI adalah konsep berbeda; jangan mencampuradukkannya. API key tidak memberikan hak lisensi. AMANAH SEO mengelola status lisensi dan kredensial AI secara terpusat; entitlement modul tetap independen.
- Semua menu modul harus tetap dapat dilihat dan dibuka. Ketika entitlement tidak aktif, tampilkan sales page yang jelas; jangan menjalankan operasi berlisensi. Pemeriksaan dilakukan di sisi server pada semua jalur eksekusi yang dilindungi.

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

## 10. Riwayat penutupan pedoman — satu jalur, tanpa putaran tak berujung

Urutan berikut dicatat sebagai rekam jejak penyelesaian, bukan daftar pekerjaan yang masih terbuka:

1. Dasar produk, target packaging satu plugin, pembagian core/modul internal, prinsip kemandirian tanggung jawab, dan kendali pemilik ditetapkan.
2. Alur end-to-end, sumber data awal, status, persetujuan, kegagalan, pemulihan, dan evaluasi ditetapkan.
3. Lingkup MVP, hal di luar MVP, serta tujuh keputusan MVP disepakati.
4. Kemampuan AI/ENGINE diperlakukan secara konservatif: integrasi tetap opsional dan status yang belum diuji tidak dianggap tersedia.
5. Keputusan lifecycle internal, keamanan minimum, kriteria penerimaan, dan batas klaim dicatat.
6. Keputusan non-pemblokir dipindahkan ke backlog; bukti akses sumber dan pengujian dipisahkan sebagai gerbang implementasi.
7. Pedoman konseptual ditutup atas instruksi pemilik pada 2026-10-11. Implementasi memerlukan persetujuan ruang lingkup terpisah.

Langkah yang telah ditutup tidak dibuka kembali kecuali ada bukti baru yang mengubah keputusan. Temuan non-pemblokir tetap di backlog, bukan alasan untuk menunda penutupan.

## 11. Status keputusan dan backlog

### 11.1 Keputusan dasar MVP yang sudah disetujui

Ketujuh keputusan MVP-01 sampai MVP-07 disetujui pemilik pada 2026-10-11. Rincian, alasan, dan batas persetujuan tercatat di [Usulan Lingkup MVP AMANAH SEO](11_MVP_SCOPE_PROPOSAL.md). Persetujuan ini menetapkan WordPress sebagai platform pertama, cakupan satu website per ruang kerja/pengaturan, sumber awal konseptual berupa WordPress internal dan GSC (dengan verifikasi teknis sebelum fitur terkait), persetujuan manusia untuk perubahan/publikasi, model akses awal owner/admin, integrasi AI/ENGINE yang opsional, dan kriteria keberhasilan berbasis siklus inti.

### 11.2 Gerbang implementasi dan keputusan arsitektur

- **GATE-DATA-01 — Verifikasi akses WordPress dan GSC.** Sumber awal konseptual telah dipilih: WordPress internal untuk struktur/konten dan GSC untuk performa pencarian. Sebelum fitur terkait dibangun atau dinyatakan berfungsi, buktikan izin, cakupan, metode baca, contoh data nyata, keterkinian, batas penggunaan, dan kondisi gagal untuk masing-masing sumber. Untuk menguji siklus dasar tugas, temuan manual yang diberi label jelas boleh digunakan tanpa mengklaim koneksi sumber otomatis lulus. Ini adalah gerbang implementasi, bukan penghalang penutupan pedoman konsep.
- **GATE-CAPABILITY-01 — Verifikasi integrasi AI/ENGINE bila diusulkan.** Produk pendamping tetap opsional. Jika integrasi dipilih, audit repositori/versi, kontrak, autentikasi/otorisasi, alur data, penanganan gagal, dan pengujian. Sampai bukti tersedia, status integrasi adalah **BELUM TERVERIFIKASI**.
- **ARCH-PACKAGING-01 — Target satu plugin.** AMANAH SEO menjadi satu-satunya plugin runtime yang dipasang. Kemampuan AI dan Engine menjadi modul internal setelah audit/migrasi terkontrol; repository AI/Engine tetap baseline sumber, bukan plugin runtime tambahan pada target akhir. Keputusan packaging tidak memasukkan seluruh fitur modul ke MVP dasar dan bukan izin untuk memindahkan kode.
- **ARCH-LIFECYCLE-01 — Pemilik siklus hidup.** AMANAH SEO Lifecycle Manager menjadi satu koordinator untuk aktivasi, upgrade, migrasi, deaktivasi, uninstall, dan cleanup komponen internal yang hanya perlu satu instance. Modul tetap memiliki logika migrasi/cleanup domain dan versi database masing-masing, dipanggil secara terkendali oleh koordinator. Kegagalan harus dicatat dan ditangani aman; uninstall default mempertahankan data pengguna, dan penghapusan data hanya mengikuti kebijakan eksplisit serta perlindungan rahasia/data.

### 11.3 Keputusan non-pemblokir yang dipindahkan ke backlog

Hal-hal berikut tidak perlu diputuskan untuk menutup pedoman atau membuktikan siklus MVP awal. Tidak ada nilai atau pilihan yang boleh dianggap disetujui hanya karena dicantumkan di sini.

| ID backlog | Topik | Kapan diputuskan |
|---|---|---|
| BL-01 | Model komersial, harga, paket, trial, renewal, masa tenggang, dan rincian komersial lisensi | Sebelum penawaran/komersialisasi yang relevan; prinsip entitlement independen dan perilaku menu/sales page telah diputuskan |
| BL-02 | Kebijakan retensi, ekspor, penghapusan, backup, pemulihan, dan kepatuhan spesifik | Sebelum menyimpan data produksi atau meluncurkan layanan |
| BL-03 | Framework, topologi deployment, database, dan arsitektur rinci | Setelah kebutuhan MVP dan verifikasi lingkungan diselesaikan, sebelum implementasi teknis |
| BL-04 | Kontrak integrasi eksternal, manifest modul, kompatibilitas versi, dan detail komunikasi teknis antarkomponen | Hanya jika kebutuhan nyata memerlukannya; modul AI/Engine tetap dibundel dalam satu plugin |
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

**Status saat ini:** pedoman konseptual dinyatakan selesai berdasarkan instruksi pemilik tanggal 2026-10-11. Tujuan, batas produk, alur utama, lingkup MVP, prinsip keamanan, dan kriteria penerimaan telah dikonsolidasikan dalam dokumen acuan. Sumber awal konseptual adalah WordPress internal dan GSC; akses aktual keduanya tetap harus dibuktikan melalui GATE-DATA-01 sebelum fitur terkait diklaim berfungsi. Kemampuan integrasi AI/ENGINE tetap belum terverifikasi dan bukan dependensi MVP. Keterbatasan bukti ini dicatat secara eksplisit dan bukan penghalang penutupan pedoman konseptual.

Persetujuan pedoman bukan persetujuan otomatis untuk mengubah kode, menggabungkan pull request, mengubah runtime, atau menerbitkan produk. Implementasi membutuhkan ruang lingkup dan persetujuan terpisah.



## 14. Lingkup MVP dan keputusan pemilik

Gunakan [Usulan Lingkup MVP AMANAH SEO](11_MVP_SCOPE_PROPOSAL.md) sebagai daftar tunggal fungsi MVP dan keputusan MVP-01 sampai MVP-07. Ketujuh keputusan dasar tersebut disetujui pemilik pada 2026-10-11. Keputusan non-pemblokir yang tidak dibutuhkan untuk menetapkan siklus utama, keamanan minimum, atau kriteria penerimaan telah dipindahkan ke backlog pada Bagian 11.

**Status penutupan:** pedoman konseptual dinyatakan selesai atas instruksi pemilik tanggal 2026-10-11. Sumber awal konseptual telah dipilih (WordPress internal dan GSC); akses dan kualitas data tetap harus diverifikasi sebelum fitur terkait dibuat atau dinyatakan lulus. Kemampuan integrasi AI/ENGINE tetap belum terverifikasi dan tidak menjadi prasyarat MVP mandiri.

Persetujuan penutupan pedoman tidak berarti persetujuan implementasi, sinkronisasi runtime, atau penggabungan pull request. Semua itu memerlukan persetujuan terpisah.


## 15. Konsolidasi final alur dan status penutupan

- Alur AMANAH SEO adalah siklus pengelolaan website → sumber/kondisi → temuan → prioritas beralasan → tugas → pemeriksaan/keputusan → pelaksanaan yang diizinkan → pencatatan hasil → pemantauan → pembaruan prioritas.
- Packaging runtime target adalah satu plugin AMANAH SEO dengan core serta modul internal AI/Engine. Batas modul dan kontrak internal harus dipertahankan; integrasi/packaging tidak berarti seluruh fitur modul masuk ke MVP.
- Alur produksi konten terperinci tetap dimiliki modul Engine; core tidak menduplikasi workflow dan hanya menggunakan kontrak internal yang disetujui.
- Sumber awal konseptual yang disetujui: WordPress internal dan Google Search Console. Sumber lain ditunda sampai ada alasan serta persetujuan.
- Keputusan konseptual yang memblokir penutupan pedoman: **tidak ada yang tersisa**. Gerbang verifikasi akses sumber, uji lingkungan, dan verifikasi integrasi adalah prasyarat implementasi/fitur terkait, bukan keputusan produk terbuka.
- Lingkup MVP dan tujuh keputusan MVP-01 sampai MVP-07 tetap menjadi acuan. Tidak ada persetujuan implementasi, merge, deployment, atau perubahan produk pendamping yang tersirat dari penutupan ini.


## 16. Keputusan arsitektur lisensi modul — 2026-10-12

Pemilik menetapkan keputusan berikut sebagai prinsip arsitektur resmi:

1. Pengguna cukup memasang dan mengaktifkan satu plugin WordPress, yaitu AMANAH SEO. Modul AI dan Engine disertakan dalam paket tersebut dan tidak memerlukan plugin terpisah.
2. AMANAH SEO menjadi pusat kendali pengelolaan API key/kredensial AI, status lisensi, entitlement modul, dan pembaruan produk yang dikelola secara terpusat.
3. Lisensi AMANAH SEO AI dan AMANAH SEO ENGINE independen. Lisensi satu modul tidak mengaktifkan modul lainnya.
4. Seluruh menu modul tetap terlihat dan dapat dibuka, meskipun pengguna belum memiliki lisensi modul tersebut.
5. Jika lisensi/entitlement tidak aktif, halaman fitur modul menampilkan sales page yang menjelaskan modul yang dibuka, manfaatnya, kebutuhan lisensi, status yang relevan, dan langkah mendapatkan atau mengaktifkan lisensi.
6. Operasi yang dilindungi lisensi tidak boleh dijalankan tanpa entitlement sah. Pemeriksaan wajib dilakukan di sisi server pada semua jalur eksekusi relevan; tampilan menu atau tombol bukan kontrol akses yang cukup.
7. API key/kredensial provider adalah rahasia layanan dan berbeda dari lisensi/entitlement produk.
8. Keputusan ini mengunci arsitektur produk dan perilaku pengguna, bukan detail harga, trial, renewal, masa tenggang, atau implementasi teknis. Implementasi memerlukan audit, scope terkontrol, acceptance criteria, rencana rollback, dan persetujuan terpisah.

Keputusan ini melengkapi dan menggantikan bagian dokumen terdahulu yang dapat ditafsirkan sebagai pemasangan plugin AI/Engine secara terpisah. Tidak ada perubahan kode, runtime, database, atau repositori produk pendamping yang disetujui oleh keputusan dokumentasi ini.
