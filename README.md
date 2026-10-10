# AMANAH SEO — Platform Foundation

**Status Dokumen:** APPROVED — disetujui sebagai pedoman fondasi; usulan implementasi dan keputusan komersial tetap memerlukan persetujuan tersendiri  
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

### Audit records

- `docs/audits/FOUNDATION_CONSISTENCY_REVIEW_2026-10-09.md` — consistency review dengan scope terbatas.
- `docs/audits/GATE_0_VERIFICATION_2026-10-09.md` — evidence untuk Gate 0 sesuai scope yang tercatat.
- `docs/audits/PHASE_2_CROSS_DOCUMENT_FINDINGS_2026-10-10.md` — temuan audit silang CDR-001–CDR-008 pada baseline awal.
- `docs/audits/PHASE_2_GATE_CLOSURE_2026-10-10.md` — gate closure historis dan addendum re-audit terbaru.
- `docs/audits/PHASE_1_DOCUMENT_REGISTER_2026-10-10.md` — register inventaris dokumen dan keterbatasan sumber.
- `docs/audits/PHASE_2_CROSS_DOCUMENT_REAUDIT_2026-10-10.md` — revalidasi temuan CDR-001–CDR-012.
- `docs/audits/ROADMAP_PHASE_2_READINESS_ASSESSMENT_2026-10-10.md` — assessment prasyarat resmi Roadmap Phase 2, bukti ASE-MISS-008, disposition CDR-011, dan kriteria Gate 2.
- `docs/audits/APPROVAL_EVIDENCE_REVALIDATION_2026-10-10.md` — verifikasi commit approval individual untuk 11 dokumen inti; tidak mengesahkan keputusan arsitektur atau implementasi.
- `docs/audits/FOUNDATION_REQUIREMENTS_TRACEABILITY_2026-10-10.md` — pemetaan delapan tujuan Charter ke dokumentasi, komponen konseptual, kriteria roadmap, dan status evidence; CDR-011 tetap terbuka untuk traceability implementasi.
- `docs/audits/PHASE_1_GITHUB_AUDIT_AMANAH_SEO_AI_2026-10-10.md` — audit baca-saja GitHub AMANAH SEO AI; temuan AI-F-001–AI-F-004; Gate 1 tetap PARTIAL/BLOCKED.
- `docs/audits/PHASE_1_GITHUB_AUDIT_AMANAH_SEO_ENGINE_V2_2026-10-10.md` — audit baca-saja GitHub ENGINE V2; temuan ENG-F-001–ENG-F-004; Gate 1 tetap PARTIAL/BLOCKED.

### Reusable templates

- `templates/ADR_TEMPLATE.md` — template Architecture Decision Record.
- `templates/AUDIT_FINDING_TEMPLATE.md` — template temuan audit.
- `templates/PRODUCT_AUDIT_CHECKLIST.md` — checklist audit baseline produk.
- `templates/IMPLEMENTATION_PLAN_TEMPLATE.md` — template rencana implementasi.

## 5. Status Conventions

### 5.1 Document status

- `DRAFT` — belum disahkan.
- `IN REVIEW` — sedang ditinjau.
- `APPROVED` — telah disahkan oleh pihak yang berwenang.
- `SUPERSEDED` — digantikan oleh dokumen atau versi baru.
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

Repositori ini berada pada tahap fondasi dokumentasi dan rekonsiliasi audit silang sumber yang dapat diakses. Hasil audit silang dokumentasi adalah **PASS WITH RECORDED OPEN DECISIONS AND SOURCE LIMITATIONS** untuk repository dan Library yang dapat diakses; ini bukan penutupan Roadmap Phase 2 / Gate 2. Assessment resmi kesiapan Phase 2 menyatakan **BLOCKED**: audit Phase 1 kedua produk belum dibuktikan selesai/reviewed dalam scope ini; CDR-011 tetap OPEN (PARTIALLY ADVANCED melalui matriks traceability tingkat fondasi); ASE-MISS-008 telah ditutup untuk bukti approval 11 dokumen inti setelah revalidasi; GitHub `main` ditetapkan sebagai sumber acuan utama; local akan disinkronkan setelah dokumentasi selesai.

Dua laporan audit baca-saja berbasis GitHub telah disusun secara terpisah; keduanya berstatus PARTIAL karena database/runtime live belum diperiksa dan ada prasyarat pengujian yang belum PASS. Belum ada klaim bahwa:
- Kedua produk telah lulus integration readiness audit.
- Platform induk telah diimplementasikan.
- Integration contracts telah final.
- Model komersial telah disahkan.

## 9. Next Steps

1. Pertahankan GitHub `main` sebagai baseline kanonis; setelah dokumentasi dan readiness selesai, pull/sinkronkan baseline tersebut ke local.
2. Tinjau laporan audit Phase 1 untuk AMANAH SEO AI dan AMANAH SEO ENGINE V2; kedua laporan GitHub-only berstatus PARTIAL dan tidak mengklaim runtime/database PASS.
3. Selesaikan disposition CDR-011 untuk traceability persyaratan dan kebutuhan baseline data/UI setelah audit produk; jangan mengasumsikan dokumen terpisah wajib tanpa keputusan.
4. Tinjau laporan audit Phase 1 kedua produk secara terpisah; Gate 1 tetap BLOCKED sampai evidence database/runtime dan validasi yang diperlukan tersedia.
5. Revisit arsitektur/kontrak berdasarkan temuan aktual produk; Gate 2 tidak PASS sebelum kriteria resmi memiliki evidence dan persetujuan.

---

**Governing principle:** Evidence first, explicit approval, controlled scope, and no unapproved changes to existing products.
