# Implementation Plan — [PLAN-ID]

**Status Dokumen:** DRAFT  
**Status Rencana:** PROPOSED — belum disetujui untuk implementasi  
**Tanggal:** [YYYY-MM-DD]  
**Pemilik:** [Name or Role]  
**Terkait:** [Audit finding / ADR / Issue / Requirement]

## 1. Tujuan

[Jelaskan masalah yang akan diselesaikan dan hasil yang diharapkan.]

## 2. Bukti dan Baseline

- **Repository:** [Repository]
- **Branch:** [Branch]
- **Commit awal:** [Exact commit hash]
- **Working tree:** [Clean / Changes present]
- **Lingkungan target:** [Environment]
- **Audit referensi:** [Finding IDs and evidence]

Jangan memulai implementasi sebelum baseline dan kondisi awal diverifikasi.

## 3. Ruang Lingkup

**Termasuk:**
- [Specific change]
- [Specific test or documentation change]

**Tidak termasuk:**
- [Explicit exclusion]
- [Unrelated product or component]

## 4. Kondisi Saat Ini

[Jelaskan perilaku aktual berdasarkan bukti audit.]

## 5. Perilaku yang Diharapkan

[Jelaskan perilaku target yang dapat diuji.]

## 6. Rencana Perubahan

Untuk setiap perubahan, jelaskan lokasi dan alasannya.

| No. | File atau komponen | Perubahan yang direncanakan | Alasan | Risiko |
|---|---|---|---|---|
| 1 | [Path] | [Change] | [Evidence or requirement] | [Risk] |
| 2 | [Path] | [Change] | [Evidence or requirement] | [Risk] |

Tambahkan baris hanya setelah file dan dampaknya diperiksa.

## 7. Database dan Konfigurasi

- **Perubahan skema:** [None / Details]
- **Migrasi:** [None / Plan]
- **Konfigurasi:** [None / Plan]
- **Secret:** [Handling requirements]
- **Backup atau pemulihan:** [Requirements]

Jangan mencatat nilai rahasia aktual dalam dokumen ini.

## 8. Urutan Implementasi

1. [Precondition and baseline verification]
2. [Smallest scoped change]
3. [Unit or component tests]
4. [Integration or runtime tests]
5. [Regression verification]
6. [Documentation and evidence update]

Urutan final harus sesuai dengan dependensi yang ditemukan saat audit.

## 9. Rencana Pengujian

| ID | Skenario | Hasil yang diharapkan | Bukti | Status |
|---|---|---|---|---|
| T-001 | [Positive case] | [Expected result] | [Test reference] | NOT TESTED |
| T-002 | [Negative case] | [Expected result] | [Test reference] | NOT TESTED |
| T-003 | [Regression case] | [Expected result] | [Test reference] | NOT TESTED |

Jangan menyatakan tes lulus sebelum hasil aktual diperiksa.

## 10. Kriteria Penerimaan

- [ ] Perubahan memenuhi persyaratan yang disetujui.
- [ ] Ruang lingkup tidak melebar tanpa persetujuan.
- [ ] Tes yang diwajibkan lulus.
- [ ] Fitur terkait yang sebelumnya PASS tetap PASS.
- [ ] Tidak ada secret dalam source code, log, atau bukti.
- [ ] Dampak database dan konfigurasi ditangani.
- [ ] Dokumentasi diperbarui.
- [ ] Commit dan hasil akhir tercatat.

## 11. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi | Bukti yang diperlukan |
|---|---|---|---|
| [Risk] | [Impact] | [Mitigation] | [Evidence] |

## 12. Rollback

- **Pemicu rollback:** [Conditions]
- **Langkah rollback:** [Procedure]
- **Pemulihan database:** [Procedure or N/A]
- **Verifikasi setelah rollback:** [Tests]
- **Penanggung jawab:** [Name or Role]

Rollback harus realistis dan mempertimbangkan perubahan data yang tidak dapat dibalik.

## 13. Persetujuan

- **Audit selesai:** [YES / NO]
- **Ruang lingkup ditinjau:** [YES / NO]
- **Rencana pengujian disetujui:** [YES / NO]
- **Persetujuan implementasi:** [PENDING / APPROVED / REJECTED]
- **Pihak yang menyetujui:** [Name or Role]
- **Tanggal:** [YYYY-MM-DD]

**Implementasi hanya boleh dimulai setelah persetujuan eksplisit untuk ruang lingkup ini tercatat.**

## 14. Hasil Akhir

Isi setelah pekerjaan dilaksanakan:

- **Commit implementasi:** [Commit hash]
- **Hasil pengujian:** [PASS / PARTIAL / FAIL / NOT TESTED]
- **Bukti:** [References]
- **Penyimpangan dari rencana:** [None / Details]
- **Risiko tersisa:** [Details]
- **Status penutupan:** [OPEN / COMPLETE / BLOCKED]

---
**Aturan:** persetujuan rencana bukan bukti bahwa implementasi berhasil. Status akhir harus didasarkan pada hasil pengujian aktual.
