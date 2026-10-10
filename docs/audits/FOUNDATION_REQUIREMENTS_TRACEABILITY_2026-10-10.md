# Foundation Requirements Traceability Baseline

**Status Dokumen:** IN REVIEW — matriks traceability tingkat fondasi; bukan spesifikasi implementasi final  
**Tanggal:** 2026-10-10  
**Repository acuan:** `amanahgroup123/AMANAH-SEO`, branch `main`  
**Baseline diperiksa:** `165835d49caa87fb4923f254adb411b21cfda844`  
**Scope:** Memetakan delapan tujuan eksplisit Project Charter ke sumber dokumentasi, tanggung jawab komponen konseptual, kriteria penerimaan roadmap, dan status evidence yang tersedia  
**Excluded:** Menetapkan kebutuhan baru, menyetujui opsi arsitektur/komersial, menetapkan schema/UI final, menjalankan tes produk, atau mengaudit/mengubah produk turunan

## 1. Aturan Interpretasi

- ID `FTR-001` sampai `FTR-008` adalah label traceability untuk delapan tujuan yang sudah ada pada Project Charter §3; bukan requirement baru.
- Nama komponen pada kolom arsitektur adalah kandidat konseptual dari Architecture Blueprint, bukan batas implementasi yang telah disahkan.
- Kriteria roadmap adalah kriteria dokumentasi/readiness, bukan bukti bahwa implementasi atau tes runtime telah lulus.
- `DOCUMENTED` berarti ada sumber dokumentasi yang eksplisit; tidak berarti sudah diimplementasikan.
- `PARTIAL` berarti hanya bagian konseptual atau sebagian relasi yang dapat dipetakan.
- `BLOCKED` berarti pemetaan final membutuhkan hasil audit produk atau keputusan yang belum disetujui.
- `NOT TESTED` berarti tidak ada hasil implementasi/runtime yang dapat diklaim pada scope ini.

## 2. Matriks Traceability Fondasi

| ID | Sumber kebutuhan yang sudah ada | Tanggung jawab komponen konseptual | Kriteria penerimaan yang sudah ada | Evidence / status sekarang | Tindak lanjut yang sah |
|---|---|---|---|---|---|
| FTR-001 | Charter §3.1: rancangan platform induk dan batas tanggung jawab | Parent Platform; Dashboard/Admin UI; Product Registry (Blueprint §5.1) | Roadmap Phase 0 §3: batas platform dan kedua produk dijelaskan tanpa klaim implementasi | DOCUMENTED pada tingkat arah dan scope; implementasi NOT TESTED | Pertahankan sebagai batas fondasi; finalisasi komponen setelah audit dan keputusan arsitektur |
| FTR-002 | Charter §3.2 dan §4.2–§4.3: hubungan terkontrol dengan dua produk yang tetap terpisah | Product Registry; Integration Boundary; adapter/entry point sebagai kandidat | Roadmap Phase 1 §4 dan Phase 5 §8: audit tiap produk, kontrak kedua sisi, kompatibilitas, skenario positif/negatif, bukti uji | PARTIAL; prinsip batas produk terdokumentasi, tetapi audit produk dan bukti kompatibilitas BLOCKED / NOT EVIDENCED | Tunggu otorisasi eksplisit dan hasil audit terpisah AMANAH SEO AI serta AMANAH SEO ENGINE V2 |
| FTR-003 | Charter §3.3: dasar akun, produk, lisensi, aktivasi, entitlement | Identity and Access; Product Registry; License and Entitlement (Blueprint §5.2–§5.4) | Roadmap Phase 2 §5 dan Phase 3 §6: keputusan dicatat, kontrak didasarkan pada temuan, scope dan tes disetujui | PARTIAL; tanggung jawab konseptual ada, tetapi Q-001–Q-013 tetap OPEN dan model final belum diputuskan | Jangan menetapkan schema atau lifecycle final sebelum keputusan dan audit yang menjadi dependensi tersedia |
| FTR-004 | Charter §3.4: keamanan, secrets, authorization, audit | Identity and Access; Integration Boundary; Audit Trail/Diagnostics (Blueprint §3 dan §5) | Roadmap Phase 4 §7 dan Phase 6 §9: akses tidak sah ditolak, secret ditangani sesuai kebijakan, pemeriksaan keamanan wajib lulus | PARTIAL; baseline keamanan dan kriteria ada, tetapi threat-model review berbasis temuan produk serta hasil kontrol runtime belum dibuktikan | Susun threat model setelah alur, data, deployment, dan dependensi aktual dipetakan |
| FTR-005 | Charter §3.5 dan §10: audit sebelum keputusan implementasi | Governance; Audit and Readiness; roadmap gates | Roadmap Phase 1 §4 / Gate 1 dan Phase 3 §6 / Gate 3 | DOCUMENTED sebagai proses; dua audit produk belum diotorisasi/dibuktikan selesai dalam assessment ini | Dapatkan persetujuan eksplisit untuk audit produk sebelum menjalankan audit |
| FTR-006 | Charter §3.6: integration contracts ditinjau sebelum implementasi | Integration Boundary dan kontrak lintas produk | Roadmap Phase 2 §5 / Gate 2 dan Phase 5 §8 | PARTIAL; kebutuhan kontrak terdokumentasi, tetapi kontrak teknis masih PROPOSED dan belum berbasis temuan audit produk | Refine kontrak setelah audit Phase 1; jangan menganggap kontrak kandidat sebagai kontrak final |
| FTR-007 | Charter §3.7: acceptance criteria dan readiness gates yang dapat dibuktikan | Audit and Readiness; test evidence; governance perubahan | Roadmap Phase 0–6 memiliki kriteria/gate; Audit and Readiness §3 menetapkan status hasil audit | DOCUMENTED pada tingkat proses/kriteria; tidak ada klaim tes runtime produk atau parent-platform implementation yang lulus | Saat scope implementasi disetujui, turunkan kriteria menjadi test cases yang dapat dijalankan dan simpan hasilnya |
| FTR-008 | Charter §3.8 dan §5.2: perubahan pada produk yang berjalan memerlukan persetujuan terpisah | Change Governance; product boundaries; integration adapters (kandidat) | Roadmap Phase 3 §6 dan Phase 5 §8: scope eksplisit, regresi, persetujuan, rollback, bukti pengujian | DOCUMENTED sebagai aturan non-scope/governance; tidak ada perubahan produk dilakukan | Pertahankan batas larangan sampai otorisasi terpisah dan scope perubahan yang disetujui tercatat |

