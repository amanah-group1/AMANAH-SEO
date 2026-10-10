# Paket Kesiapan Implementasi MVP AMANAH SEO
**Versi:** 0.1-draft  
**Status:** RANCANGAN TERKONTROL — belum disetujui untuk implementasi  
**Tanggal:** 2026-10-11  
**Dasar produk:** [Pedoman Induk AMANAH SEO](PEDOMAN_INDUK_AMANAH_SEO.md) dan [Lingkup MVP AMANAH SEO](11_MVP_SCOPE_PROPOSAL.md)  
**Batas:** Dokumen persiapan saja. Tidak mengizinkan coding, deployment, perubahan database/runtime, atau integrasi produk pendamping.

## 1. Tujuan

Menetapkan gerbang kesiapan data, batas teknis minimum, urutan implementasi, bukti penerimaan, dan aturan perubahan sebelum kode MVP dibuat. Paket ini tidak menggantikan dua dokumen acuan produk dan tidak menganggap kemampuan yang belum diuji sebagai tersedia.

## 2. Keputusan dasar yang diwarisi

- Platform website pertama: WordPress.
- Cakupan MVP: satu website per workspace/pengaturan.
- Siklus inti: website → kondisi/temuan → prioritas beralasan → tugas → pemeriksaan/keputusan pemilik → hasil → langkah berikutnya.
- Semua perubahan website dan publikasi memerlukan persetujuan eksplisit; MVP tidak mengubah atau menerbitkan konten secara otonom.
- Integrasi AMANAH SEO AI dan AMANAH SEO ENGINE V2 opsional, bukan dependensi MVP.
- Data tidak tersedia, usang, parsial, atau gagal harus ditampilkan apa adanya; jangan mengubahnya menjadi nol atau status sukses.
- Paket ini tidak memilih framework, database, hosting, atau model komersial secara prematur. Rincian itu ditetapkan setelah gerbang data dan pemeriksaan lingkungan.

## 3. GATE-DATA-01 — verifikasi sumber data SEO

### 3.1 Kandidat pertama yang direkomendasikan

**Google Search Console API — Search Analytics, dengan scope OAuth read-only `https://www.googleapis.com/auth/webmasters.readonly`.**

Alasan kandidat:
- Menyediakan data performa pencarian Google yang relevan untuk siklus MVP: klik, tayangan, CTR, posisi rata-rata, dan pengelompokan berdasarkan tanggal, halaman, kueri, perangkat, atau negara.
- Memiliki endpoint resmi untuk memeriksa daftar properti yang dapat diakses pengguna dan tingkat izinnya.
- Memungkinkan MVP memulai dari data pencarian yang terukur, tanpa mengklaim sebagai audit teknis penuh atas seluruh website.
- OAuth dan scope read-only lebih sesuai dengan prinsip akses minimum daripada meminta kredensial akun Google pengguna.

Sumber dokumentasi resmi yang diperiksa:
- Search Analytics query: https://developers.google.com/webmaster-tools/v1/searchanalytics/query
- Daftar properti Search Console: https://developers.google.com/webmaster-tools/v1/sites/list
- OAuth dan scope: https://developers.google.com/webmaster-tools/v1/how-tos/authorizing
- Batas penggunaan: https://developers.google.com/webmaster-tools/limits
- Penjelasan status/keterkinian data: https://support.google.com/webmasters/answer/17011364

### 3.2 Status verifikasi yang jujur

| Aspek | Temuan saat ini | Status |
|---|---|---|
| API dan fungsi | Dokumentasi resmi menunjukkan endpoint Search Analytics dan daftar properti tersedia | TERVERIFIKASI SECARA DOKUMENTASI |
| Relevansi untuk MVP | Mendukung metrik performa pencarian dan tren awal | SESUAI DENGAN BATASAN |
| Scope least-privilege | OAuth 2.0 dan scope read-only tersedia | TERVERIFIKASI SECARA DOKUMENTASI |
| Data/freshness | Data terbaru dapat bersifat preliminary; data final dan parsial harus dibedakan | BATASAN DIKETAHUI |
| Kuota/limit | Ada batas per-site, per-user, dan per-project; kueri tertentu lebih mahal | BATASAN DIKETAHUI |
| Akses nyata ke properti milik pemilik | Belum ada bukti autentikasi dan properti yang berhasil dibaca dalam sesi ini | BELUM DIVERIFIKASI |
| Persetujuan penggunaan dan pemilik akses | Pemilik harus menghubungkan akun dan menyetujui akses | MENUNGGU TINDAKAN PEMILIK |
| Hasil uji integrasi | Belum ada request/response nyata yang diuji | BELUM DILAKUKAN |

