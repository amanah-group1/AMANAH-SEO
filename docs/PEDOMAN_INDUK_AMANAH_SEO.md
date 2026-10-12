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
7. **Satu plugin untuk pengguna, modul dengan batas fungsi yang jelas.** Pengguna cukup memasang dan mengaktifkan satu plugin WordPress, yaitu AMANAH SEO. Modul AMANAH SEO AI dan AMANAH SEO ENGINE dibundel di dalam paket yang sama dan tidak memerlukan plugin terpisah. Keduanya tetap memiliki identitas, tanggung jawab, serta hak lisensi masing-masing.
8. **Lisensi modul independen dan navigasi konsisten.** Semua menu modul tetap terlihat di bawah satu menu induk AMANAH SEO, termasuk saat lisensi modul belum dimiliki. Lisensi AI tidak mengaktifkan Engine dan lisensi Engine tidak mengaktifkan AI. Halaman modul tanpa entitlement aktif menampilkan sales page yang menjelaskan kebutuhan lisensi, manfaat modul, status yang relevan, dan langkah aktivasi/pembelian; fitur yang dilindungi lisensi tidak boleh dijalankan.
9. **Kontrol terpusat, penegakan di server.** AMANAH SEO menjadi pusat pengelolaan API key/kredensial AI, status lisensi, dan entitlement modul. Hak penggunaan diperiksa di sisi server pada setiap operasi yang dilindungi; menyembunyikan tombol atau halaman saja tidak cukup. Lisensi kedaluwarsa, tidak valid, dicabut, atau gagal diverifikasi tidak boleh dianggap aktif.
10. **Jangan menduplikasi mesin khusus.** AMANAH SEO mengatur konteks website, prioritas, pekerjaan, persetujuan, dan pemantauan. Kemampuan AI khusus tetap berada di modul AMANAH SEO AI; mesin workflow konten terstruktur tetap berada di modul AMANAH SEO ENGINE.
9. **Bukti sebelum klaim.** Status fitur, keamanan, pengujian, dan kesiapan harus didukung bukti.
10. **Perubahan terkendali.** Tidak ada implementasi sebelum ruang lingkup dan kriteria penerimaan disepakati.
11. **Sederhana dan dapat dipahami.** Dashboard dan pedoman tidak boleh memaksa pemilik mengikuti proses dokumentasi yang berputar-putar tanpa menghasilkan keputusan atau produk yang lebih jelas.
12. **Aman secara default.** Akses mengikuti kewenangan; rahasia dan data pengguna dilindungi; tindakan penting dicatat; kegagalan dijelaskan tanpa membocorkan rahasia.

## 4. Pembagian tiga produk

### 4.1 AMANAH SEO — produk utama, plugin tunggal, dan pengelola

AMANAH SEO adalah satu-satunya plugin yang perlu dipasang pengguna. Paket plugin memuat core serta modul AI dan Engine. Pengguna tidak perlu memasang plugin AMANAH SEO AI atau AMANAH SEO ENGINE secara terpisah.

Tanggung jawab konsep:
- Pengalaman utama dan dashboard pengelolaan website.
- Ringkasan kondisi website dan status pekerjaan.
- Daftar masalah, peluang, dan prioritas beserta alasannya.
- Pengelolaan tugas, status, ketergantungan, tenggat, dan penanggung jawab.
- Alur pemeriksaan, persetujuan, dan keputusan pengguna.
- Gambaran hasil, perubahan kondisi, dan rekomendasi langkah berikutnya.
- Pengaturan akses, status lisensi, entitlement, dan kredensial antarmodul melalui kontrol terpusat AMANAH SEO.
- Navigasi tunggal untuk seluruh halaman core dan modul, tanpa menu induk terpisah dari plugin lain.
- Penegakan lisensi di sisi server sebelum fitur terlindungi dijalankan.

AMANAH SEO tetap menjadi pengelola dan **tidak mengambil alih tanggung jawab fungsional khusus** modul AI atau Engine hanya karena keduanya berada dalam satu paket.

### 4.2 Modul AMANAH SEO AI

