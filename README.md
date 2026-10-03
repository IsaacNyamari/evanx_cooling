# Evanx Cooling Systems

Website and online shop for **Evanx Cooling Systems**, an HVAC and refrigeration company in Nairobi, Kenya.

- **Company site:** home, about, services (six service pages), contact form with email delivery, sitemap.
- **Shop:** 195 HVAC and refrigeration products in 24 categories, with live search, category filtering and "Order via WhatsApp" buttons (prices show as "on request" until you set them).
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
| `WHATSAPP_NUMBER` | Number that receives WhatsApp orders, international format e.g. `254707856908` (defaults to `PHONE`) |
| `PHONE`, `CONTACT_EMAIL` | Contact details shown across the site (defaults in `config/site.php`) |
| `FACEBOOK_URL`, `YOUTUBE_URL` | Social links in the header and footer |
| `MAIL_*` | SMTP settings for the contact form |
| `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION` | `database` in production, so run the migrations first |

Contact details are read through `config('site.*')`, not `env()` in views, so they keep working after `php artisan config:cache`.

## The shop

| Public URL | What it is |
| --- | --- |
| `/shop` | Product list with search, category filter and sorting (state is kept in the URL) |
| `/shop/{slug}` | Product page with gallery, details, related products and an "Order via WhatsApp" button |

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

## SEO (Admin > SEO)

Every public page gets a unique title, meta description, canonical link, Open Graph / Twitter share tags and a
robots tag, all produced by `App\Support\Seo`. Nothing is hard-coded in the page templates any more.

- **Pages** (home, shop, services, about, contact, the six service pages): built-in titles (<= 60 characters) and
  descriptions (<= 160) are used until you override them in **Admin > SEO > Pages**, where you can also set a share
  image per page or hide a page from Google (`noindex`).
- **Products**: a title ("Name in Kenya | Evanx Cooling Systems") and description (the first sentences of the product
  text plus a call to action) are generated automatically. Customise them in **Admin > SEO > Products** or in the
  product form; empty fields mean "automatic". Product pages also publish `Product` structured data (with an offer
  only when the product has a price).
- **Share images (Open Graph)**: products use their own photo; everything else uses the default card
  `public/img/og-default.jpg` (1200 x 630), or your own upload under **SEO > Pages > Default share image**.
  Regenerate the default card with `php tools/generate-og-image.php`.
- **Overview tab**: a 0-100 score with a checklist (HTTPS, sitemap, robots.txt, share image, product photos,
  duplicate titles, hidden pages) and a list of products to improve. Pages and Products tabs show live character
  counters plus Google-result and WhatsApp/Facebook previews while you type.
- **One-click AI SEO**: every row of **Admin > Products** (and of **SEO > Products**) has an *AI SEO* button. One click
  writes the title and description with Gemini and saves them straight away: the button shows *Generating...*, then
  *Done*, and the row's badge changes from *Auto* to *Custom*. *Redo* replaces it (after a confirmation). Failures are
  shown on the row and change nothing.
- **Generate with AI (Gemini)**: on the product form (new and edit) and in **SEO > Products**, the *Generate with AI*
  button writes the Google title and meta description from the product's name, category and description. The text is
  put into the boxes for you to read and adjust; nothing is saved until you press Save. To switch it on, create a key
  at <https://aistudio.google.com/apikey> and add it to the server `.env`:

  ```
  GEMINI_API_KEY=your-key
  GEMINI_MODELS=gemini-flash-lite-latest,gemini-flash-latest
  ```

  then `php artisan config:cache`. `GEMINI_MODELS` is a list of (free) models tried in order: if the first is out of
  quota, unknown or down, the next one is used; a rejected key stops straight away. Without a key the button is shown disabled. The key stays on the server and is
  never sent to the browser. The AI is told to use only facts from the product details, and answers are length-checked
  (title <= 60, description <= 160 characters, retried once and trimmed if needed). Each admin is limited to 12
  generations a minute. The product name and description are sent to Google to produce the text.
- Search-result URLs (`/shop?q=...`) are `noindex`; the admin and login pages are `noindex,nofollow`.
- Meta keywords are not used: Google ignores them.

After submitting the sitemap in Google Search Console, titles and descriptions update the next time Google
re-crawls a page (use *URL inspection > Request indexing* to speed that up for important pages).

## Sitemap (Admin > Sitemap)

The admin page generates the sitemap and shows the link to give to Google. It lists the home page, About,
Services (with the six service pages), Contact, the Shop and every **visible** product (with last-updated date and
main image). Admin and login pages are never listed.

