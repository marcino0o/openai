# AGENTS Instructions

## Commit message convention
- We use semantic-release, so use Conventional Commit prefixes in every commit title, e.g. `feat:`, `fix:`, `chore:`, `docs:`, `test:`, `refactor:`; do not manually edit the changelog, because it is generated automatically from commit messages.
- Keep commit subjects short and imperative to support automated changelog generation and release tagging.

## Quality gate before commit
- Before creating any commit, run all available quality checks that are part of the project pipeline (prefer `composer check:all` when available).
- At minimum, run each relevant script from `composer.json` used by pipelines (for example: `composer cs`, `composer rector`, `composer stan`, `composer test`, `composer test:unit`, `composer test:integration`).
- Do not skip tests: always run the full available automated test scope before commit.
- If any check cannot be executed due to environment limitations, document that clearly in your final report.