Peran konseptual: menyediakan kemampuan SEO berbantuan AI, pemahaman konteks/entity, analisis, atau fungsi AI lain yang benar-benar tersedia dan teruji. Modul ini dibundel dalam plugin AMANAH SEO; pengguna tidak memasangnya secara terpisah.

Batasan:
- Hak penggunaan fitur AI ditentukan oleh lisensi/entitlement AI yang independen.
- Menu modul tetap dapat dibuka saat entitlement tidak aktif, tetapi halaman fitur menampilkan sales page yang sesuai dan operasi berlisensi ditolak.
- Tidak boleh dinyatakan menyediakan kemampuan tertentu hanya berdasarkan rencana atau nama fitur; harus diverifikasi melalui repositori dan pengujian.

### 4.3 Modul AMANAH SEO ENGINE

Peran konseptual: menyediakan intelligence dan workflow SEO/konten yang terstruktur, dari kebutuhan dan strategi hingga pembuatan, evaluasi, perbaikan, peninjauan, dan kesiapan publikasi. Modul ini dibundel dalam plugin AMANAH SEO; pengguna tidak memasangnya secara terpisah.

Batasan:
- Hak penggunaan fitur Engine ditentukan oleh lisensi/entitlement Engine yang independen dari lisensi AI.
- Menu modul tetap dapat dibuka saat entitlement tidak aktif, tetapi halaman fitur menampilkan sales page yang sesuai dan operasi berlisensi ditolak.
- Tidak menjamin peringkat mesin pencari.

### 4.4 Aturan hubungan core dan modul

Core AMANAH SEO, modul AI, dan modul Engine berada dalam satu paket distribusi, tetapi batas tanggung jawab fungsional tetap dipisahkan. Modul dapat dijalankan secara independen berdasarkan entitlement masing-masing. Integrasi dengan sistem eksternal tetap opsional dan harus memiliki kontrak yang jelas: tujuan, pemilik data, input/output, autentikasi/otorisasi, kegagalan, pencatatan, kompatibilitas versi, serta cara memutus koneksi. Jika integrasi eksternal tidak tersedia atau gagal, sistem harus menunjukkan status yang jujur.

## 5. Alur kerja utama AMANAH SEO

Alur konseptual dari awal hingga perbaikan berkelanjutan:

1. **Daftarkan dan pahami website.** Catat identitas website, tujuan pemilik, cakupan akses, kondisi awal, sumber data yang tersedia, dan batasan. Jangan mengasumsikan koneksi atau data tersedia sebelum diverifikasi.
2. **Bangun gambaran kondisi.** Kumpulkan sinyal yang dapat diakses, tandai kualitas/keterkinian data, lalu kelompokkan temuan menjadi masalah, peluang, risiko, dan hal yang belum diketahui.
3. **Susun prioritas.** Urutkan pekerjaan berdasarkan tujuan pemilik, dampak yang diharapkan, urgensi, usaha, risiko, dependensi, serta kekuatan bukti. Jelaskan alasan rekomendasi; jangan menyajikan skor sebagai fakta objektif bila itu hanya estimasi.
4. **Buat rencana kerja.** Setiap pekerjaan memiliki tujuan, dasar temuan, hasil yang diharapkan, pemilik/penanggung jawab, dependensi, risiko, status, dan kriteria selesai.
5. **Pilih cara pelaksanaan.** Kerjakan secara manual, melalui AMANAH SEO AI, melalui AMANAH SEO ENGINE V2, atau melalui integrasi lain yang telah disetujui. Jangan menganggap semua metode selalu tersedia.
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
- Lisensi modul, entitlement fitur, aktivasi website, kredensial integrasi, dan API key AI adalah konsep berbeda; jangan mencampuradukkannya. API key adalah kredensial layanan, bukan lisensi produk. Core AMANAH SEO mengelola pengaturan kredensial AI dan status lisensi, sedangkan setiap modul memiliki entitlement sendiri.
- Penolakan lisensi harus ditegakkan di sisi server pada akses halaman berfitur, aksi formulir, AJAX/REST API, proses latar belakang, dan jalur eksekusi lain yang relevan. Semua menu tetap dapat terlihat; tampilan sales page tidak boleh membuka operasi berlisensi.

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
| BL-01 | Model komersial, harga, paket, trial, renewal, aturan masa tenggang, dan detail komersial lisensi | Sebelum penawaran/komersialisasi yang relevan; prinsip lisensi AI dan Engine yang independen serta perilaku menu/sales page sudah diputuskan |
| BL-02 | Kebijakan retensi, ekspor, penghapusan, backup, pemulihan, dan kepatuhan spesifik | Sebelum menyimpan data produksi atau meluncurkan layanan |
| BL-03 | Framework, topologi deployment, database, dan arsitektur rinci | Setelah kebutuhan MVP dan verifikasi lingkungan diselesaikan, sebelum implementasi teknis |
| BL-04 | Kontrak integrasi eksternal, manifest modul, kompatibilitas versi, dan detail teknis komunikasi antarkomponen | Hanya jika kebutuhan nyata memerlukannya; modul AI/Engine tetap dibundel dalam satu plugin, bukan dipasang sebagai plugin terpisah |
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



