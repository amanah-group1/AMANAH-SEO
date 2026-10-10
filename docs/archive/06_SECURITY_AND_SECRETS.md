> **STATUS: DOKUMEN LAMA / SUPERSEDED (2026-10-10).** Sumber acuan aktif untuk visi, tujuan, batas produk, dan alur utama adalah [Pedoman Induk AMANAH SEO](../PEDOMAN_INDUK_AMANAH_SEO.md). Isi di bawah dipertahankan hanya sebagai catatan historis; tidak boleh mengesampingkan pedoman induk baru. Materi lama hanya boleh diadopsi kembali setelah ditinjau dan disetujui secara eksplisit.

---

> **STATUS: DOKUMEN LAMA / TIDAK BERLAKU SEBAGAI PEDOMAN AKTIF.** Mulai 2026-10-10, visi, tujuan, batas produk, dan alur utama ditentukan oleh [Pedoman Induk AMANAH SEO](../PEDOMAN_INDUK_AMANAH_SEO.md). Dokumen ini dipertahankan untuk penelusuran historis dan tidak boleh dipakai untuk mengesampingkan pedoman induk baru. Jika ada isi yang masih berguna, adopsi kembali secara eksplisit setelah ditinjau; status keputusan lama tidak otomatis menjadi keputusan aktif.

---

# AMANAH SEO — Security and Secrets

