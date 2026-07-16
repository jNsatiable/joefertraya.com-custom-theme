# joefertraya.com — custom theme

Hand-built WordPress custom theme replacing the current Elementor site. No page builders, no ACF, no form plugins.

## Read first

- **Handoff doc** (working preferences, Rentl pattern map): `H:\My Drive\Personal Vault\ai\personal-site-handoff-from-rentl.md`
- **WordPress Project Framework** (workflow §1, gotchas §2): `H:\My Drive\Personal Vault\ai\wordpress\WordPress Project Framework.md`

## Layout

- **The repo root is the theme itself** (`style.css`, `functions.php`, `assets/`, page templates). This is deliberate: Hostinger's native Git deployment clones the whole repo into one target directory (`public_html/wp-content/themes/joefertraya-theme`), so the repo must be the theme — no deploy script involved. If a companion plugin is ever needed, it gets its own repo + Git deployment.
- Design tokens live in `assets/css/tokens.css`; every color/font/layout value references a token, never a raw value.
- **Tokenize what you touch** (dark-mode prep, decided 2026-07-16): any PR that touches a line containing a raw color (`#fff`, `rgba(...)`, etc.) must promote that value to a semantic token in `tokens.css` (e.g. `--surface-card`, `--text-on-dark`, `--shadow-color`) as part of the same PR. Dark mode will later be implemented as a token-value swap under `[data-theme="dark"]` + `prefers-color-scheme` (port the Rentl toggle pattern) — this rule makes that a one-file change instead of a repo-wide audit.
- **Bump `JT_THEME_VERSION` in every PR that changes any CSS or JS file**, and keep `style.css`'s header version in sync. The enqueue `?ver=` query is the only cache-buster; an unbumped asset change silently never reaches visitors (learned the hard way in PR #12→#13).
- `.htaccess` here is theme-level only (blocks web access to `*.md` and dotfiles on the live server) — it is NOT the WordPress root `.htaccess`, which stays unmanaged by git per the Framework doc.
- `reference-files/` — gitignored copies of the live site's current theme, Elementor CSS, and content export. Read-only inputs, not part of the build.

## Workflow (from the Framework doc, non-negotiable)

- Discuss before executing; a question is not a go-ahead.
- One branch per change (`feat/`/`fix/`/`chore/`), never commit to `main`. Commit body: user-facing impact first, root cause (for fixes), what the change does, then a "Files changed" list.
- PR → squash-merge → delete branch → sync local `main` as one motion once approved — but confirm that bundling at the start of each new session.
- Mockup before implementing any visual/design decision; verify visual work against live rendering, not just CSS reading.
- This repo is Google-Drive-synced: expect phantom modified-timestamp diffs after merges (`git checkout -- <file>` clears them) and occasional stale `.git/config.lock`.
