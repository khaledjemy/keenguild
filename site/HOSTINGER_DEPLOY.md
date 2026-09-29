# KeenGuild deployment on Hostinger

For automatic deployments from GitHub Actions, see [the GitHub deployment setup](deploy/GITHUB_AUTO_DEPLOY.md). It is disabled until the production repository source and SSH secrets are ready.

The local repository is the **single code source**. Do not maintain a second
editable `hostinger-upload` tree: upload the reviewed files directly from this
repository and preserve the production database and `.env` on the server.
Do not copy the local `.env`, SQLite database, administrator password, or test
data to the server. Compare the deployed files or checksums before claiming
that production matches the local source.

## Layout and prerequisites

Keep `dist/` next to `site/`: the Laravel controller reads `../dist/index.html`
and its assets. Never expose `site/.env`, `site/storage`, `site/vendor`, or the
project root to HTTP. For Hostinger Web/Cloud's fixed `public_html`, use this
layout within the domain directory:

```text
keenguild.com/
  dist/                 (private legacy home source)
  site/                 (private Laravel application)
  public_html/          (only files copied from site/public)
    index.php           (use site/deploy/hostinger-public/index.php)
    .htaccess           (copy site/public/.htaccess)
    assets/ build/ css/ fonts/ js/
```

Copy the *contents* of `site/public` into `public_html`, excluding its local
`storage` symlink and stock `index.php`. Use the deployment-specific `index.php`
above instead. Set `KEENGUILD_PUBLIC_PATH` in the server `.env` to the absolute
`public_html` directory. If the host disables PHP's `exec()` and
`php artisan storage:link` fails, first confirm that `public_html/storage` does
not already exist, then create a shell symlink from `public_html/storage` to
`site/storage/app/public`. Do not upload the local `.env`, SQLite
database, local uploads, `node_modules`, or development `vendor` directory.

Use PHP 8.2+ with the extensions required by `site/composer.json`, Composer 2,
and a writable production database. Install dependencies on the server with
`composer2 install --no-dev --optimize-autoloader` where Hostinger supplies
`composer2`, or the equivalent Composer 2 command. Upload the built
`site/public/build/` and `dist/assets/legacy.css` produced by `npm run build`
locally if Node is unavailable on the plan.

## First deployment

1. Back up any existing site and database. Upload the reviewed repository state
   while preserving the `dist/` + `site/` relationship above.
2. Create a production `site/.env` on the server, not in Git. Set
   `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://YOUR_DOMAIN`,
   `KEENGUILD_PUBLIC_PATH=/home/ACCOUNT/domains/YOUR_DOMAIN/public_html`,
   database credentials, and the actual mail transport credentials. Keep
   `MAIL_FROM_ADDRESS=info@keenguild.com` only after that mailbox works.
   Configure the independent Cloudflare assistant's public site identifiers
   only if its production origin has been approved there.
3. Run from `site/`: `php artisan key:generate` on first installation only,
   `php artisan migrate --force`, and `php artisan storage:link`.
   To preserve the current site's edited content, upload the private
   `storage/app/content-export.json` generated locally by
   `php artisan keenguild:content-transfer export PRIVATE_PATH`. On a newly
   migrated target without users or inquiries, run
   `php artisan keenguild:content-transfer import storage/app/content-export.json --replace-migration-defaults`.
   **Do not run the catalog or legal seeders on top of this imported data.**
   Review all published content and create the first administrator using
   `php artisan keenguild:create-admin`. Never regenerate `APP_KEY` on updates.
4. Run `php artisan optimize`, then `php artisan keenguild:launch-check`.
   If the check fails, resolve its actual findings rather than disabling it.
5. Verify Arabic/English homepage, privacy, terms, quote submission and admin
   login over HTTPS on the final domain. Check uploaded images, video, the
   assistant, mobile layout and email delivery. Confirm redirects and backups.

## Required scheduled task

Run `php artisan schedule:run` **every minute** with Hostinger's cron facility.
The application then removes project inquiries older than 12 months daily.
Use the server's real absolute path to `site/artisan` and PHP binary; do not
copy a sample account path. Confirm the job's output after its first run.

The final Hostinger steps differ between Web/Cloud and VPS plans. Do not apply
VPS operating-system templates to a server with existing data: changing the
OS can erase it.
