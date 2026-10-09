# Gate 0 Verification — Documentation Baseline

**Audit status:** PARTIAL  
**Baseline examined:** `52ec71d83720d82e5a78df18ed0c402b59ece1e9` (`main`)  
**Correction branch:** `controlled/doc-foundation-gate0-verification`  
**Audit date:** 2026-10-09  
**Scope:** Documentation inventory, status conventions, project boundaries, and Markdown path references.  
**Out of scope:** Product source code, databases, runtime, security testing, and integration testing.

## 1. Results

| ID | Criterion | Result | Evidence |
|---|---|---|---|
| G0-01 | All 11 core documents exist | PASS | All paths from `docs/00_PROJECT_CHARTER.md` through `docs/10_OPEN_QUESTIONS.md` were fetched from the baseline. |
| G0-02 | All 4 templates exist | PASS | All four template paths listed in the README correction were fetched successfully. |
| G0-03 | README lists the exact core-document paths | PASS | `README.md`, section 4. |
| G0-04 | README lists all four template paths explicitly | PASS after correction | `README.md`, section 4, correction branch. |
| G0-05 | Platform and product boundaries are explicit | PASS | `README.md`, `docs/00_PROJECT_CHARTER.md`, and `docs/03_PRODUCT_BOUNDARIES.md` distinguish the parent platform from both products and avoid asserting unverified implementation. |
| G0-06 | Decision, document, and audit status concepts are separated | PARTIAL | The Decision Register now explicitly identifies its document status as DRAFT. Template files still use different `Status` values (`PROPOSED`, `OPEN`, `NOT STARTED`); whether these describe template-instance workflow or document status needs a deliberate governance decision. |
| G0-07 | Explicit Markdown references to documentation/template paths resolve | PASS for checked references | 23 references matching `docs/*.md` or `templates/*.md` were extracted from the 16 inventoried files; no missing targets were found. This check does not validate every possible link syntax. |
| G0-08 | Gate 0 inventory criterion matches README | PASS after correction | `docs/08_AUDIT_AND_READINESS.md` now says the 11 core documents and four templates must be explicitly listed in README and exist at the expected paths. |

## 2. Controlled corrections

1. `README.md`: replaced the generic `templates/` entry with the four exact template paths and descriptions.
2. `docs/01_DECISION_REGISTER.md`: added an explicit document status of DRAFT, while preserving individual decision statuses.
3. `docs/08_AUDIT_AND_READINESS.md`: aligned the Gate 0 inventory criterion with the README's explicit template list.
4. This report records the checks, evidence scope, and remaining limitation.

## 3. Remaining item

**G0-FINDING-01 — Template status semantics need clarification.**

The template files currently start with workflow/status values such as `PROPOSED`, `OPEN`, or `NOT STARTED`. These may describe the record that will be created from the template rather than the template document itself. No automatic reinterpretation was made. Governance should decide whether templates need a separate `Status Dokumen: DRAFT` field and a separately named field for the record's lifecycle status.

## 4. Gate decision

**Overall Gate 0: PARTIAL.**

The file inventory and checked path references pass, and the documentation inconsistencies in the approved correction scope have been addressed. Gate 0 is not marked PASS because template status semantics remain unresolved and the reference scan is limited to the explicit Markdown path patterns described above.

No product code, product database, or runtime was changed or tested as part of this documentation-only audit.
