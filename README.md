# Corcel Blog

A modern blog frontend built with **Laravel 12** on top of a **WordPress database** using [Corcel](https://github.com/jgrossi/corcel). No WordPress PHP required — Laravel reads the WP data directly and renders a fast, custom frontend.

## Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.4 · Laravel 12 · Corcel v9 |
| Database | MySQL (WordPress schema) |
| Frontend | Blade · Tailwind CSS v4 |
| Code quality | Laravel Pint · PHPUnit 12 |

## Features

- **Posts** — listing with featured hero, topic filter bar, pagination
- **Post page** — reading progress bar, sticky TOC sidebar, syntax highlighting, share buttons, prev/next navigation, view counter, author bio card
- **Authors** — profile page with Gravatar, bio, post stats, post grid
- **Categories & Tags** — dedicated listing pages
- **Comments** — threaded display with reply support, comment form
- **Search** — full-text with keyword highlighting and contextual snippets
- **Dark mode** — system-aware with manual toggle, no flash on load
- **SEO** — XML sitemap, OpenGraph/Twitter Card meta, canonical URLs
- **Custom 404** — branded error page with recent posts

## Getting Started

```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Point to your WordPress database in .env
# DB_CONNECTION=wordpress
# DB_HOST=127.0.0.1
# DB_DATABASE=your_wordpress_db
# DB_USERNAME=...
# DB_PASSWORD=...

# Seed demo content (optional — 20 UX/design posts, 3 authors, 5 categories)
php artisan db:seed --class=WordPressContentSeeder

# Build assets
npm run build

# Serve
php artisan serve
```

## Routes

| Method | URI | Description |
|---|---|---|
| GET | `/` | Post index (homepage) |
| GET | `/posts/{slug}` | Single post |
| POST | `/posts/{slug}/comments` | Submit comment |
| GET | `/author/{username}` | Author profile |
| GET | `/category/{slug}` | Category listing |
| GET | `/tag/{slug}` | Tag listing |
| GET | `/page/{slug}` | Static WordPress page |
| GET | `/search` | Search results |
| GET | `/sitemap.xml` | XML sitemap |

## Development

```bash
# Run tests
php artisan test

# Watch assets
npm run dev

# Format PHP
vendor/bin/pint
```
