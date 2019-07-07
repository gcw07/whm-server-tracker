## WHM Server Tracker

WHM Server Tracker is a simple tracker that helps you keep track of your WHM servers. I help manage several WHM servers, but needed a way to keep track of all the servers and accounts on each one.

WHM has a built in API and you can setup an account to use that API key to access information on your servers. This pulls in the following information:

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

## Installation

Install this package by cloning this repository and install like you normally install Laravel.

- Run `composer install` and `npm install`
- Run `npm` and `npm run dev` to generate assets
- Copy `.env.example` to `.env` and fill your values (`php artisan key:generate`, database, pusher values etc)
- Run `php artisan migrate`
- Start your queue listener and setup the Laravel scheduler.
- Run installer `php artisan server-tracker:install` to setup default user.

## License

This project and the Laravel framework are open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