**Kesimpulan GATE-DATA-01: BELUM LULUS.** Kandidat teknis telah dipilih dan batas dokumentasinya diverifikasi, tetapi sumber belum dapat dinyatakan siap dipakai MVP sampai akses ke properti nyata diuji. Tidak boleh ada klaim bahwa data telah terhubung.

### 3.3 Bukti minimum untuk menutup gerbang

Pemilik perlu menyediakan akses melalui proses OAuth yang disetujui dan memilih properti Search Console yang tepat. Jangan mengirim password Google, token, client secret, atau kredensial rahasia ke percakapan atau repository.

Uji baca-saja harus membuktikan:
1. OAuth consent dan callback berhasil; token disimpan di tempat rahasia yang sesuai, bukan di log atau repository.
2. Endpoint daftar properti berhasil mengembalikan properti yang diharapkan dan tingkat izin yang memadai.
3. Satu kueri rentang tanggal yang konservatif menghasilkan respons valid; catat tanggal data, jumlah baris, dan status final/preliminary.
4. Data klik, tayangan, CTR, dan posisi dipetakan dengan benar; respons kosong tidak dianggap sebagai angka nol atau kegagalan tanpa pemeriksaan konteks.
5. Kasus 401/403, 429/quota, 5xx/timeout, properti tanpa data, serta token dicabut diperlakukan dengan status yang jelas.
6. Bukti uji tanpa token atau rahasia: timestamp, endpoint/metode, properti tersamarkan bila perlu, rentang tanggal, kode status, bentuk respons yang disanitasi, dan hasil PASS/FAIL.
7. Pemilik mengonfirmasi bahwa properti dan akses tersebut benar dan diizinkan untuk digunakan.

Sampai semua butir terbukti, GATE-DATA-01 tetap terbuka dan implementasi pengambilan data belum boleh dimulai.

## 4. Kontrak data minimum untuk MVP

Kontrak berikut adalah target desain, bukan klaim implementasi yang sudah ada.

### Input
- Identitas properti Search Console yang dipilih pemilik.
- Rentang tanggal dan dimensi terbatas untuk kueri MVP.
- Scope OAuth read-only.
- Konfigurasi jadwal pengambilan yang konservatif dan dapat dikendalikan.

### Output normalisasi
- Properti dan rentang tanggal yang diminta.
- Metrik klik, tayangan, CTR, posisi rata-rata bila tersedia.
- Dimensi yang dikembalikan API, seperti tanggal/halaman/kueri/perangkat.
- Waktu pengambilan, tanggal terbaru yang tersedia, status final/preliminary, dan metadata sumber.
- Status data: `available`, `partial`, `stale`, `empty`, `unauthorized`, `rate_limited`, atau `error`.
- Catatan keterbatasan/ketidakpastian yang bisa ditampilkan pada dashboard.

### Batas interpretasi
- Search Console mengukur performa pada Google Search; bukan bukti menyeluruh mengenai kesehatan teknis website atau seluruh SEO.
- API dapat membatasi baris yang dikembalikan dan tidak menjamin seluruh baris tersedia.
- Data preliminary dapat berubah. Dashboard harus menyatakan rentang waktu dan freshness; jangan mencampur data preliminary dengan data final tanpa label.
- Metrik posisi rata-rata bukan jaminan ranking atau hasil bisnis.
- Tidak adanya baris bukan bukti bahwa klik/impression pasti nol tanpa memeriksa rentang, properti, izin, dan metadata.

## 5. Paket implementasi yang diusulkan — tanpa coding

### Tahap A — bukti sumber dan keputusan teknis
- Tutup GATE-DATA-01 menggunakan checklist Bagian 3.3.
- Periksa repository dan lingkungan AMANAH SEO yang benar sebelum menetapkan bahasa, framework, database, deployment, atau lokasi rahasia.
- Tulis keputusan arsitektur minimum dan alasan pemilihannya; jangan menyalin asumsi dari AMANAH SEO AI atau ENGINE V2.

### Tahap B — desain kontrak dan keamanan
- Definisikan antarmuka provider sumber data dan normalisasi respons tanpa mengikat dashboard ke detail vendor.
- Tetapkan OAuth state/CSRF, validasi redirect URI, pengelolaan token, rotasi/pencabutan, enkripsi saat tersimpan, dan redaksi log.
- Terapkan server-side authorization untuk setiap workspace dan properti.
- Gunakan prinsip read-only. Tidak ada scope untuk memodifikasi website, sitemap, atau Search Console.
- Tetapkan batas timeout, pagination, kuota, backoff, cache, dan kebijakan ketika data usang.
- Tentukan retensi dan penghapusan token/data sebelum penyimpanan data produksi.

