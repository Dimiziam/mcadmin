# Git repo move to GitHub (2026-10) and prod setup

## What changed

The repo moved from `https://github.ramzes.biz/ramzes/MediaClubAdmin.git`
(server no longer reachable) to **https://github.com/Dimiziam/mcadmin.git**.
Same as was done for the site (`mediaclub` → `https://github.com/Dimiziam/mediaclub.git`).

All branches were moved: `master`, `OSA-2024`, `OSA2024`,
`bugfix/lang_update`, `fix_chart_url`.

## History was rewritten

GitHub rejects files over 100 MB. Commit `sql` (2016-03-25) had added DB dumps
of ~390 MB each:

- `www/_sql/mediaclub.sql.gz`
- `www/_sql/mediaclub_0401.sql.gz`

They were removed from every commit with `git filter-branch --index-filter
'git rm --cached …'`, and `www/_sql/*.gz` was added to `.gitignore`.
**Every commit hash from 2016 on changed.** Any old clone must be re-cloned
or reset (see below); `git pull` on an old clone will not work.

DB dumps belong outside git. Keep them on disk only.

## Prod: `/home/mediaclub/mediaclub.com/admin`

- Owner: `mediaclub:hosting` (same as `../site`). After running git as root:
  `chown -R mediaclub:hosting /home/mediaclub/mediaclub.com/admin`
- Root needs `git config --global --add safe.directory /home/mediaclub/mediaclub.com/admin`
  to avoid the "dubious ownership" error.
- Root's `cp` is aliased to `cp -i`. When pasting several commands, the
  overwrite prompt swallows the next pasted line. Use `\cp` to skip the alias.

### Prod-only files (skip-worktree)

These files differ from the repo on prod and are flagged
`git update-index --skip-worktree`, so `git status` stays clean and
`git pull` won't overwrite them:

| File | Why |
| --- | --- |
| `www/index.php` | `YII_DEBUG` off |
| `www/protected/config/main_db.php` | prod DB credentials (`info_db`, `old_db`) |
| `www/console/index.php` | prod-only edits (pre-2026) |
| `www/protected/models/Genre.php` | prod-only edits (pre-2026) |
| `www/protected/models/User.php` | prod-only edits (pre-2026) |
| `www/protected/views/user/add.php` | prod-only edits (pre-2026) |
| `www/protected/views/user/edit.php` | prod-only edits (pre-2026) |
| `www/protected/views/user/search.php` | prod-only edits (pre-2026) |

A copy of the prod versions and a patch (`all_prod_changes.patch`) are in
`/root/admin_keep/` on prod.

Useful commands:

```bash
git ls-files -v | grep '^S'                      # list flagged files
git update-index --no-skip-worktree <file>       # unflag (to see/commit changes)
git update-index --skip-worktree <file>          # flag again
```

If a commit changes one of these files, `git pull` on prod stops with an
error rather than overwriting it. Then: unflag the file, merge the change in
by hand, flag it again.

**Watch out:** `git reset --hard` / rebuilding the index clears the flags, and
the prod edits then show up as modified (or get overwritten by a checkout).
If the admin shows *The table "admins" for active record class "Admin" cannot
be found*, `main_db.php` has the repo (dev) credentials, not the prod ones.
Restore it from `/root/admin_keep/main_db.php`.

### Updating prod

Pull as the owner (no chown needed). Prod git is old (1.8.x), so `git -C`
doesn't work:

```bash
cd /home/mediaclub/mediaclub.com/admin
sudo -u mediaclub -H git pull
```

Or as root, then fix ownership:

```bash
cd /home/mediaclub/mediaclub.com/admin
git pull
chown -R mediaclub:hosting .
```

### Moving an old clone to the new history (what was done on prod)

```bash
git diff > /root/admin_keep/local.patch          # save local edits first
cp -a <edited files> /root/admin_keep/
git remote set-url origin https://github.com/Dimiziam/mcadmin.git
git fetch origin --prune
git reset --hard origin/master
git branch -u origin/master
\cp -a /root/admin_keep/<file> <path>            # put prod edits back
git reflog expire --expire=now --all && git gc --prune=now
```

## Credentials

The dev DB and `mclub_reader` passwords are in the repo history
(`main_db.php`). Treat them as exposed; rotate them if the repo ever becomes
non-private.
