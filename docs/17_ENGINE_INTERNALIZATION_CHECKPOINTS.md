# ENGINE Internalization Checkpoint Log

## Scope and source
- ENGINE source repository: `amanahgroup123/amanah-seo-engine-v2`
- Source ref inspected: `main`, tree `80e7c38a327baeef1d7ad7b13d006d453d5838b1`
- Composer runtime requirement: PHP `>=7.4`; PSR-4 prefix `Amanah\\SEOEngine\\` mapped to `plugin/`.
- Composer development-only tools: PHPUnit `^9.6`, WordPress Coding Standards `^3.0`, PHP Parallel Lint `^1.4`.
- Source tree inventory: 297 PHP files under `plugin/` including standalone entry point; 296 internal production PHP files remain candidates for vendoring after excluding `plugin/amanah-seo-engine.php`. Five `.gitkeep` files are not production source.
- Runtime third-party Composer packages are not declared. WordPress APIs and PHP are the platform dependencies. This does not yet certify all external service integrations or runtime configuration.

## Mapping
Internal namespace root remains `Amanah\\SEOEngine\\` to avoid a broad, risky namespace rewrite. Internal PSR-4 root is `plugin/modules/engine-runtime/`, using the imported `Core/Autoload/ClassLoader.php`. Production files are mapped by removing the source `plugin/` prefix and prepending `plugin/modules/engine-runtime/`.

| Source group | Core destination | Status |
|---|---|---|
| PSR-4 class loader | `plugin/modules/engine-runtime/Core/Autoload/ClassLoader.php` | Imported |
| Production service bootstrap | `plugin/modules/engine-runtime/Core/Bootstrap/Plugin.php` | Imported, not activated yet |
| Keyword, Owner URL, Search Intent domain | `plugin/modules/engine-runtime/Domain/{Keyword,OwnerUrl,SearchIntent}/` | Imported |
| Keyword Analysis, Keyword Relationship, Cluster domain | `plugin/modules/engine-runtime/Domain/{KeywordAnalysis,KeywordRelationship,Cluster}/` | Imported |
| Content and Content Brief domain | `plugin/modules/engine-runtime/Domain/{Content,ContentBrief}/` | Imported |
| Keyword, Owner URL, Search Intent application use cases | `plugin/modules/engine-runtime/Application/UseCase/` | Imported |
| Keyword relationship, cluster, strategy, planning, blueprint, content and evaluation use cases | `plugin/modules/engine-runtime/Application/UseCase/` | Imported |

## Checkpoints on `integration/core-ai-engine-20261012`
- `23a3781ae65602c411da843a567259712dc95151` — added production PSR-4 loader.
- `ccec0dfcff37f4d84626bbf39cac9dd91e71f570` — imported production bootstrap source.
- `96839d6184a92ceace4e1d691d7963b508ea8338` — imported keyword, owner URL and search intent domain.
- `20832e5313b1471201754a9f60f8005aa0706338` — imported keyword analysis, relationships and cluster domain.
- `fa17a5cd978b88d47fc2e80c0f532843e110e5cd` — imported content and content brief domain.
- `ff45662866e3970bf5ccb676c35652d61ebb03c4` — imported keyword and owner URL use cases.
- `fcaf01c47197a88a52fa71c651ce56f4fd083703` — imported search intent and keyword analysis use cases.
- `27502f530ff67c5090449bdd22b86757bf90894e` — imported cluster, strategy and content planning use cases.
- `586ec2ac0212f057a23af1460274a5238ee3598f` — imported content blueprint and evaluation use cases.

## Safety and release gates
- The standalone ENGINE entry point is excluded from internal loading because it owns separate plugin lifecycle hooks.
- ENGINE's bootstrap is not yet required by Core. Do not activate it until all classes, platform adapters, admin integration, licensing, and migration guards are in place.
- Migration `Version17RemoveLegacySerpCredentials` was identified for review because it deletes stored `dataforseo` credentials; it must not be run during integration until that destructive behavior is replaced by a safe, explicitly approved migration.
- ENGINE has its own release-gate workflow in the source repository. Its source-repository PASS does not establish integrated Core+AI+ENGINE PASS.
- Required remaining work: complete source/dependency transfer (excluding dev artifacts and standalone entry point), inspect and safely adapt menu/license/migration ownership, add Core bootstrap registration and integration regression tests, run PHP lint and all Core/ENGINE tests via CI, and verify the latest PR workflow results. Local WordPress runtime verification is separate and must not be claimed from CI alone.


## Integration checkpoint update — 2026-10-12

