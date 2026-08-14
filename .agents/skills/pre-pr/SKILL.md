---
name: pre-pr
description: Run the full validation set and audit the working diff against this repo's definition of done before opening a PR.
disable-model-invocation: true
---

# Pre-PR check

Validates the current change set against `AGENTS.md`. Covers the part CI cannot:
CI runs lint, PHPStan, and PHPUnit, but nothing checks that docs moved with the
code or that the repo's known pitfalls were respected.

## 1. Establish the diff

```sh
git status --short
git diff main...HEAD --stat
git diff --stat
```

Use the branch diff against `main` plus any uncommitted work. Everything below
is judged against that combined set.

## 2. Run the validation set

```sh
composer test:lint:fix
composer test:types
composer test
git diff --check
```

Run all four even if an early one fails — a single report of every problem beats
serial round-trips. Note that `test:lint:fix` *writes*; if it changed files,
say which ones so they get committed.

If the diff touches the merge pipeline (`src/Support/`, `src/Validators/`,
`src/TailwindMerge.php`), also run `composer bench` and compare against the
numbers in the PR description. Nothing else guards against a performance
regression.

If PHPStan reports something new, fix it. `phpstan-baseline.neon` is for
unavoidable suppressions only, per working rule 5.

## 3. Audit the diff against the definition of done

Read the actual changed files — do not infer from filenames.

**Docs sync** (`AGENTS.md` "Documentation Sync Policy"):

- User-visible behavior, public API, or version-support change → `README.md`
  updated? Check the badges and the version prose line, not just the body.
- Public API surface change → `docs/index.md` updated?
- Agent workflow, repo conventions, required commands, architecture, or
  guardrails changed → `AGENTS.md` updated? Guidance belongs there, never
  duplicated into `CLAUDE.md`.
- Version-update process changed, or the change taught you a new config-ordering
  gotcha → `.agents/skills/tw-version-update/SKILL.md` updated?
- Breaking change → `UPGRADE.md` updated?

**Tests**: behavior changed → tests changed in the same diff. `src/Support/Config.php`
touched → `tests/Unit/ClassMapTest.php` almost certainly needs updating; it
asserts the whole default class map.

**Known pitfalls** — flag any of these the diff walks into:

- New or reordered class group sharing a prefix with an existing one. Broad
  validators (colors especially) registered before specific ones silently steal
  classes. Confirm the ordering is deliberate.
- New static state, or anything making the class map depend on mutable state.
  `ClassGroupUtils` memoizes both the trie and class name → class group id per
  instance; a mutable dependency breaks both memos. `Config::getMergedConfig()`
  must return its memo untouched on a hit — re-running the merge loop over an
  already-merged config duplicates list entries.
- Validator work on the hot path. Validators run once per *distinct* class name,
  but still on every cold lookup — a regex without a cheap prefix or character
  guard in front of it is a real cost.
- `symfony/string` or any new runtime dependency. The library requires only
  `psr/simple-cache`; `u()` was removed from the hot path deliberately.
- Code-point slicing of a class name. `ClassNameParser` produces *byte* offsets;
  they must be consumed with `substr`.
- A test file not ending in `Test.php`. PHPUnit silently skips it.
- PHP 8.2+ syntax. The floor is 8.1, and CI only catches this in the 8.1 leg:
  `readonly` classes, DNF types, trait constants, standalone `null`/`false`/`true`
  types, `#[\Override]`, and 8.2+ stdlib functions.
- `vendor/` edits. It is generated; never hand-edit.

## 4. Report

Give a short verdict:

- **Validation**: each of the four commands, pass or fail with the relevant output
- **Blocking**: anything that must be fixed before the PR opens
- **Worth a look**: judgment calls the author should confirm, not defects

If everything passes and the diff is clean against the checklist, say so plainly
in a line or two. Do not pad the report to look thorough.

Do not commit, push, or open the PR — this skill only reports.
