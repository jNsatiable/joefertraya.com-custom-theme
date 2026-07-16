# joefertraya.com — custom theme

Hand-built WordPress custom theme replacing the current Elementor site. No page builders, no ACF, no form plugins.

## Read first

- **Handoff doc** (working preferences, Rentl pattern map): `H:\My Drive\Personal Vault\ai\personal-site-handoff-from-rentl.md`
- **WordPress Project Framework** (workflow §1, gotchas §2): `H:\My Drive\Personal Vault\ai\wordpress\WordPress Project Framework.md`

## Layout

- `joefertraya-theme/` — the theme (the deliverable). Design tokens live in `assets/css/tokens.css`; every color/font/layout value references a token, never a raw value.
- `reference-files/` — gitignored copies of the live site's current theme, Elementor CSS, and content export. Read-only inputs, not part of the build.

## Workflow (from the Framework doc, non-negotiable)

- Discuss before executing; a question is not a go-ahead.
- One branch per change (`feat/`/`fix/`/`chore/`), never commit to `main`. Commit body: user-facing impact first, root cause (for fixes), what the change does, then a "Files changed" list.
- PR → squash-merge → delete branch → sync local `main` as one motion once approved — but confirm that bundling at the start of each new session.
- Mockup before implementing any visual/design decision; verify visual work against live rendering, not just CSS reading.
- This repo is Google-Drive-synced: expect phantom modified-timestamp diffs after merges (`git checkout -- <file>` clears them) and occasional stale `.git/config.lock`.
