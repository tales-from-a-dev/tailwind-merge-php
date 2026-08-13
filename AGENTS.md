# AGENTS.md

This repository is `tailwind-merge-php`, a PHP port of [tailwind-merge](https://github.com/dcastil/tailwind-merge) by dcastil. It merges Tailwind CSS class strings by removing style conflicts while preserving non-conflicting and non-Tailwind classes.

## Primary Goals

- Keep merge behavior correct for supported Tailwind CSS versions (currently v4.0–v4.3).
- Keep API and type contracts stable.
- Maintain strict PHP type safety (PHPStan level max).

## Repo Map

- Source code: `src/`
- Tests: `tests/`
- CI workflows: `.github/workflows/`

Deeper implementation notes live in:

- `.agents/tailwind-css-version-update.md` (Tailwind CSS version support workflow)

`CLAUDE.md` imports this file rather than restating it, so this file stays the single source of truth for agent guidance. Keep repo-wide guidance here.

## Architecture

This library is a PHP port of dcastil's JS `tailwind-merge`, so class names, algorithm, and data shapes deliberately mirror the JS implementation. When a design choice looks odd, check the upstream file it was ported from before changing it.

Public API is `TailwindMerge`, `TailwindMergeInterface`, and `Support\Config`. Everything else (`Support/`, `Validators/`, `ValueObjects/`, `Helper/`) is marked `@internal`.

Merge pipeline, per `TailwindMerge::merge()`:

1. `TailwindMerge` flattens the variadic string/array arguments into one class list, optionally short-circuiting through the PSR-16 cache (key = `xxh3` of the class list).
2. `Support\ClassListMerger` walks the class list **in reverse** — that is why the last class wins. Each kept class is prepended to the result, restoring source order.
3. `Support\ClassNameParser` splits a class into `modifiers`, `baseClassName`, `hasImportantModifier`, and `maybePostfixModifierPosition` (the `/` position), tracking `[]` and `()` depth so separators inside arbitrary values are ignored. Classes not carrying the configured `prefix` are marked `isExternal` and pass through untouched.
4. `Support\ClassGroupUtils` resolves the base class to a class group id via `Support\ClassMap`, a trie built from `classGroups`: it descends `nextPart` by `-`-separated segments first, and only falls back to the validators registered at that node.
5. Conflicts: a kept class marks its own `modifierId.classGroupId` plus every id in `conflictingClassGroups` (and `conflictingClassGroupModifiers` when a postfix modifier is present) as seen. Any class encountered later in the reverse walk that hits a seen id is dropped.
6. `Support\SortModifiers` normalizes variant order so `hover:focus:*` and `focus:hover:*` collide, while keeping `orderSensitiveModifiers` and arbitrary variants (`[...]`) pinned in place.

In `src/Support/Config.php`, a class group entry is a list of strings (literal class parts), nested arrays (further path segments), `ThemeGetter` instances (`Config::fromTheme('color')`, resolved lazily against `theme`), or callables (validators from `src/Validators/`).

### Known pitfalls

- **Class-group order in `Config` is semantically significant.** For groups sharing a prefix, the class map tries exact child paths first, then runs validators in registration order, so a broad validator (colors especially) registered before a specific one will claim classes the specific group should own. Sidebar order yields to correctness inside a shared-prefix cluster; see `.agents/tailwind-css-version-update.md`.
- **`Config` holds static state.** `setAdditionalConfig()` writes to a static property, and `getMergedConfig()` memoizes in statics keyed off it. Constructing a `TailwindMerge` mutates process-global config, so tests passing custom config can affect later ones unless the config is reset.
- **The class map is built once per `ClassGroupUtils` instance, but validators still run per lookup.** `getClassGroupId()` memoizes the `ClassPartObject`, so the map costs one build per `TailwindMerge` instance rather than one per class. Validators are not memoized: they run on every class that reaches them, so keep them cheap — put prefix or character checks before regexes. Anything that makes the map depend on mutable state would break the memo.
- **Slash syntax is postfix-first.** The parser assumes the part after `/` is a postfix modifier (`text-lg/7`). When the full slashed class belongs to its own group instead (for example named container queries), express that with `postfixLookupClassGroups` rather than branching in the parser.
- **PHP 8.1 is the floor** (CI matrix runs 8.1–8.5): no readonly classes, no 8.2+ syntax.

## Code Conventions

- Validators live in `src/Validators/` as a `final class` implementing `ValidatorInterface` with a static `validate(string): bool`, referenced from `Config` as a first-class callable (`FooValidator::validate(...)`). Shared regexes are `ValidatorInterface` constants; the `ValidatesArbitraryValue` and `ValidateArbitraryVariable` traits carry shared `[...]` and `(...)` matching logic.
- `tests/Feature/` holds behavior tests calling `(new TailwindMerge())->merge(...)` with `#[DataProvider]` arrays of `[input, expectedOutput]`.
- `tests/Unit/` covers validators, the cache, and `ClassMapTest`, which asserts the entire default class map — expect to update its expectations whenever `Config` gains or moves a class group.

## Environment and Commands

- Language: PHP 8.1–8.5
- Package manager: Composer

Core commands:

- `composer test` — run the full test suite (no coverage)
- `composer test:coverage` — run tests with coverage (requires Xdebug)
- `composer test:lint` — check code style with PHP-CS-Fixer (dry-run)
- `composer test:lint:fix` — auto-fix code style issues
- `composer test:types` — run static analysis with PHPStan
- `composer test:types:baseline` — regenerate the PHPStan baseline

Targeted test runs:

- `vendor/bin/phpunit tests/Feature/ModifiersTest.php` — one file
- `vendor/bin/phpunit --filter testItHandlesV43FeaturesCorrectly` — one test method

## Working Rules for Changes

1. Treat `src/Support/Config.php` as the behavioral source of truth for default class groups and conflicts.
2. If behavior changes, update tests in `tests/` in the same change.
3. Run `composer test:lint:fix` before committing to ensure consistent formatting.
4. Do not manually edit `vendor/`; it is a generated artifact.
5. Use `phpstan-baseline.neon` only for unavoidable PHPStan suppressions — do not suppress real issues.

## Documentation Sync Policy

Treat documentation updates as part of the same change, not as follow-up work.

Required when relevant:

1. Update `README.md` for any user-visible behavior, API, or version support changes.
2. Update `AGENTS.md` whenever agent workflow, repo conventions, required commands, architecture, or guardrails change. Do not duplicate that guidance into `CLAUDE.md`; it imports this file.

Definition of done for every PR/change:

1. Code and tests are updated.
2. Relevant docs are updated in the same change set.
3. `AGENTS.md` guidance is reviewed and updated if the change affects how agents should work in this repo.
4. `.agents/*` guidance is reviewed and updated if the change affects the version update workflow.

## Tailwind CSS Version Support

For Tailwind CSS version support work, follow `.agents/tailwind-css-version-update.md`.

## Quick Validation Matrix

- Modifier or arbitrary-variant semantics: run `tests/Feature/ModifiersTest.php`, `tests/Feature/ArbitraryVariantsTest.php`.
- Class groups, conflicts, default config: run `tests/Feature/DefaultConfigTest.php`, `tests/Feature/ClassGroupConflictsTest.php`, `tests/Feature/TailwindCssVersionsTest.php`.
- Arbitrary values and properties: run `tests/Feature/ArbitraryValuesTest.php`, `tests/Feature/ArbitraryPropertiesTest.php`.
- Validators: run `tests/Unit/Validators/`.
- Full suite: `composer test`.
