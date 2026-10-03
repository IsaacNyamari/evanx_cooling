# Evanx Cooling Systems

Website and online shop for **Evanx Cooling Systems**, an HVAC and refrigeration company in Nairobi, Kenya.

- **Company site:** home, about, services (six service pages), contact form with email delivery, sitemap.
- **Shop:** 195 HVAC and refrigeration products in 24 categories, with live search, category filtering and "request a quote" (prices are on request until you set them).
- **Admin:** manage products, categories and customer messages.
- **Built with:** Laravel 12, Livewire 3 (with Volt), Bootstrap 5 (public site), AdminLTE (admin), Vite, Pest/PHPUnit.

## Requirements

- PHP 8.2+ (8.3 recommended) with the `gd`, `fileinfo`, `mbstring`, `pdo_mysql` extensions
- Composer 2
- MySQL / MariaDB (SQLite is used for tests)
- Node 18+ and npm, only for rebuilding CSS/JS (the built assets in `public/build` are committed)

## Local setup

```bash
git clone https://github.com/IsaacNyamari/evanx_cooling.git
cd evanx_cooling
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` (database credentials, mail, `APP_URL`), create the database, then:

```bash
php artisan migrate
php artisan db:seed --class=ShopSeeder
php artisan serve
```

Create an admin user (public registration is disabled; admins are only created from the terminal):

```bash
php artisan admin:create
```

You are prompted for the name, email and password (hidden, entered twice). The email is marked as verified so the
admin can sign in straight away at `/login`. The same command works on the server in cPanel Terminal.

The site is then at <http://localhost:8000> and the admin at `/admin` (sign in at `/login`).

### Environment variables worth knowing

| Variable | Purpose |
| --- | --- |
| `PHONE`, `CONTACT_EMAIL` | Contact details shown across the site (defaults in `config/site.php`) |
| `FACEBOOK_URL`, `YOUTUBE_URL` | Social links in the header and footer |
| `MAIL_*` | SMTP settings for the contact form |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | `database` in production, so run the migrations first |

Contact details are read through `config('site.*')`, not `env()` in views, so they keep working after `php artisan config:cache`.

## The shop

| Public URL | What it is |
| --- | --- |
| `/shop` | Product list with search, category filter and sorting (state is kept in the URL) |
| `/shop/{slug}` | Product page with gallery, details, related products and quote buttons |

| Admin URL (login required) | What it is |
| --- | --- |
| `/admin/products` | List, search, filter, toggle Active/In stock, create, edit, delete |
| `/admin/categories` | The same for categories, with parent/child nesting |
| `/admin/messages` | Contact form messages |

Everything above is a Livewire component under `app/Livewire/Shop` and `app/Livewire/Admin`.

### Seeding the catalogue

`php artisan db:seed --class=ShopSeeder` imports the catalogue from `database/seeders/data/*.json`
(a snapshot of the supplier's WooCommerce Store API). It rebrands names and descriptions for Evanx
Cooling Systems, swaps in our phone and email, and links each product to its categories (a product can
be in several). The seeder is safe to re-run: it matches on the original IDs, won't duplicate rows, and
won't overwrite images stored locally.

### Images

Images are stored in `public/uploads/products` and `public/uploads/categories`, **inside `public/`**,
so they work on shared hosting without `php artisan storage:link`.

- **Admin uploads** go through `App\Support\ImageUploader`, which creates the folder, saves the file and
  confirms it exists on disk before the database is touched. A failure shows a clear error on the form.
- **Missing files** fall back to `public/img/placeholder.svg` instead of a broken image.
- **`php artisan shop:download-images`** downloads any product or category image that is still a remote URL
  into `public/uploads`. The seeder reuses those local copies, so a fresh server never needs the old site.

## Deploying to cPanel (shared hosting)

1. Clone the repo with **cPanel → Git Version Control** into a folder **outside** `public_html`
   (for example `/home/USER/evanx_cooling`).
2. Point the domain's document root at that folder's **`public`** directory.
3. In cPanel Terminal, inside the clone:

   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan db:seed --class=ShopSeeder --force
   php artisan config:cache && php artisan view:cache
   ```

   Don't run `route:cache` (some routes are closures).
4. Create `.env` on the server (it is not in the repo) with production values. In particular:
   `APP_ENV=production` and `APP_DEBUG=false`, your real `APP_URL`, and the database and SMTP credentials.
5. Make `storage/`, `bootstrap/cache/` and `public/uploads/` writable (755, or 775 if uploads fail).
   Livewire parks a picked file in `storage/app/livewire-tmp` first, so uploads fail if `storage/` isn't writable.

To update after a push, pull in Git Version Control, then run `composer install --no-dev -o`,
`php artisan migrate --force`, `php artisan config:cache` and `php artisan view:cache`.

`public/build` (compiled CSS/JS) is committed because shared hosting has no Node. After changing
anything in `resources/css` or `resources/js`, run `npm install && npm run build` locally and commit the result.

## Error pages

Branded pages for 401, 403, 404, 405, 419, 429, 500 and 503 live in `resources/views/errors`. They use a
standalone layout that doesn't touch the database or session, so they still render when those are what failed.
Redirects are configured in `bootstrap/app.php`:

- an expired form (419) returns the visitor to the previous page with their input and a short notice;
- opening a POST-only URL in the browser (such as `/send-message`) goes to the contact page, or home otherwise;
- a signed-out visitor on an unknown `/admin/...` URL is sent to the login page.

## Tests

```bash
php artisan test
```

Feature tests cover the seeder, disabled registration, the `admin:create` command, public shop, Livewire list filtering, admin CRUD, image uploads (saved, replaced
and removed from disk, bad files rejected) and the error handling. Two stock Laravel Breeze tests
(`AuthenticationTest` navigation menu, `PasswordConfirmationTest`) currently fail because they expect a
`/dashboard` route and this site's admin lives at `/admin`.

## Project layout

```
app/Livewire/Shop        Public shop components (product list, product detail)
app/Livewire/Admin       Admin components (product and category index/form)
app/Models               Product, Category (+ Concerns/HasImages), contact messages
app/Support              ImageUploader
app/Console/Commands     shop:download-images
config/site.php          Phone, email and social links
database/seeders         ShopSeeder and data/ (catalogue snapshot)
public/uploads           Product and category images (committed)
resources/views/errors   Error pages
```

## What's next

The shop is currently a catalogue with quote requests. Planned for full e-commerce: a cart, checkout,
M-Pesa payment and an orders section in the admin.