### Tahap C — implementasi siklus MVP
1. Daftar dan pengaturan satu website/Workspace.
2. Koneksi dan status sumber data yang dapat dicabut.
3. Ringkasan data dan timestamp/freshness.
4. Temuan dengan sumber, rentang tanggal, alasan, serta label ketidakpastian.
5. Prioritas yang dapat ditelusuri ke temuan; bukan skor misterius.
6. Tugas, status, pemilik keputusan, dependensi, dan kriteria selesai.
7. Persetujuan/catatan pemilik tanpa menjalankan tindakan website otomatis.
8. Riwayat hasil dan dashboard tindakan berikutnya.

Urutan ini adalah rencana; coding baru boleh dimulai setelah GATE-DATA-01 lulus, paket ditinjau, dan persetujuan implementasi diberikan.

## 6. Penanganan kegagalan yang wajib dirancang

| Kondisi | Perilaku yang diwajibkan |
|---|---|
| Token kedaluwarsa/dicabut atau 401 | Tandai koneksi perlu autentikasi ulang; jangan menampilkan data baru seolah berhasil |
| 403/izin properti tidak memadai | Tampilkan izin ditolak; jangan mencoba melewati kontrol akses |
| 429/kuota | Backoff terbatas, hormati batas provider, jadwalkan ulang secara aman, tampilkan waktu pengambilan terakhir |
| Timeout/5xx | Retry terbatas hanya untuk operasi baca yang aman; simpan status gagal dan jangan membuat duplikasi |
| Respons kosong | Bedakan data kosong, rentang tanpa data, properti salah, dan respons parsial; jangan otomatis mengisi nol |
| Data preliminary/usang | Tampilkan label dan waktu pembaruan; jangan menyatakan hasil final |
| Respons tidak valid/schema berubah | Validasi, hentikan pemrosesan yang tidak aman, catat error yang disanitasi |
| Pemilik mencabut koneksi | Hentikan pengambilan berikutnya, cabut/hapus token sesuai kebijakan yang disetujui, simpan jejak audit tanpa rahasia |

## 7. Kriteria penerimaan dan bukti pengujian

MVP belum dapat dinyatakan lulus sebelum tes otomatis dan uji integrasi terkontrol membuktikan:

- Jalur sukses dengan properti yang berizin dan data nyata.
- Properti tanpa data dan rentang tanggal kosong tidak menghasilkan klaim palsu.
- Data preliminary, parsial, dan stale ditandai dengan benar.
- 401, 403, 429, timeout, 5xx, respons invalid, dan token revoked ditangani secara aman.
- Retry terbatas tidak menggandakan tugas/hasil dan tidak melanggar kuota.
- Pengguna tidak dapat membaca data workspace/properti yang bukan kewenangannya.
- OAuth state/CSRF dan redirect/callback divalidasi.
- Token, client secret, dan data sensitif tidak masuk ke log, pesan error, test snapshots, atau repository.
- Setiap temuan dapat ditelusuri ke sumber/rentang waktu atau diberi label ketidakpastian.
- Tidak ada perubahan website/publikasi otonom.
- Kegagalan integrasi tidak ditampilkan sebagai keberhasilan.

Bukti minimal: hasil tes otomatis, ringkasan uji integrasi yang disanitasi, checklist izin, dan hasil uji akses ditolak. Jangan menulis PASS sebelum bukti ada.

## 8. Ruang lingkup yang tidak diizinkan oleh paket ini

- Tidak membuat atau mengubah kode, schema database, konfigurasi runtime, deployment, atau WordPress.
- Tidak menggabungkan perubahan ini ke `main` tanpa persetujuan terpisah.
- Tidak mengubah AMANAH SEO AI atau AMANAH SEO ENGINE V2.
- Tidak membangun integrasi AI/ENGINE sebelum kebutuhan dan kontraknya disetujui secara terpisah.
- Tidak menerbitkan konten atau mengubah website secara otonom.
- Tidak mengklaim data Search Console telah tersedia sebelum pengujian akses nyata lulus.
- Tidak menjanjikan ranking atau hasil bisnis.

## 9. Gerbang persetujuan berikutnya

Sebelum coding:
1. Pemilik mengonfirmasi properti Search Console yang akan dipakai dan melakukan proses otorisasi resmi.
2. Bukti uji read-only dicatat dan GATE-DATA-01 dinyatakan PASS atau FAIL.
3. Paket ini ditinjau dan disetujui, termasuk keputusan arsitektur yang belum diputuskan.
4. Ruang lingkup implementasi, daftar file/komponen yang akan disentuh, acceptance criteria, serta rencana rollback ditetapkan dalam PR implementasi tersendiri.
5. Pemilik memberikan persetujuan eksplisit untuk mulai coding.

**Status akhir saat ini:** paket persiapan disusun; kandidat sumber data didukung dokumentasi resmi; akses nyata belum diverifikasi; GATE-DATA-01 masih terbuka; implementasi belum disetujui dan belum dimulai.