## 3. Hasil Penilaian

- Kedelapan tujuan Charter sekarang memiliki tautan dokumentasi, kandidat tanggung jawab konseptual, kriteria penerimaan roadmap, dan status evidence yang dinyatakan eksplisit.
- Matriks ini memperbaiki traceability **kebutuhan fondasi → dokumen/komponen kandidat → kriteria roadmap**, tetapi belum membuktikan traceability end-to-end ke komponen implementasi dan test cases yang dapat dijalankan.
- Belum ada dasar untuk menyatakan acceptance tests implementasi lulus: platform induk belum masuk implementasi berdasarkan dokumen, dan audit teknis kedua produk belum dilakukan dalam scope ini.
- Detail data model dan UI/navigation tetap dicatat sebagai gap scope (ASE-MISS-005/006); matriks ini tidak menyimpulkan bahwa dokumen data/UI terpisah wajib dibuat.
- Q-001–Q-028 tetap mengikuti status masing-masing di Open Questions. Q-028 ditambahkan setelah baseline matriks ini disusun; pencantumannya di sini hanya memperbarui rujukan status dan tidak memperluas cakupan matriks menjadi test-case traceability end-to-end. Matriks ini tidak menutup atau mengubah status pertanyaan.

## 4. Disposition CDR-011

**CDR-011 tetap OPEN — PARTIALLY ADVANCED.**

Bagian yang kini terdokumentasi: hubungan antara delapan tujuan Charter, sumber dokumentasi, kandidat tanggung jawab konseptual, dan kriteria roadmap.

Bagian yang belum dapat diselesaikan:
1. Requirements-to-component-to-executable-acceptance-test traceability pada tingkat implementasi.
2. Kebutuhan dan batas data model berdasarkan audit schema aktual.
3. Kebutuhan UI/navigation inventory berdasarkan alur produk dan scope MVP.
4. Kontrak integrasi final yang didasarkan pada evidence audit kedua produk.
5. Keputusan eksplisit untuk menyelesaikan atau menunda pertanyaan kritis yang menjadi dependensi Gate 2.

**Syarat penutupan CDR-011:** setelah audit produk diotorisasi dan ditinjau, perbarui matriks dengan temuan dan komponen yang benar-benar ada, tentukan test cases yang sesuai, lalu catat keputusan eksplisit apakah data/UI baselines menjadi dokumen terpisah atau bagian dari dokumen yang ada.

## 5. Batas dan Non-Claims

Dokumen ini bukan approval Gate 2, bukan izin implementasi, dan bukan otorisasi audit produk. Tidak ada source code, database, konfigurasi, atau runtime AMANAH SEO AI maupun AMANAH SEO ENGINE V2 yang diperiksa atau diubah.
