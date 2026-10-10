# Phase 1 GitHub Baseline Audit — AMANAH SEO AI

**Audit ID:** ASE-P1-AUDIT-AI-001  
**Status:** PARTIAL — GitHub source/documentation review completed; official product Gate 1 remains BLOCKED pending database/runtime evidence and executable validation  
**Audit date:** 2026-10-10  
**Canonical repository:** [amanahgroup123/AMANAH-SEO-AI](https://github.com/amanahgroup123/AMANAH-SEO-AI)  
**Branch / commit:** `main` / `ab8e18c5e23e0d16f79115619955b0f4f00bf4b6`  
**Authorization:** Explicit owner approval in conversation on 2026-10-10 for separate read-only Phase 1 audits  
**Change policy:** No source, test, database, configuration, or runtime files were changed.

## 1. Scope Lock

### Included
- Canonical GitHub `main` snapshot and repository structure.
- Master documentation and control/status documents.
- Read-only inspection of representative critical paths: plugin bootstrap, Entity Profile administration/CRUD/REST, AI settings and credential handling, Content Analyzer request/validation/persistence, installer schema declarations, PHPUnit configuration, selected tests, and CI workflow definitions.
- Evidence classification for functionality, security-relevant handling, test coverage, and documentation drift.

### Excluded
- Any source/test/architecture implementation or refactor.
- Live WordPress, live database contents, installed plugin copy, API calls with user credentials, and local filesystem/runtime verification.
- Running tests or triggering CI workflows.
- Cross-product integration design or changes to AMANAH SEO ENGINE V2.

**Important boundary:** This is a GitHub evidence audit, not a claim that the live WordPress runtime or database passed. Those items remain NOT TESTED.

## 2. Baseline and Documents Reviewed

### Repository snapshot
- Default branch: `main`
- HEAD: `ab8e18c5e23e0d16f79115619955b0f4f00bf4b6`
- Tracked file count reported by GitHub tree: 131
- Test files under `tests/`: 20
- Runtime plugin source is under `plugin/`; the repository root is the development/control repository.

### Control and master documentation
- [PROJECT_RULES.md](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/PROJECT_RULES.md)
- [PROJECT_STATUS.md](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/PROJECT_STATUS.md)
- [NEXT_TASK.md](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/NEXT_TASK.md)
- PRD, Technical Architecture, Database Design, Security & Privacy, REST API, Testing & QA, User Role & Permission, Product Roadmap, Technical Blueprint, and Implementation Control documents under `00_MASTER_DOCUMENTATION/`
- [TQ-007 AI Engine Implementation Contract](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/00_MASTER_DOCUMENTATION/TQ-007_AI_ENGINE_IMPLEMENTATION_CONTRACT.md)
- [TQ-008 Credential Encryption Decision](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/00_MASTER_DOCUMENTATION/TQ-008_AI_API_CREDENTIAL_ENCRYPTION.md)
- [CORE-002 Database Decision](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/00_MASTER_DOCUMENTATION/CORE-002_DATABASE_DECISION.md)
- [CORE-003 Test Coverage Audit](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/00_MASTER_DOCUMENTATION/CORE-003_TEST_COVERAGE_AUDIT.md)
- [CORE-006 Content Analyzer Reconciliation Addendum](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/00_MASTER_DOCUMENTATION/CORE-006_CONTENT_ANALYZER_RECONCILIATION_ADDENDUM_v1.0.md)
- [Content Analysis → Generation Requirements Contract](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/00_MASTER_DOCUMENTATION/CONTENT_ANALYSIS_TO_GENERATION_REQUIREMENTS_CONTRACT.md)
- [Canonical Plugin Source Boundary](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/00_MASTER_DOCUMENTATION/REPO-STRUCTURE-001_CANONICAL_PLUGIN_SOURCE_BOUNDARY.md)

### Critical source/test paths reviewed
- [Plugin bootstrap](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/amanah-seo-ai.php)
- [Plugin core loader](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/includes/class-plugin-core.php)
- [Entity menu/controller/model/view/REST controller](https://github.com/amanahgroup123/AMANAH-SEO-AI/tree/main/plugin/modules/entity)
- [AI settings controller](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/admin/class-ai-settings-controller.php)
- [AI credential encryption](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/modules/ai/class-ai-credentials.php)
- [Content Analyzer](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/modules/content/class-content-analyzer.php)
- [Content Analysis repository](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/modules/content/class-wordpress-content-analysis-repository.php)
- Selected tests: EntityControllerTest, EntityPermissionsTest, AISettingsControllerTest, AICredentialEncryptionTest, ContentAnalysisContractTest
- [PHPUnit configuration](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/phpunit.xml.dist) and the CORE-003 / CORE-006 workflows under `.github/workflows/`

## 3. Findings

### AI-F-001 — Entity name links to an unregistered admin page slug

**Severity:** HIGH — confirmed functional blocker  
**Evidence:**
- [Entity list view](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/modules/entity/views/entity-form.php) builds entity links with `admin.php?page=amanah-seo-ai&entity_id=...`.
- [Entity admin registration](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/modules/entity/class-entity-admin.php) registers the page slug `amanah-entity-profile`.
- [Top-level admin menu](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/admin/class-admin-menu.php) registers `amanah-seo-ai-settings`; the reviewed source does not register `amanah-seo-ai` as an admin page slug.

**Impact:** Clicking an entity name can route to a WordPress admin page that the plugin has not registered. This aligns with the reported “Sorry, you are not allowed to access this page” symptom; runtime reproduction was not performed in this audit.

**Disposition:** Confirmed code-level defect. Do not patch during this audit. Requires a separately scoped correction with route and regression tests.

### AI-F-002 — License key is persisted in the database without an encryption layer in the reviewed save path

**Severity:** MEDIUM — security handling risk / contract decision required  
**Evidence:**
- [Settings controller](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/admin/class-ai-settings-controller.php) writes the submitted `license_key` directly to the `amanah_license` table in `save_license()`.
- [Installer schema](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/includes/class-install.php) declares `license_key varchar(255)` in that table.
- AI provider credentials follow a separate encryption path in [AI Credentials](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/plugin/modules/ai/class-ai-credentials.php). The reviewed license save/read path does not use that class or an equivalent encryption wrapper.

**Impact:** The database stores the license key in readable form to any principal or backup process with database access. Product documentation describes license data retention but does not clearly establish an encryption requirement for license keys; therefore this is recorded as a risk and contract gap, not as a claim that an explicit product requirement was violated.

**Disposition:** Keep open for security/contract review. No storage change was made.

### AI-F-003 — Current operational status documents lag the code on `main`

**Severity:** MEDIUM — governance/evidence reliability  
**Evidence:**
- [PROJECT_STATUS.md](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/PROJECT_STATUS.md) and [NEXT_TASK.md](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/NEXT_TASK.md) still describe CORE-005 as pending implementation/runtime verification and refer to controlled branch/PR work.
- The inspected `main` snapshot already contains Entity Profile controller, model, view, REST controller, Authority module, and related tests.

**Impact:** A reader cannot safely use those status files alone to infer which parts of CORE-005 are present in the canonical branch or which gates remain open. Runtime validation is still not established by the presence of code.

**Disposition:** Status documentation should be reconciled only through a separate documentation correction; no status was changed during this audit.

### AI-F-004 — Settings controller tests do not establish end-to-end save behavior

**Severity:** MEDIUM — test coverage gap  
**Evidence:**
- [AISettingsControllerTest.php](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/tests/Unit/AISettingsControllerTest.php) tests provider names, optional model behavior, and simple local values; the reviewed test does not instantiate `AMANAH_SEO_AI_AI_Settings_Controller` or exercise its save/redirect/persistence path.
- [AICredentialEncryptionTest.php](https://github.com/amanahgroup123/AMANAH-SEO-AI/blob/main/tests/Unit/AICredentialEncryptionTest.php) does exercise the credential wrapper with a mocked settings store, but it does not validate the real database implementation.

**Impact:** Passing these individual assertions would not prove that the full settings POST flow correctly validates, persists, and redirects under WordPress. This is a coverage statement, not a claim that the save path fails.

**Disposition:** Record as a test-evidence gap; do not expand the test suite within this audit.

## 4. Baseline Assessment

| Area | Evidence-based result | Limitation |
|---|---|---|
| Repository/source boundary | DOCUMENTED; plugin runtime source resides in `plugin/` | No local checkout or installed copy compared |
| Entity Profile CRUD/permissions | Relevant controller/model/REST/menu paths exist | Entity link route mismatch is confirmed; no live reproduction |
| AI provider credentials | Sodium secretbox + WordPress auth salt-derived key implemented in reviewed path | Rotation behavior and live configuration not tested |
| Content Analyzer | Input validation, structured contract validation, sanitization, persistence, failure traceability present in reviewed path | No provider call or live DB write executed |
| Tests | 20 test files and PHPUnit configuration present | Tests not run by this audit; current-main CI result not evidenced by available status endpoint |
| Database | Schema declarations and installer path reviewed | Live table definitions, data integrity, migration result, and backups NOT TESTED |
| Runtime / WordPress | NOT TESTED | Explicitly outside GitHub-only evidence available in this pass |

## 5. Audit Disposition

**GitHub source/documentation review:** COMPLETE FOR THE BOUNDED PATHS LISTED ABOVE.  
**Official Phase 1 product audit / Gate 1:** **NOT PASS / BLOCKED — PARTIAL EVIDENCE ONLY.**

Reasons:
1. Live WordPress runtime and live database state have not been inspected.
2. PHPUnit was not executed in this read-only pass; no passing result is claimed.
3. The Entity Profile route mismatch remains uncorrected by design.
4. License-key storage handling requires a recorded security/contract disposition.

## 6. Non-Claims and Stop Boundary

- No source code, tests, database, configuration, or runtime was modified.
- No product feature or integration is declared PASS based solely on source presence.
- No architecture or licensing contract was finalized.
- No audit of AMANAH SEO ENGINE V2 is included in this report; it is recorded separately.
- Further work is limited to reviewing this report and, only after separate approval, defining any correction or local/runtime validation package.