### Production source transfer
- The full current ENGINE production PHP source set was internalized under `plugin/modules/engine-runtime/`: **296 source PHP files**, excluding the standalone `plugin/amanah-seo-engine.php` entry point. The internal bootstrap adds one additional PHP file.
- Production layers now include Domain, Application, AI providers, Infrastructure/WordPress, repositories, migrations, Platform license/update implementations, and Presentation/Admin workflows.
- No Composer runtime packages are required by ENGINE. Composer development dependencies and release-building tools were not copied into the production plugin.
- The original namespace prefix `Amanah\\SEOEngine\\` is preserved and mapped by the imported PSR-4 class loader.
- The Engine admin menu is nested under the existing `amanah-seo` Core menu. It no longer registers a second top-level menu; Engine license, update, AI configuration, and usage screens are hidden so these responsibilities remain Core-owned.
- Core loads `modules/engine-runtime/bootstrap.php` internally. The Core plugin deactivation hook delegates to ENGINE's lifecycle cleanup; deactivation clears both scheduled job hooks.
- The historical Version 17 credential migration is replaced with a compatibility marker/no-op so existing DataForSEO credentials are preserved. Source migration audit found this was the only migration matching destructive delete/drop patterns among versions 1–18.

### Integrated regression suite
- Added 24 ENGINE unit-test files under `tests/engine-runtime/Unit/` plus an internal PHPUnit bootstrap and config.
- The standalone release-metadata test was not imported because it checks the standalone ENGINE ZIP/release metadata, which is not the Core plugin's release artifact.
- Core regression CI now runs PHPUnit 9.6 in addition to existing regression scripts and PHP syntax checks.
- Current verified CI commit before this documentation-only update: `5bc4f36b2d3105f30b5998f55f777d452e3fb83c`.
  - [Core regression checks — PASS, PHP 8.1 and 8.2](https://github.com/amanahgroup123/AMANAH-SEO/actions/runs/38237747923)
  - [Core PHP checks — PASS](https://github.com/amanahgroup123/AMANAH-SEO/actions/runs/38237747958)
  - PHPUnit result: 105 tests, 313 assertions, 0 failures/errors on the verified Core regression run.
- Static regression checks confirm internal bootstrap presence, production source coverage, Core-owned menu placement, and preservation of legacy SERP credentials.

### Remaining release gates
- Keep PR #58 in draft until the current head's workflows are verified after all changes.
- Perform WordPress runtime smoke testing on a staging instance: activation, admin menu order/capabilities, Engine page load, database migration on a database copy, AI/Engine key-management integration, and deactivation/re-activation.
- Resolve any remaining Core-managed module-license integration gaps before calling this release-ready. Hidden ENGINE license screens prevent duplicate UI, but do not by themselves prove that ENGINE entitlement management is wired to the Core licensing dashboard.
- Do not copy this branch into the active WordPress plugin directory until the release gates above pass.


## Checkpoint 10 — Core-owned ENGINE workflow navigation
- Code commit: `b87d68f5ab970cf56a5611f231df250850a735b4`.
- Regression-test commit: `6b1163c0ac0202c4f80227f595cbd3d4c573e94a`.
- Changed files: `plugin/modules/engine-runtime/Presentation/Admin/AdminMenu.php` and `tests/module-integration-regression.php`.
- Finding: ENGINE's top-level menu had already been removed, but two workflow submenu groups still pointed to the now-unregistered `amanah-seo-engine` parent. Those pages could become orphaned instead of appearing under the single Core menu.
- Fix: both submenu parents now target `amanah-seo`; page slugs are unchanged. Added a regression assertion that fails if a submenu still points to the removed parent.
- Static repository verification: wrong-parent matches = 0; Core-parent matches in the affected registration = 3; regression assertion is present.
- GitHub Actions for head `6b1163c0ac0202c4f80227f595cbd3d4c573e94a`: Core regression and Core PHP checks were queued/running at checkpoint creation; results must be verified on the final checkpoint head before release.


## Checkpoint 11 — WordPress runtime smoke and duplicate bootstrap fix
- WordPress runtime CI added in `3dc828a86f7f32dbf455fb8b7bc1de97c3752082`, with smoke assertions added in `5327e7de993aa82b93a33de856b87f0d2da4861d`.
- First real WordPress activation exposed a fatal error: `AMANAH_SEO_Module_Navigation` was declared both inside `class-amanah-seo-admin.php` and in the separately required `class-amanah-seo-module-navigation.php`.
- Fix commit: `090cb3d696f7f748d9cc66972c4a5154c8138af7` removes the stale duplicate include; regression guard commit: `21deefb2f3eea0bbca59213eee178c9ec733ab14` prevents the duplicate include from returning.
- WordPress + MySQL runtime smoke on head `21deefb2f3eea0bbca59213eee178c9ec733ab14`: PASS. It activated the plugin, verified required Core/ENGINE tables and ENGINE schema version 18, tested Core-managed ENGINE license storage, verified ENGINE menu registration under Core, deactivated and reactivated the plugin, then repeated the smoke checks. Both smoke invocations printed PASS.
- Core regression on the same head: PASS on PHP 8.1 and 8.2, including 20 integration checks, 5 module-license checks, and PHPUnit 9.6.38 with 105 tests / 317 assertions / zero failures or errors.
- Core PHP syntax workflow on the same head: PASS.
- These CI results establish fresh-install activation and reactivation in a clean WordPress/MySQL environment. They do not replace staging verification against a copy of an existing site database, especially for existing license entitlements and stored data.


## Checkpoint 12 — Core license adapter namespace and live WordPress proof
- Expanded the real WordPress smoke test to exercise `Amanah\\SEOEngine\\Platform\\License\\LicenseSecretStore` directly, not only the Core storage class. Test updates: `0f861b552ec35a8d3473a41edfd59839e6b1199c` and syntax correction `1610d4e6781fe37657f8e74ca99ccb517e24b5a4`.
- The live smoke run exposed a namespace-resolution bug in the Engine adapter: unqualified static references resolved to `Amanah\\SEOEngine\\Platform\\License\\AMANAH_SEO_Module_Licenses`, which does not exist.
- Fix commit: `5212c956e5f6689f85fc0bc677bee331190ca157` explicitly qualifies all three calls to the global Core license manager; regression guard commit: `30a8374b36bf89697ddf6fa651bca81760cf27d4`.
- WordPress + MySQL runtime smoke on head `30a8374b36bf89697ddf6fa651bca81760cf27d4`: PASS twice (activation and reactivation). Both runs exercised Engine-to-Core encrypted license storage round-trip, migration version 18, required tables, and Core-owned menu registration.
- Core regression on that head: PASS on PHP 8.1 and 8.2. Core PHP syntax workflow: PASS. The exact head's WordPress runtime run passed.
- Remaining non-CI release gate: staging verification against a copy of an existing database and confirmation that the configured licensing API returns expected active/entitlement state. Clean-install CI cannot establish existing-site data compatibility or live external entitlement validity.


## Checkpoint 13 — Preserve existing DataForSEO credential
- Runtime smoke enhancement commit: `29d9356043ba2a69944c803353c30647284b005c`; follow-up namespace syntax correction: `e1d5ffbc5ec31842617bd694a0ee9f5cd07692f9`.
- The WordPress smoke test now writes a temporary encrypted DataForSEO credential through ENGINE's real `SecretStore`, invokes migration `Version17RemoveLegacySerpCredentials::up()`, and verifies the credential remains readable before cleaning up the temporary test option.
- CI at `e1d5ffbc5ec31842617bd694a0ee9f5cd07692f9`: Core regression PASS on PHP 8.1 and 8.2; Core PHP syntax PASS; WordPress + MySQL activation/reactivation smoke PASS twice, including the ENGINE license adapter round-trip and DataForSEO credential-preservation assertion.
- This verifies the integrated migration implementation in a clean database. It does not replace an upgrade rehearsal against a copy of a real, previously populated site database.


## Checkpoint 14 — Existing-row schema migration rehearsal
- Test commit: `800f02850d35c979e4a648310633d9c572fe7448`.
- The WordPress/MySQL smoke test now inserts a temporary existing ENGINE keyword row, reruns the idempotent Core-domain schema migration, and confirms the row ID remains present before cleaning up the temporary row. It also invokes the Version 17 migration against a temporary encrypted DataForSEO secret and verifies the secret is preserved.
- CI at `800f02850d35c979e4a648310633d9c572fe7448`: Core PHP checks PASS; Core regression PASS on PHP 8.1 and 8.2 (22 integration checks, 5 license checks, 105 PHPUnit tests / 317 assertions, zero failures/errors); WordPress + MySQL activation/reactivation PASS twice, including populated-row and credential-preservation checks.
- This is a controlled existing-row rehearsal, not a substitute for running migrations on a copy of the user's real WordPress database.


## Checkpoint 15 — Populated keyword-row preservation
- Test commit: `800f02850d35c979e4a648310633d9c572fe7448`; namespace correction commit: `e1d5ffbc5ec31842617bd694a0ee9f5cd07692f9`.
- The runtime smoke now seeds a real row in the ENGINE keyword table, reruns `Version2CoreDomain` against the populated schema, and verifies that the existing row ID survives. The temporary row is removed after the assertion.
- Latest verified code head before this documentation update: `800f02850d35c979e4a648310633d9c572fe7448`.
  - Core PHP syntax: PASS.
  - Core regression: PASS on PHP 8.1 and 8.2; 22 integration checks, 5 license checks, and 105 PHPUnit tests / 317 assertions, with zero failures/errors.
  - WordPress + MySQL runtime: PASS twice for activation/reactivation, ENGINE-to-Core license storage, populated keyword-row preservation, DataForSEO credential preservation, and Core-owned menu registration.
- The test provides a controlled existing-row migration rehearsal; an upgrade test against a copy of the real active site's database remains a separate release gate.
