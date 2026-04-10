---
name: git-flow
description: "Apply this skill for all git and version control tasks. Triggers when: starting any new feature, bug fix, refactor, or chore; committing completed changes; pushing to GitHub; reviewing branch strategy; writing commit messages. Always remind the user to commit and push after completing any change, and to create a branch before starting new work."
license: MIT
metadata:
  author: viktorzubov
---

# Git Flow

## Context

This project follows **GitHub Flow** — a lightweight, branch-based workflow suited for solo or small-team continuous development. The `main` branch is always deployable.

## Workflow

### Before Starting Any Work

Always create a branch from `main` before making changes:

```bash
git checkout main && git pull
git checkout -b <prefix>/<short-description>
```

### After Completing Any Change

Always commit and push when work on a task is done:

```bash
git add .
git commit -m "<type>: <description>"
git push -u origin <branch-name>
```

Then open a Pull Request on GitHub and merge into `main`.

## Branch Naming

| Prefix | Use for |
|---|---|
| `feature/` | New functionality |
| `fix/` | Bug fixes |
| `refactor/` | Code restructuring without behaviour change |
| `chore/` | Dependencies, config, tooling, maintenance |
| `docs/` | Documentation only |
| `test/` | Adding or updating tests only |

**Examples:**
- `feature/post-view-counter`
- `fix/comment-form-validation`
- `refactor/search-highlighter`
- `chore/update-dependencies`

## Commit Message Convention

Follow the [Conventional Commits](https://www.conventionalcommits.org/) format:

```
<type>: <short imperative description>
```

| Type | Use for |
|---|---|
| `feat` | New feature |
| `fix` | Bug fix |
| `refactor` | Code change, no feature or fix |
| `chore` | Maintenance, dependencies, config |
| `test` | Adding or updating tests |
| `docs` | Documentation only |
| `style` | Formatting, whitespace, no logic change |

**Rules:**
- Use the imperative mood: "add post view counter", not "added" or "adds"
- Keep the subject under 72 characters
- Do not end with a period
- Be specific: "fix comment form closing when comment_status is closed" not "fix bug"

**Examples:**
```
feat: add post view counter using WordPress post meta
fix: return 403 when comment_status is not open
refactor: extract keyword highlighting into SearchHighlighter service
chore: update corcel to v3.1
test: add PHPUnit tests for CategoryController 404 handling
```

## Rules

- **Always branch first.** Never commit feature work directly to `main`.
- **Remind the user to commit and push** after completing any task, code change, or fix.
- **Suggest a branch name** when starting work on anything new.
- **One concern per commit.** Don't mix a feature and a bug fix in the same commit.
- **Commit early and often.** Small, focused commits are easier to review and revert.
- **Never force push to `main`.** Only force push to your own feature branches if needed.
- **Delete branches after merging** to keep the repository clean.

## Reminders

At the end of any coding task, always prompt:

> "Ready to commit and push. Suggested branch: `<prefix>/<description>`. Run:
> ```bash
> git checkout -b <prefix>/<description>
> git add .
> git commit -m \"<type>: <description>\"
> git push -u origin <prefix>/<description>
> ```"