- **Link to submit:** `https://your-domain/sitemap.xml` (copy button on the page). In Google Search Console go to
  *Sitemaps*, enter `sitemap.xml` and submit once. Google re-reads it by itself afterwards.
- The page shows when it was last generated and flags it **Out of date** after products change; click *Regenerate*.
  *Download sitemap.xml* saves the file if you need to upload it somewhere.
- Also from the terminal: `php artisan sitemap:generate`. The deploy page regenerates it after each deployment.
- The file is stored in `storage/app/sitemap.xml` and served by the app, so there is no static file in `public/`
  to go stale. `/robots.txt` is also generated: it blocks `/admin` and `/login` and points to the sitemap.
- Category filters are deliberately not listed (to Google they are the same page as `/shop`).
- On the command line the URLs come from `APP_URL`, so keep it set to the real `https://` address.

## One-click deployments (Admin > Deployments)

After the first manual setup, updates can be done from the admin without cPanel Terminal:
open **Admin > Deployments**, tick what you want, confirm with your password and press **Deploy now**.
It runs, in order: `git fetch` + `git reset --hard origin/<branch>`, `composer install --no-dev`,
`php artisan migrate --force`, `php artisan db:seed --class=ShopSeeder --force`, then clears and
rebuilds the caches and regenerates the sitemap. The log streams live on the page, and the last 15 runs are kept as history.
The pipeline stops at the first failing step.

Things to know:

- **Off by default.** Set `DEPLOY_ENABLED=true` in the server `.env` to switch it on (then `php artisan config:cache`).
  Optionally restrict it with `DEPLOY_ALLOWED_EMAILS=you@example.com,other@example.com`.
- **Needs your password** for every deployment, and only one deployment can run at a time.
- **The seeder is safe to run every time.** It only adds missing catalogue items and repairs broken
  slugs/images; prices, descriptions and categories you edited in the admin are never overwritten.
- **`git reset --hard`** throws away any changes made directly to tracked files on the server, which is what
  makes deployments reliable. Untracked files (`.env`, uploaded images, logs) are not touched.
- **If the host blocks background processes** (the page will say so), deployments are queued and picked up by the
  Laravel scheduler. Add this once in cPanel > Cron Jobs (every minute); the page shows the exact line for your server:

  ```
  * * * * * cd /home/USER/evanx_cooling && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
  ```
- If it can't find `php` or `composer`, set `DEPLOY_PHP_BINARY` / `DEPLOY_COMPOSER` in `.env`.

The first time you enable it, update the server by hand once (`git pull`, `composer install --no-dev -o`,
`php artisan migrate --force`) so the page and its table exist. After that it can update itself.

## Private repository

The repository can be private. The server then needs read access of its own, using a **deploy key**
(a key that can read just this one repository). On the server, in cPanel Terminal:

```bash
ssh-keygen -t ed25519 -C "evanx-deploy" -f ~/.ssh/evanx_deploy -N ""
cat ~/.ssh/evanx_deploy.pub          # copy this
```

1. On GitHub: repository > **Settings > Deploy keys > Add deploy key**, paste the key, leave "Allow write access" **off**.
2. Tell SSH to use that key for GitHub, by adding this to `~/.ssh/config` (create it if missing, then `chmod 600 ~/.ssh/config`):

   ```
   Host github.com
       HostName github.com
       User git
       IdentityFile ~/.ssh/evanx_deploy
       IdentitiesOnly yes
   ```
3. Switch the server's remote to SSH and test it:

   ```bash
   cd ~/evanx_cooling
   ssh-keyscan github.com >> ~/.ssh/known_hosts
   git remote set-url origin git@github.com:IsaacNyamari/evanx_cooling.git
   git fetch      # should succeed with no password prompt
   ```
4. Only then make the repository private (GitHub > Settings > General > Danger Zone > Change visibility).

If the key is stored somewhere other than `~/.ssh/config` can reach, set `DEPLOY_SSH_KEY=/home/USER/.ssh/evanx_deploy` in `.env`.
Never put a personal access token in the remote URL; deploy keys are safer because they are read-only and tied to this repository.

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
app/Support/Seo*.php     SEO engine (titles, descriptions, share tags), analyzer and audit
database/seeders         ShopSeeder and data/ (catalogue snapshot)
public/uploads           Product and category images (committed)
resources/views/errors   Error pages
```

## What's next

The shop is currently a catalogue where customers order through WhatsApp. Planned for full e-commerce: a cart, checkout,
M-Pesa payment and an orders section in the admin.
