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
