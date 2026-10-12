# Usulan Lingkup MVP AMANAH SEO
**Status:** DISETUJUI PEMILIK — DASAR LINGKUP MVP  
**Tanggal:** 2026-10-11  
**Tujuan:** Mempersempit keputusan agar pedoman dapat selesai tanpa membuat daftar fitur yang terus membesar.

## 1. Rekomendasi inti

MVP harus membuktikan satu siklus nilai dari awal sampai akhir:

**Website → kondisi/temuan → prioritas beralasan → tugas → pemeriksaan/keputusan pemilik → pencatatan hasil → langkah berikutnya.**

Jangan menjadikan MVP sebagai upaya membangun semua fitur SEO sekaligus. Target packaging adalah satu plugin AMANAH SEO dengan core dan modul internal AI/Engine setelah audit/migrasi terkontrol. Kemampuan penuh modul tidak otomatis masuk lingkup fungsional MVP; jangan menyalin workflow Engine ke core.

Prinsip packaging/aktivasi yang mengikat: pengguna cukup memasang plugin AMANAH SEO dan menggunakan satu lisensi AMANAH SEO untuk aktivasi produk. Tidak boleh mensyaratkan instalasi atau aktivasi plugin AMANAH SEO AI maupun AMANAH SEO ENGINE V2 secara terpisah. Prinsip ini tidak berarti seluruh fitur modul otomatis mendapat entitlement atau siap digunakan; validasi teknis lisensi dan kontrak modul tetap menjadi gerbang implementasi.

## 2. Fungsi yang direkomendasikan sebagai wajib MVP

1. **Daftar website** — menambahkan, melihat, mengubah informasi dasar, dan menonaktifkan koneksi/website dengan aman.
2. **Ringkasan kondisi dan sumber data** — menampilkan temuan hanya dari data yang benar-benar tersedia, waktu pembaruan, serta batasan atau kegagalan pengambilan data.
3. **Temuan dan prioritas** — mencatat masalah/peluang, bukti atau sumber, dampak/urgensi yang diperkirakan, ketidakpastian, serta alasan prioritas.
4. **Daftar pekerjaan** — membuat tugas dari temuan, menetapkan status dan penanggung jawab, mencatat dependensi, serta menentukan kriteria selesai.
5. **Kontrol pengguna** — status menunggu persetujuan dan catatan keputusan untuk tindakan berdampak; MVP tidak menerbitkan atau mengubah website secara otonom.
6. **Hasil dan riwayat** — menyimpan status, waktu, bukti hasil, kegagalan, keputusan, dan catatan perubahan yang relevan.
7. **Dashboard tindakan** — menunjukkan kondisi, prioritas teratas, pekerjaan terhambat/gagal, dan keputusan yang menunggu pemilik.
8. **Akses dan keamanan minimum** — autentikasi/otorisasi sisi server, pemisahan akses, perlindungan tindakan sensitif, dan penanganan rahasia yang aman.
9. **Penanganan kegagalan** — membedakan belum tersedia, tertunda, gagal, dan selesai; menyediakan penjelasan serta langkah pemulihan yang aman.
10. **Pengujian penerimaan** — membuktikan siklus utama berjalan dan pengguna tidak dapat mengakses atau menyetujui tindakan di luar kewenangannya.

## 3. Usulan yang tidak masuk MVP awal

Kecuali pemilik secara eksplisit menyatakan sebaliknya, jangan jadikan hal berikut sebagai syarat MVP:
- Generasi artikel lengkap di dalam AMANAH SEO.
- Mesin workflow konten yang menduplikasi AMANAH SEO ENGINE V2.
- Menjadikan kemampuan AI atau seluruh workflow Engine sebagai prasyarat fungsional MVP dasar. Modul internal dapat berada dalam paket plugin yang sama, tetapi siklus MVP harus tetap dapat diuji tanpa mengandalkan fitur modul yang belum diverifikasi.
- Penerbitan otomatis, perubahan massal, atau tindakan SEO berisiko tanpa persetujuan.
- Integrasi dengan banyak penyedia data sekaligus sebelum satu sumber dipilih dan diverifikasi.
- Portal pelanggan, tim/organisasi kompleks, marketplace, payment gateway, billing AI, atau sistem komersial penuh.
- Klaim ranking, skor kualitas yang tidak dapat dijelaskan, atau metrik yang tidak memiliki sumber terverifikasi.
- Analitik lanjutan yang tidak dibutuhkan untuk siklus utama.

