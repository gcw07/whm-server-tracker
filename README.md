# WHM Server Tracker

WHM Server Tracker is a web application for managing and monitoring WHM servers and their hosted accounts. It uses the WHM API to pull in server and account data, and provides automated monitoring for uptime, domain blacklisting, WordPress versions, and Lighthouse performance.

## Features

#### Server Information
- Server disk usage
- Number of accounts on a server
- Backups turned on or off on a server
- Number of backups turned on for a server
- The days backups will run on a server
- Current default PHP version on a server
- All the accounts on a server

#### Account Information
- Domain name for an account
- Username for an account
- Backups turned on or off for an account
- Current plan name for an account
- Account disk space used and allowed
- If the account is suspended

#### Monitoring & Alerts
- **Uptime monitoring** — checks whether sites are up and alerts on downtime
- **Domain blacklist checking** — flags domains appearing on email blacklists
- **WordPress version checking** — detects outdated WordPress installations
- **Lighthouse performance auditing** — tracks performance scores for hosted sites
- **Email and Slack notifications** — alerts delivered via email and/or Slack webhook
- **Queue monitoring** — background jobs managed and monitored via Laravel Horizon

## Requirements

- PHP ^8.4
- MySQL
- Node.js / npm
- Composer
- A WHM server with API access enabled

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/gcw07/whm-server-tracker.git
   cd whm-server-tracker
   ```

2. Run the setup command (installs dependencies, generates key, runs migrations, and builds assets):
   ```bash
   composer setup
   ```

3. Copy `.env.example` to `.env` and fill in the required values:
   ```bash
   cp .env.example .env
   ```
   Key environment variables to configure:
   - Database connection (`DB_*`)
   - `SERVER_TRACKER_MAIL_TO_ADDRESS` — email address for alert notifications
   - `SERVER_TRACKER_SLACK_WEBHOOK_URL` — Slack webhook URL for alert notifications

4. Set the queue connection to an async driver (required for background jobs):
   ```
   QUEUE_CONNECTION=redis
   ```

5. Start Laravel Horizon to process background jobs:
   ```bash
   php artisan horizon
   ```

6. Add the Laravel scheduler to your cron:
   ```
   * * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
   ```

7. Run the installer to set up the default user:
   ```bash
   php artisan server-tracker:install
   ```

For local development, you can start the server, queue worker, log watcher, and Vite dev server together:
```bash
composer run dev
```

## Contributions

This project makes use of the following third-party packages:

- **[Flux UI Pro](https://fluxui.dev)** — the UI component library used throughout the application. Flux UI Pro requires a paid license; you must purchase one before using this project.
- **[spatie/laravel-uptime-monitor](https://github.com/spatie/laravel-uptime-monitor)** — powers the uptime monitoring features.

## License

This project and the Laravel framework are open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
