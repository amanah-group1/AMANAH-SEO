# AMANAH SEO — Platform Foundation

**Status Dokumen:** DRAFT — pending review  
**Documentation Language:** Bahasa Indonesia dengan istilah teknis standar dalam bahasa Inggris.

## 1. Project Overview

AMANAH SEO direncanakan sebagai platform induk untuk mengelola produk-produk SEO dalam satu ekosistem yang terintegrasi, dengan tetap mempertahankan independensi setiap produk.

Platform induk direncanakan menyediakan shared capabilities seperti:

- Central dashboard dan product catalog.
- Account, access control, dan security management.
- License, entitlement, dan website activation management.
- Integration management dan operational diagnostics.
- Navigation dan akses ke produk berdasarkan entitlement.

Seluruh kapabilitas tersebut merupakan target arsitektur, bukan klaim bahwa implementasinya sudah tersedia.

## 2. Products in Scope

### AMANAH SEO AI

Produk untuk AI-assisted SEO intelligence. Kapabilitas aktual dan batas integrasinya harus dikonfirmasi melalui audit dokumentasi, source code, automated tests, dan runtime.

### AMANAH SEO ENGINE V2

Produk untuk structured SEO workflow. Kapabilitas aktual, workflow, dan integration points harus dikonfirmasi melalui audit tersendiri.

### AMANAH SEO Platform

Platform induk yang direncanakan mengelola shared capabilities dan mengintegrasikan kedua produk melalui kontrak yang disetujui.

## 3. Non-Negotiable Principles

1. Audit before implementation.
2. Tidak mengubah repositori AMANAH SEO AI dan AMANAH SEO ENGINE V2 selama penyusunan fondasi ini.
3. Setiap produk mempertahankan domain dan lifecycle-nya.
4. Tidak melakukan integrasi sebelum readiness audit selesai dan disetujui.
5. Keputusan yang disahkan harus dibedakan dari usulan, asumsi, dan pertanyaan terbuka.
6. Authorization wajib ditegakkan server-side.
7. License, activation token, AI provider API key, dan internal service credentials harus diperlakukan sebagai konsep yang berbeda.
8. Perubahan implementasi membutuhkan scope, acceptance criteria, test plan, dan approval.
9. Fungsi yang telah PASS harus dilindungi dari regression.
10. Harga, tier, website limits, dan kebijakan komersial tidak dianggap final tanpa persetujuan eksplisit.

## 4. Repository Structure

- `docs/00_PROJECT_CHARTER.md` — project vision, objectives, scope, dan principles.
- `docs/01_DECISION_REGISTER.md` — keputusan, usulan, asumsi, dan status persetujuan.
- `docs/02_ARCHITECTURE_BLUEPRINT.md` — target architecture.
- `docs/03_PRODUCT_BOUNDARIES.md` — product responsibilities dan boundaries.
- `docs/04_INTEGRATION_CONTRACTS.md` — integration contract principles.
- `docs/05_LICENSE_AND_ENTITLEMENTS.md` — license dan entitlement design.
- `docs/06_SECURITY_AND_SECRETS.md` — security baseline dan secrets management.
- `docs/07_DOCUMENT_GOVERNANCE.md` — document control dan change governance.
- `docs/08_AUDIT_AND_READINESS.md` — audit methodology dan readiness criteria.
- `docs/09_ROADMAP_AND_GATES.md` — proposed phases dan approval gates.
- `docs/10_OPEN_QUESTIONS.md` — keputusan yang belum dibuat.
### Reusable templates

- `templates/ADR_TEMPLATE.md` — template Architecture Decision Record.
- `templates/AUDIT_FINDING_TEMPLATE.md` — template temuan audit.
- `templates/PRODUCT_AUDIT_CHECKLIST.md` — checklist audit baseline produk.
- `templates/IMPLEMENTATION_PLAN_TEMPLATE.md` — template rencana implementasi.

## 5. Status Conventions

### 5.1 Document status

- `DRAFT` — belum disahkan.
- `IN_REVIEW` — sedang ditinjau.
- `APPROVED` — telah disahkan oleh pihak yang berwenang.
- `SUPERSEDED` — digantikan oleh dokumen atau versi baru.
- `DEPRECATED` — tidak direkomendasikan untuk penggunaan baru.
- `ARCHIVED` — disimpan sebagai rekaman historis.

### 5.2 Decision status

Gunakan status keputusan yang didefinisikan di `docs/01_DECISION_REGISTER.md`: `CONFIRMED`, `PROPOSED`, `OPEN`, `ASSUMPTION`, dan `SUPERSEDED`. Status ini bukan status dokumen.

### 5.3 Audit status

Gunakan status hasil audit yang didefinisikan di `docs/08_AUDIT_AND_READINESS.md`: `PASS`, `PARTIAL`, `BLOCKED`, `FAIL`, `NOT TESTED`, dan `NOT APPLICABLE`.

`VERIFIED` menjelaskan klaim atau evidence yang telah diverifikasi; ini bukan status dokumen atau keputusan.

## 6. Audit and Integration Policy

Setiap produk akan diaudit secara terpisah. Audit harus mencatat repository URL, baseline commit SHA, dokumentasi utama, test results, runtime evidence, security findings, data ownership, dan integration feasibility.

Tidak ada hasil audit yang boleh dinyatakan PASS atau READY FOR INTEGRATION tanpa evidence yang memadai.

Integrasi hanya dimulai setelah:
1. Audit selesai dan direview.
2. Findings dan risiko memiliki disposition.
3. Target architecture dan integration contracts disetujui.
4. Implementation plan serta acceptance criteria disetujui.

## 7. Commercial Decisions

Model lisensi, tier, website limits, harga, renewal, dan bundle masih memerlukan keputusan bisnis eksplisit.

Setiap angka atau model komersial yang pernah dibahas harus dianggap sebagai usulan sampai dicatat sebagai keputusan yang disahkan.

## 8. Current Status

Repositori ini sedang berada pada tahap foundation documentation.

Belum ada klaim bahwa:
- Kedua produk telah lulus integration readiness audit.
- Platform induk telah diimplementasikan.
- Integration contracts telah final.
- Model komersial telah disahkan.

## 9. Next Steps

1. Review Project Charter.
2. Review dan sahkan Decision Register.
3. Audit AMANAH SEO AI dan AMANAH SEO ENGINE V2 secara terpisah tanpa mengubah keduanya.
4. Perbarui architecture blueprint berdasarkan audit evidence.
5. Sahkan integration contracts sebelum implementation planning.

---

**Governing principle:** Evidence first, explicit approval, controlled scope, and no unapproved changes to existing products.