Ini adalah usulan batas MVP, bukan pernyataan bahwa semua fungsi wajib di atas sudah tersedia atau telah diimplementasikan.

## 4. Keputusan MVP yang telah disetujui pemilik

| ID | Keputusan | Keputusan yang disetujui | Status |
|---|---|---|---|
| MVP-01 | Platform website pertama | WordPress sebagai platform pertama. | DISETUJUI 2026-10-11 |
| MVP-02 | Cakupan awal | Satu website per ruang kerja/pengaturan pada MVP, dengan rancangan tidak menghalangi perluasan kemudian. | DISETUJUI 2026-10-11 |
| MVP-03 | Sumber data awal | WordPress internal untuk struktur/konten website dan Google Search Console untuk metrik performa pencarian adalah sumber awal yang dipilih secara konseptual. Akses nyata, izin, cakupan, dan perilaku gagal masing-masing wajib dibuktikan sebelum fitur terkait dinyatakan berfungsi. Sumber lain ditunda. | DISETUJUI 2026-10-11; verifikasi teknis sebelum fitur terkait |
| MVP-04 | Pelaksanaan tindakan | Semua perubahan website dan publikasi membutuhkan persetujuan eksplisit; MVP hanya mengatur tugas dan hasil, bukan melakukan tindakan otonom. | DISETUJUI 2026-10-11 |
| MVP-05 | Akun dan akses | Minimal pengguna terautentikasi dengan hak akses pemilik/admin yang jelas; multi-tenant/tim kompleks ditunda. | DISETUJUI 2026-10-11 |
| MVP-06 | Pemakaian modul AI/Engine | Kemampuan AI/Engine tidak menjadi prasyarat fungsional MVP dasar. Karena target packaging adalah satu plugin, kontrak internal modul tetap harus dirancang sebelum migrasi; fitur modul dipanggil hanya jika dibutuhkan dan sudah diuji. | DISETUJUI 2026-10-11 untuk prinsip MVP; detail kontrak sebelum migrasi |
| MVP-07 | Keberhasilan MVP | Siklus utama selesai, setiap rekomendasi memiliki alasan/sumber atau label ketidakpastian, persetujuan tercatat, dan kegagalan terlihat. Tetapkan target angka setelah baseline tersedia. | DISETUJUI 2026-10-11 |

## 5. Catatan persetujuan pemilik

Pada 2026-10-11, pemilik menyetujui ketujuh keputusan MVP-01 sampai MVP-07 sebagai dasar pedoman AMANAH SEO. Sumber awal konseptual yang dipilih kemudian dikonsolidasikan adalah WordPress internal untuk struktur/konten dan GSC untuk performa pencarian. Pemilihan sumber tidak membuktikan akses atau koneksi telah tersedia; hal tersebut wajib diverifikasi sebelum fitur terkait dibuat atau dinyatakan lulus. Persetujuan ini menetapkan arah produk dan lingkup MVP, bukan izin implementasi, perubahan kode/runtime, atau penggabungan pull request.

## 6. Kriteria penerimaan MVP yang dapat diuji

MVP belum boleh dinyatakan lulus sebelum bukti menunjukkan:
1. Pengguna yang berwenang dapat mendaftarkan dan mengelola website dalam batas yang disetujui.
2. Dashboard membedakan data nyata dari data yang tidak tersedia atau sudah usang.
3. Setiap temuan memiliki sumber atau alasan, status, dan tingkat ketidakpastian yang sesuai.
4. Prioritas dapat ditelusuri ke temuan dan tujuan; urutannya tidak tampil sebagai klaim tanpa penjelasan.
5. Temuan dapat menjadi tugas, dan tugas memiliki status, penanggung jawab atau pemilik keputusan, serta kriteria selesai.
6. Tindakan sensitif tidak dapat dilakukan tanpa kewenangan dan persetujuan yang diwajibkan.
7. Keputusan, status, kegagalan, dan hasil penting dapat ditelusuri dalam riwayat.
8. Kegagalan sumber data atau integrasi tidak membuat sistem mengklaim pekerjaan berhasil.
9. Pengujian mencakup jalur sukses, kegagalan, akses ditolak, data tidak tersedia, dan percobaan ulang yang aman.
10. Tidak ada klaim bahwa fitur integrasi/AI/ENGINE berjalan sebelum pengujian integrasi membuktikannya.

