# Bionic Backend

REST API backend for the **Bionic Cleaning Tracker** application.
Built with **CodeIgniter 4**, **PHP 8.3**, and **PostgreSQL**.

## Roles

| Role | Description |
|------|-------------|
| Admin | Manage users, locations, items, and actions |
| Operator | Submit task completions |
| Verifikator | Verify submitted tasks |

## Docker Deployment

The fastest way to run this application is with Docker.
See [DOCKER.md](DOCKER.md) for the full guide including how to build, push to Docker Hub, and run on a server.

```bash
cp .env.example .env   # adjust DB_PASS and APP_BASE_URL
docker compose up -d --build
```

## Local Development (without Docker)

Requirements: PHP 8.3+, Composer, PostgreSQL.

```bash
composer install
cp .env.example .env   # fill in database credentials
php spark migrate --all
php spark db:seed MainSeeder
php spark serve
```

## CLI Commands

```bash
php spark shift:initialize   # Set up operator shift assignments
php spark shift:rotate       # Rotate shift assignments
php spark status:clean       # Clean task status
```
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