## 14. Keputusan arsitektur tambahan — 2026-10-12

Pemilik menetapkan keputusan arsitektur berikut pada 2026-10-12. Keputusan ini melengkapi dan, jika bertentangan, menggantikan rumusan lama tentang AI/Engine sebagai plugin terpisah atau integrasi antarproduk yang menjadi prasyarat navigasi.

1. Pengguna cukup memasang dan mengaktifkan **satu plugin WordPress: AMANAH SEO**.
2. Modul AMANAH SEO AI dan AMANAH SEO ENGINE disertakan dalam paket AMANAH SEO; pengguna tidak perlu memasang plugin AI atau Engine secara terpisah.
3. AMANAH SEO menjadi pusat kendali untuk pengelolaan API key/kredensial AI, status lisensi, dan entitlement modul.
4. Lisensi AI dan Engine independen. Lisensi salah satu modul tidak memberikan hak penggunaan modul lainnya.
5. Semua menu modul tetap tampil dan dapat dibuka dari menu induk AMANAH SEO, terlepas dari status lisensinya.
6. Jika entitlement modul tidak aktif, halaman terkait menampilkan sales page yang jelas tentang modul dan kebutuhan lisensinya. Fitur berlisensi tidak dapat dijalankan sampai entitlement yang sah aktif.
7. Lisensi harus diverifikasi di sisi server pada setiap jalur eksekusi yang dilindungi; menu yang terlihat bukan bukti hak akses.
8. Prinsip paket tunggal ini tidak berarti semua fitur otomatis tersedia atau lulus pengujian. Kemampuan fitur tetap harus dibuktikan melalui audit dan pengujian sebelum diklaim siap.

Keputusan ini adalah persetujuan arsitektur konseptual dan dokumentasi. Ini **bukan** persetujuan untuk mulai implementasi kode, mengubah repositori AI/Engine, mengubah runtime, atau menggabungkan pull request. Implementasi membutuhkan ruang lingkup teknis, acceptance criteria, dan persetujuan terpisah.

## 15. Lingkup MVP dan keputusan pemilik

Gunakan [Usulan Lingkup MVP AMANAH SEO](11_MVP_SCOPE_PROPOSAL.md) sebagai daftar tunggal fungsi MVP dan keputusan MVP-01 sampai MVP-07. Ketujuh keputusan dasar tersebut disetujui pemilik pada 2026-10-11. Keputusan non-pemblokir yang tidak dibutuhkan untuk menetapkan siklus utama, keamanan minimum, atau kriteria penerimaan telah dipindahkan ke backlog pada Bagian 11.

**Status penutupan:** pedoman konseptual dinyatakan selesai atas instruksi pemilik tanggal 2026-10-11. Sumber data tertentu tetap harus diverifikasi sebelum implementasi; kemampuan integrasi AI/ENGINE tetap belum terverifikasi dan tidak menjadi prasyarat MVP mandiri.

Persetujuan penutupan pedoman tidak berarti persetujuan implementasi, sinkronisasi runtime, atau penggabungan pull request. Semua itu memerlukan persetujuan terpisah.