## 7. Penutupan keputusan MVP

Ketujuh rekomendasi MVP-01 sampai MVP-07 telah disetujui pemilik pada 2026-10-11 dan menjadi dasar lingkup MVP. Tidak perlu meminta persetujuan ulang untuk keputusan yang sama.

- Sumber awal konseptual WordPress internal dan GSC telah dipilih; akses, izin, cakupan, serta perilaku gagal harus dibuktikan sebelum fitur yang bergantung padanya diimplementasikan atau dinyatakan lulus.
- Verifikasi kemampuan AI/Engine merupakan syarat sebelum modul dimigrasikan atau fitur modul dipanggil; bukan syarat agar siklus MVP dasar berdiri sendiri.
- Pertanyaan komersial, arsitektur rinci, perluasan tim/website, dan KPI numerik dipindahkan ke backlog sesuai waktu kebutuhan pada Pedoman Induk.
- Implementasi dan merge PR tetap membutuhkan persetujuan terpisah.
- Jangan membuat dokumen atau fase baru kecuali ada keputusan konkret yang tidak dapat dicatat di dua dokumen acuan ini.


## 8. Keputusan sumber data dan batas verifikasi

Sumber awal yang disetujui untuk arah produk:

- **WordPress internal:** identitas website, URL, judul, konten, dan hubungan konten yang benar-benar dapat dibaca dengan izin yang tersedia.
- **Google Search Console (GSC):** kueri, klik, tayangan, CTR, posisi, dan halaman pencarian, jika properti dan akses baca-saja telah diverifikasi.
- **Masukan manual:** diperbolehkan untuk menjalankan siklus dasar ketika data otomatis belum tersedia, tetapi harus diberi label sebagai masukan pengguna, bukan hasil pengukuran.

Keputusan ini menetapkan sumber awal secara konseptual, bukan menyatakan koneksi telah dibuat. GSC tidak wajib untuk menguji alur dasar website → temuan → prioritas → tugas → keputusan → hasil; tetapi fitur metrik pencarian tidak boleh dibangun atau dinyatakan lulus sebelum GATE-DATA-01 untuk GSC terbukti. Data yang kosong, usang, parsial, tidak diizinkan, atau gagal diambil harus dibedakan. Tidak boleh mengubah data yang tidak tersedia menjadi nol atau membuat metrik rekaan.

## 9. Alur, status, dan pemulihan minimum

- Setiap temuan harus memiliki asal/sumber, waktu, alasan atau bukti, ketidakpastian, dan status.
- Setiap tugas harus memiliki tujuan, pemilik keputusan/penanggung jawab, kriteria selesai, dependensi bila ada, dan riwayat status.
- Status minimum: Belum dimulai, Siap, Berjalan, Menunggu persetujuan, Terhambat, Gagal, Selesai, Dibatalkan.
- Selesai memerlukan bukti terhadap kriteria selesai. Menunggu persetujuan bukan izin. Terhambat dan Gagal memerlukan alasan serta langkah berikutnya. Dibatalkan memerlukan alasan dan tidak menghapus riwayat.
- Penolakan persetujuan mengembalikan tugas untuk revisi atau membatalkannya. Perubahan material setelah persetujuan memerlukan pemeriksaan/persetujuan ulang.
- Percobaan ulang hanya boleh dilakukan bila aman dan tidak menggandakan tindakan; hasil yang tidak pasti harus diverifikasi sebelum diulang.
- Siklus dasar MVP tetap tidak mengubah atau menerbitkan website secara otomatis.

## 10. Status keputusan dan penutupan pedoman

Semua keputusan konseptual yang diperlukan untuk menetapkan produk dan MVP telah dicatat. Tidak ada keputusan produk terbuka yang memblokir penutupan pedoman. Yang masih wajib sebelum implementasi/fitur terkait adalah bukti teknis: akses lingkungan WordPress testing, akses WordPress internal sesuai cakupan fitur, akses GSC untuk metrik pencarian, serta uji penerimaan. Kekurangan bukti teknis tidak boleh disebut PASS, tetapi tidak membuka kembali keputusan konseptual yang sudah disetujui.