**Status Dokumen:** APPROVED — dokumen persyaratan keamanan disetujui sebagai baseline tata kelola; status implementasi kontrol belum diverifikasi  
**Bukti persetujuan baseline:** [commit df3251c44b17cb517e2599ca458e693658517cb8](https://github.com/amanahgroup123/AMANAH-SEO/commit/df3251c44b17cb517e2599ca458e693658517cb8) (2026-10-09).  
**Status Persyaratan:** Baseline untuk audit dan perencanaan; butir yang bergantung pada keputusan arsitektur atau operasi belum otomatis menjadi keputusan final  
**Jenis dokumen:** Security baseline  
**Cakupan:** Platform induk AMANAH SEO dan batas integrasinya dengan produk terpisah

## 1. Tujuan

Dokumen ini menetapkan baseline persyaratan keamanan untuk AMANAH SEO. Persetujuan dokumen berarti baseline ini dapat digunakan sebagai acuan audit dan perencanaan; persetujuan tersebut tidak berarti setiap kontrol telah diputuskan sebagai desain final, diterapkan, atau lulus pengujian. Ketentuan yang bergantung pada pilihan arsitektur, deployment, penyedia identitas, atau model operasi tetap harus diselaraskan dengan keputusan terkait sebelum implementasi. Dokumen ini bukan bukti bahwa kontrol sudah berjalan atau audit keamanan telah lulus.

Tujuan utama:
- Melindungi akun, data, konfigurasi, dan kredensial.
- Mencegah akses tanpa izin dan eskalasi hak akses.
- Memisahkan autentikasi, otorisasi, entitlement, dan aktivasi lisensi.
- Menetapkan persyaratan keamanan untuk integrasi produk.
- Menyediakan bukti yang dapat diaudit sebelum status keamanan dinyatakan PASS.

## 2. Prinsip Keamanan

1. **Least privilege:** setiap pengguna, proses, dan integrasi hanya memperoleh hak minimum yang dibutuhkan.
2. **Deny by default:** akses ditolak kecuali ada aturan yang secara eksplisit mengizinkannya.
3. **Defense in depth:** kontrol keamanan tidak bergantung pada satu lapisan saja.
4. **Server-side enforcement:** keputusan akses dan entitlement yang kritis wajib divalidasi di sisi server.
5. **Secret minimization:** simpan dan sebarkan rahasia sesedikit mungkin.
6. **Fail securely:** kegagalan pemeriksaan keamanan tidak boleh berubah menjadi pemberian akses.
7. **Auditability:** keputusan sensitif harus dapat ditelusuri tanpa membocorkan rahasia.
8. **Evidence-based status:** kontrol tidak boleh dinyatakan PASS tanpa bukti pengujian yang relevan.

## 3. Batas Kepercayaan

Batas berikut merupakan model awal yang masih harus diverifikasi:

- Browser dan perangkat pengguna.
- Platform induk AMANAH SEO.
- Produk AMANAH SEO AI.
- Produk AMANAH SEO ENGINE V2.
- Database dan penyimpanan.
- Layanan AI pihak ketiga.
- Layanan lisensi atau aktivasi eksternal, jika nantinya dipilih.
- Sistem deployment, backup, dan administrasi.

Data yang berasal dari browser, produk terintegrasi, atau layanan eksternal tidak boleh otomatis dianggap tepercaya.

## 4. Perbedaan Identitas dan Hak Akses

Istilah berikut tidak boleh dipertukarkan:

- **Authentication:** memverifikasi identitas pengguna atau sistem.
- **Authorization:** menentukan tindakan dan resource yang boleh diakses.
- **Entitlement:** menentukan fitur atau kapasitas produk yang menjadi hak suatu akun atau lisensi.
- **Activation:** mencatat atau memverifikasi penggunaan lisensi pada instalasi atau lingkungan tertentu.
- **License:** catatan atau perjanjian hak penggunaan produk berdasarkan aturan yang disetujui.
- **Product access:** keputusan akhir apakah suatu operasi produk boleh dijalankan setelah pemeriksaan yang relevan.

Lolos autentikasi tidak otomatis berarti pengguna berhak mengakses semua produk, fitur, atau data.

## 5. Klasifikasi Kredensial dan Rahasia

Rahasia yang perlu diinventarisasi melalui audit meliputi:

- Password dan mekanisme pemulihan akun.
- Session token, cookie autentikasi, dan token akses.
- API key layanan AI, termasuk provider Gemini atau OpenAI jika digunakan.
- Kredensial database.
- Signing key, encryption key, dan token integrasi.
- Kredensial deployment, backup, dan layanan eksternal.

API key provider AI milik pengguna harus diperlakukan sebagai rahasia produk. Kredensial internal platform merupakan kategori berbeda dan tidak boleh disamakan atau dibagikan tanpa kontrak yang disetujui.

Jangan menaruh rahasia di source code, dokumentasi publik, repository Git, pesan diagnostik, atau log biasa.

## 6. Penyimpanan dan Siklus Hidup Rahasia

Persyaratan yang diusulkan:

- Gunakan mekanisme penyimpanan rahasia yang sesuai dengan lingkungan deployment.
- Enkripsi saat transit menggunakan koneksi aman.
- Lindungi rahasia saat tersimpan berdasarkan kemampuan platform dan risiko.
- Batasi siapa atau proses apa yang dapat membaca rahasia.
- Jangan menampilkan nilai rahasia secara utuh setelah disimpan.
- Sediakan mekanisme rotasi dan pencabutan kredensial.
- Hindari menyalin rahasia ke backup atau log yang tidak terlindungi.
- Jangan mengklaim enkripsi aktif sebelum metode, key management, dan pengujiannya diverifikasi.

Password pengguna harus ditangani menggunakan mekanisme hashing password yang sesuai, bukan disimpan sebagai teks biasa.

## 7. Session, CSRF, dan Permintaan

Untuk aplikasi web, audit dan pengujian harus mencakup:

- Cookie session dengan atribut keamanan yang sesuai dengan arsitektur deployment.
- Perlindungan CSRF untuk operasi yang mengubah keadaan.
- Pemeriksaan otorisasi pada setiap operasi sensitif.
- Validasi nonce atau mekanisme anti-CSRF yang sesuai dengan framework.
- Pembatasan brute force dan percobaan login bila relevan.
- Masa berlaku, pembaruan, dan pencabutan session.
- Validasi metode HTTP dan asal permintaan sesuai kebutuhan.

Nonce atau token CSRF tidak menggantikan pemeriksaan capability atau authorization.

## 8. Input, Output, dan Data

Persyaratan yang diusulkan:

- Validasi input berdasarkan skema dan tipe yang diharapkan.
- Terapkan batas ukuran pada input, file, dan respons eksternal.
- Gunakan query database yang aman dan parameterisasi.
- Escape output berdasarkan konteks HTML, atribut, URL, atau JavaScript.
- Jangan mempercayai output AI sebagai HTML atau instruksi yang aman.
- Validasi struktur respons AI sebelum menyimpan atau menampilkannya.
- Hindari memasukkan data sensitif ke prompt atau layanan eksternal tanpa dasar dan persetujuan yang sesuai.
- Gunakan pesan kesalahan yang aman bagi pengguna dan detail teknis terbatas untuk log internal.

## 9. Integrasi Antarproduk

Nama produk yang digunakan dalam dokumen ini:

- AMANAH SEO sebagai platform induk yang direncanakan.
- AMANAH SEO AI sebagai produk terpisah.
- AMANAH SEO ENGINE V2 sebagai produk terpisah.

Ketiganya tidak boleh diasumsikan berbagi session, database, kredensial, atau kewenangan administrator sebelum kontrak dan implementasinya diaudit.

Setiap integrasi harus menentukan:
- Identitas pemanggil dan cara autentikasinya.
- Hak akses minimum.
- Data yang boleh dikirim dan diterima.
- Validasi payload dan versi kontrak.
- Timeout, kegagalan, retry, dan batas permintaan.
- Penanganan secret serta pencatatan audit.
- Perilaku ketika layanan tidak tersedia.

Tidak ada perubahan kode atau runtime kedua produk yang diizinkan hanya berdasarkan dokumen ini.

## 10. Logging dan Audit Events

Log harus membantu diagnosis tanpa membocorkan:
- Password.
- API key atau token lengkap.
- Session cookie.
- Encryption key atau signing key.
- Data pribadi yang tidak dibutuhkan untuk diagnosis.

Peristiwa yang layak dipertimbangkan untuk dicatat:
- Login berhasil atau gagal.
- Perubahan role dan permission.
- Perubahan lisensi, entitlement, dan aktivasi.
- Perubahan kredensial provider AI.
- Penolakan akses terhadap operasi sensitif.
- Perubahan konfigurasi keamanan.
- Aktivitas administratif penting.

Setiap event perlu memiliki identitas peristiwa, waktu, aktor atau sumber yang tersedia, tindakan, hasil, dan correlation ID jika sesuai. Hindari menyimpan isi rahasia di payload audit.

## 11. Backup, Pemulihan, dan Insiden

Sebelum produksi, tetapkan dan uji:
- Jadwal serta retensi backup.
- Perlindungan akses dan kerahasiaan backup.
- Prosedur pemulihan dan bukti uji restore.
- Prosedur pencabutan atau rotasi kredensial.
- Respons terhadap kebocoran secret dan akses tidak sah.
- Pemilik insiden, jalur eskalasi, dan pencatatan tindakan.
- Penilaian dampak serta pemulihan setelah insiden.

Keberadaan file backup saja tidak membuktikan bahwa pemulihan berhasil.

## 12. Security Acceptance Criteria

Status PASS hanya dapat diberikan apabila bukti pengujian menunjukkan bahwa kontrol terkait berfungsi.

Checklist audit:
- [ ] Pengguna tanpa autentikasi ditolak dari resource yang dilindungi.
- [ ] Pengguna dengan role terbatas tidak dapat menjalankan operasi administrator.
- [ ] Entitlement yang tidak aktif tidak memberikan akses fitur berbayar.
- [ ] Perubahan lisensi atau entitlement divalidasi di sisi server.
- [ ] Secret tidak muncul utuh di halaman, respons error, atau log.
- [ ] Permintaan perubahan keadaan memiliki perlindungan CSRF yang sesuai.
- [ ] Input tidak valid ditolak dengan aman.
- [ ] Respons eksternal dan AI divalidasi sebelum dipakai.
- [ ] Kegagalan layanan eksternal tidak membuka akses secara default.
- [ ] Backup dapat dipulihkan melalui prosedur yang diuji.
- [ ] Pengujian keamanan memiliki langkah reproduksi dan bukti yang tersimpan.

Checklist ini belum dijalankan hanya karena dokumen dibuat.

## 13. Bukti Audit

Setiap temuan keamanan sebaiknya mencatat:
- ID temuan dan tingkat risiko.
- Komponen serta versi atau commit yang diperiksa.
- Prasyarat dan langkah reproduksi.
- Hasil yang diharapkan dan hasil aktual.
- Bukti yang telah disamarkan dari secret.
- Dampak dan batas cakupan.
- Status perbaikan serta hasil pengujian ulang.

Jangan menyimpan secret asli di bukti audit.

## 14. Keputusan yang Masih Terbuka

Hal-hal berikut belum diputuskan:
- Mekanisme autentikasi dan penyedia identitas platform.
- Model role dan permission final.
- Topologi deployment dan pengelolaan secret.
- Model isolasi data antarakun dan antarproduk.
- Mekanisme komunikasi serta autentikasi integrasi.
- Kebijakan retensi log dan backup.
- Kebutuhan sertifikasi atau standar keamanan formal.

Keputusan final harus dicatat melalui Decision Register atau Architecture Decision Record (ADR).

## 15. Riwayat Perubahan

| Versi | Status | Perubahan |
|---|---|---|
| 0.1 | DRAFT | Baseline keamanan awal untuk ditinjau |

---

**Peringatan:** status APPROVED berlaku untuk dokumen sebagai baseline persyaratan dan acuan tata kelola. Status itu tidak mengubah usulan atau pertanyaan terbuka menjadi keputusan final, bukan bukti bahwa implementasi sudah aman, dan bukan hasil audit keamanan. Riwayat versi 0.1 berstatus DRAFT adalah catatan historis dan tidak menggantikan status dokumen saat ini.
