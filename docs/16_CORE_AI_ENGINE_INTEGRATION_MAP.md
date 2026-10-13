# AMANAH SEO Core + AI + ENGINE Integration Map

Status: controlled integration work in progress; this document is not release approval.
Integration branch: `integration/core-ai-engine-20261012`
Core base: `5be9b8803ec9c92e759bcb4f887baaa36d844281`
AI source baseline: `cd83b80fc7030651cee020391081279a9a36e27f`
ENGINE source baseline (main): `80e7c38a327baeef1d7ad7b13d006d453d5838b1`
ENGINE release-gate fix evidence: commit `3cbda06ce1b622926f2f494674e855f2ee9474f2`, run https://github.com/amanahgroup123/amanah-seo-engine-v2/actions/runs/38224963086 (PHP 7.4 and 8.2 passed).

## Ownership and integration boundary

| Area | Owning source / paths | Integration rule |
|---|---|---|
| Product entry, lifecycle, Core menu, existing Core storage | Core `plugin/amanah-seo.php`, `plugin/includes/class-amanah-seo-activator.php`, `class-amanah-seo-admin.php`, `class-amanah-seo-store.php`, `class-amanah-seo-modules.php`, `class-amanah-seo-module-navigation.php` | Core owns the single plugin entry point, top-level menu, capability boundary, and activation/deactivation hooks. |
| AI runtime/provider settings/credentials | AI `plugin/modules/ai/`, `plugin/admin/class-ai-settings-controller.php`, `plugin/includes/class-plugin-core.php` | Preserve existing class contracts, provider configuration, encrypted credential storage, and API routes. Do not activate AI's standalone plugin lifecycle from Core. |
| Entity Profile | AI `plugin/modules/entity/` and corresponding admin/controller files | Use registered page slug `amanah-entity-profile`; preserve existing entity rows and capability checks. PR #49 fixes old links/redirects that incorrectly used `amanah-seo-ai`. |
| Authority & Author | AI `plugin/modules/authority/` | Keep its capability and user-contact hooks; no duplicate top-level menu. |
| Content Analyzer / Article Generation | AI `plugin/modules/content/` | Preserve validators, execution trace, persistence, human approval and existing REST contracts. |
| FAQ / Schema generation | AI `plugin/modules/faq/`, `plugin/modules/schema/` and REST controllers introduced by PR #19 | Generation code is not equivalent to a complete admin review UI. Do not expose placeholder screens as functional tools. Preserve existing schema persistence. |
| ENGINE domain/workflows/services/repositories | ENGINE `plugin/Core/`, `plugin/Application/`, `plugin/Domain/`, `plugin/Infrastructure/`, `plugin/Presentation/` | ENGINE currently has its own plugin entry and migrations v1–v18. Do not require its standalone entry file or execute its activation hooks from Core. Port internal classes behind a namespaced adapter only after their complete dependency graph is vendored and bootstrap is covered by tests. |
| Licensing / entitlements | Existing Core and AI licensing implementations | No license table/option deletion, credential reset, or entitlement bypass. Unified entitlement mapping requires regression coverage before changing runtime checks. |

## AI PR selection

- PR #46: selectively take menu registration behavior only after aligning its host slug check. Its source checks `admin_page_hooks['amanah-seo-dashboard']`; Core's actual menu hook must be used instead. Keep standalone AI navigation compatibility in the source project, but the Core package must not create a second top-level menu.
- PR #49: take the page-slug correction for entity list/save/delete/analysis redirects; no schema change is required.
- PR #19: take FAQ/Schema generation, validators, REST and persistence implementation selectively. Do not take placeholder admin pages as functional tools. Check overlap with PR #46 and the current Core menu before applying.
- Do not merge these PRs wholesale: they touch overlapping AI bootstrap, menu and tests.

## Initialization and database safety

1. The Core entry point remains the only activation/deactivation owner.
2. AI internal bootstrap must load classes once and must not invoke a second plugin activation hook.
3. ENGINE's `plugin/amanah-seo-engine.php` must not be required by the Core entry point; it boots a separate lifecycle and migration manager.
4. AI install routine uses schema version `1.3.0` and can archive `wp_amanah_content_analysis` to a legacy table. Integration must not run that archive path when the destination table already exists; never drop or overwrite a populated archive.
5. ENGINE migrations currently run through v18 and must be reviewed individually before being wired into Core. Migration execution is not yet approved for the integrated runtime.
6. Existing API keys, options, license records, entity records, schema records, and post meta must remain untouched by module loading.

## Evidence and open release gates

- Core PHP checks PASS at integration commit `3b7f35d97396b8283ae86f1625e35ed630f2d040`: https://github.com/amanahgroup123/AMANAH-SEO/actions/runs/38225059093
- Core regression checks PASS at that commit on PHP 8.1 and 8.2: https://github.com/amanahgroup123/AMANAH-SEO/actions/runs/38225058981
- ENGINE Phase 12 release gate PASS at `3cbda06ce1b622926f2f494674e855f2ee9474f2`, PHP 7.4 and 8.2: https://github.com/amanahgroup123/amanah-seo-engine-v2/actions/runs/38224963086
- These checks prove source-repository checks only. They do not prove Core+AI+ENGINE integrated runtime or WordPress activation.
- ENGINE has not yet been vendored as an internal module in this branch. This PR remains draft until that package, its dependency/autoload boundary, database migration guard, and integration regression checks are complete.
