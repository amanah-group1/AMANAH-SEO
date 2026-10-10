# Usulan Lingkup MVP AMANAH SEO
**Status:** DISETUJUI PEMILIK SEBAGAI DASAR LINGKUP MVP  
**Tanggal:** 2026-10-11  
**Tujuan:** Mempersempit keputusan agar pedoman dapat selesai tanpa membuat daftar fitur yang terus membesar.

## 1. Rekomendasi inti

MVP harus membuktikan satu siklus nilai dari awal sampai akhir:

**Website → kondisi/temuan → prioritas beralasan → tugas → pemeriksaan/keputusan pemilik → pencatatan hasil → langkah berikutnya.**

Jangan menjadikan MVP sebagai upaya membangun semua fitur SEO, menggantikan produk AI, atau menyalin workflow ENGINE. Jika suatu kemampuan tidak diperlukan untuk menyelesaikan siklus ini, tempatkan setelah MVP.

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
- Ketergantungan wajib pada AMANAH SEO AI atau ENGINE V2.
- Penerbitan otomatis, perubahan massal, atau tindakan SEO berisiko tanpa persetujuan.
- Integrasi dengan banyak penyedia data sekaligus sebelum satu sumber dipilih dan diverifikasi.
- Portal pelanggan, tim/organisasi kompleks, marketplace, payment gateway, billing AI, atau sistem komersial penuh.
- Klaim ranking, skor kualitas yang tidak dapat dijelaskan, atau metrik yang tidak memiliki sumber terverifikasi.
- Analitik lanjutan yang tidak dibutuhkan untuk siklus utama.

Ini adalah usulan batas MVP, bukan pernyataan bahwa semua fungsi wajib di atas sudah tersedia atau telah diimplementasikan.

## 4. Keputusan MVP yang telah disetujui pemilik

| ID | Keputusan | Rekomendasi | Status |
|---|---|---|---|
| MVP-01 | Platform website pertama | WordPress sebagai platform pertama; keputusan dicatat setelah persetujuan pemilik. | DISETUJUI 2026-10-11 |
| MVP-02 | Cakupan awal | Satu website per ruang kerja/pengaturan pada MVP, dengan rancangan tidak menghalangi perluasan kemudian. | DISETUJUI 2026-10-11 |
| MVP-03 | Sumber data wajib | Mulai dengan sumber data paling kecil yang dapat dibuktikan tersedia; jangan membuat integrasi multi-provider sebagai syarat awal. Nama sumber spesifik tetap diverifikasi sebelum implementasi. | DISETUJUI 2026-10-11 (prinsip); sumber spesifik menunggu verifikasi |
| MVP-04 | Pelaksanaan tindakan | Semua perubahan website dan publikasi membutuhkan persetujuan eksplisit; MVP hanya mengatur tugas dan hasil, bukan melakukan tindakan otonom. | DISETUJUI 2026-10-11 |
| MVP-05 | Akun dan akses | Minimal pengguna terautentikasi dengan hak akses pemilik/admin yang jelas; multi-tenant/tim kompleks ditunda. | DISETUJUI 2026-10-11 |
| MVP-06 | Integrasi AI/ENGINE | Opsional; tidak menjadi prasyarat MVP. Kontrak integrasi disusun hanya jika fungsi MVP benar-benar membutuhkannya. | DISETUJUI 2026-10-11 |
| MVP-07 | Keberhasilan MVP | Siklus utama selesai, setiap rekomendasi memiliki alasan/sumber atau label ketidakpastian, persetujuan tercatat, dan kegagalan terlihat. Tetapkan target angka setelah baseline tersedia. | DISETUJUI 2026-10-11 |

## 5. Catatan persetujuan pemilik

Pada 2026-10-11, pemilik menyetujui ketujuh rekomendasi MVP-01 sampai MVP-07 sebagai dasar pedoman AMANAH SEO. Persetujuan MVP-03 mencakup prinsip memilih sumber data terkecil yang dapat diverifikasi, bukan pemilihan nama penyedia tertentu. Sumber data spesifik tetap harus diverifikasi sebelum implementasi. Persetujuan ini menetapkan arah produk dan lingkup MVP; bukan persetujuan untuk implementasi, perubahan kode/runtime, atau penggabungan pull request.

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

- Prinsip sumber data terkecil yang dapat diverifikasi disetujui; nama sumber tertentu tetap harus dipilih dan dibuktikan sebelum implementasi.
- Verifikasi kemampuan AI/ENGINE merupakan syarat sebelum integrasi tertentu dipilih, bukan syarat untuk MVP berdiri sendiri.
- Pertanyaan komersial, arsitektur rinci, perluasan tim/website, dan KPI numerik dipindahkan ke backlog sesuai waktu kebutuhan pada Pedoman Induk.
- Implementasi dan merge PR tetap membutuhkan persetujuan terpisah.
- Jangan membuat dokumen atau fase baru kecuali ada keputusan konkret yang tidak dapat dicatat di dua dokumen acuan ini.
