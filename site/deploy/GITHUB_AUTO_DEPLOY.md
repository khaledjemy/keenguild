# Automatic deployment from GitHub to Hostinger

The workflow at `.github/workflows/deploy-hostinger.yml` deploys pushes to `main` to the existing KeenGuild layout. It is deliberately disabled until the repository variable `HOSTINGER_DEPLOY_ENABLED` is set to `true`. It never sends the Laravel `.env`, local database, local uploads, or `vendor` from GitHub to the server. It does not deploy the separate Cloudflare agent repository.

## Before enabling

1. Review and commit the complete production source in this repository. The current GitHub `main` may be older than the manually uploaded Hostinger site. **Do not enable deployment after pushing only this workflow**: that would replace server code with the older GitHub state. Preserve production `.env`, uploads, and database with a Hostinger backup before the first run.
2. Enable SSH access for the existing Hostinger account. Create a **dedicated deployment Ed25519 key** on your own computer and add only its public key to the Hostinger account's authorized SSH keys. Do not use your personal SSH private key and do not send a private key in chat.
3. In the GitHub repository, open Settings → Secrets and variables → Actions. Add these repository secrets:
   - `HOSTINGER_SSH_HOST`: the host/IP shown in Hostinger SSH Access.
   - `HOSTINGER_SSH_USER`: the hosting account's SSH username.
   - `HOSTINGER_SSH_PORT`: the SSH port shown there (Hostinger Web/Cloud commonly uses `65002`).
   - `HOSTINGER_SSH_PRIVATE_KEY`: the complete private deployment key, including its header and footer.
   - `HOSTINGER_SSH_KNOWN_HOSTS`: the verified SSH host-key line for the host and port. Compare its fingerprint with the one already trusted in Bitvise/Hostinger before saving it; do not blindly trust `ssh-keyscan` output.
4. Create a `production` environment in GitHub Actions if desired. Restrict it to the `main` branch. The workflow uses this environment for deployment history and optional protection rules.
5. Once the full source and secrets are ready, add the **repository variable** `HOSTINGER_DEPLOY_ENABLED=true`. Trigger a manual run from Actions → Deploy KeenGuild to Hostinger → Run workflow. Verify the run and both site languages before relying on automatic pushes.

After that, a successful `git push origin main` containing changes under `site/` or `dist/` runs the deployment. The workflow runs the PHP test suite before the deploy job, builds assets, and checks the remote database and layout before touching live files. It then puts Laravel in maintenance mode, synchronizes `site/` and `dist/` to their existing private directories and public assets to `public_html/`, installs production Composer dependencies, runs migrations and cache commands, performs the launch check, and takes the site out of maintenance mode. Public smoke checks cover both homepages, work, admin login, and `/up`. It does not delete files on the server; obsolete files require a separately reviewed cleanup. Never regenerate the production `APP_KEY` or import seed/demo data as part of routine deployments.

This is a maintenance-window deployment, **not an atomic release or automatic rollback**. If a step fails after maintenance begins, the site intentionally remains in maintenance mode instead of serving a possibly mixed version. Read the failed GitHub Actions step and the server logs, repair or restore code and database from the Hostinger backup if needed, run `php artisan keenguild:launch-check` inside `site/`, then run `php artisan up` only when the site is safe. A failure in the final public smoke checks happens after `artisan up`, so inspect the live site immediately. GitHub holds only code, not the production database or user uploads.
