# laravel-vue-boilerplate-pt-br-claude

[Português](README.md) | **English**

> **This is a boilerplate.** It is a starting point for new Laravel + Vue projects, preconfigured for Brazilian Portuguese (pt-BR) and for development with Claude Code. It is not a finished application.

## Goal

This boilerplate aims to produce **clean code** that is **easy to maintain** and **self-managed with AI**:

- **Clean code:** strict conventions (single-purpose Actions, SOLID, DRY, strict typing, descriptive names) keep the code predictable and easy to read.
- **Easy maintenance:** a simple architecture, mandatory tests and automated pipeline checks prevent regressions and keep the standard over time.
- **Self-managed with AI:** project rules are documented in `CLAUDE.md` and `AGENTS.md`, so AI agents like Claude Code write, review and maintain the code always following the same standards.

## What's included

- **Backend:** Laravel 13, PHP 8.5, Fortify authentication (login, registration, password reset, email verification, 2FA and passkeys)
- **Frontend:** Vue 3, Inertia v3, TypeScript, Tailwind CSS v4, Wayfinder
- **Multilingual:** English, Spanish and Brazilian Portuguese, with a language switcher on the home page, the authentication screens and the dashboard. The default is `APP_LOCALE=pt_BR` (`America/Sao_Paulo` timezone) and translations live in `lang/en`, `lang/es` and `lang/pt_BR`
- **Quality:** strict types in every PHP file, Pint, PHPStan (Larastan level 7), Pest, and `vp check` for the frontend
- **CI:** GitHub Actions workflow running all checks and tests on every push to `main` and on pull requests
- **Claude Code ready:** project rules in `CLAUDE.md` and Laravel Boost guidelines in `AGENTS.md`

## Getting started

```bash
composer setup
composer dev
```

`composer setup` installs dependencies, creates `.env`, generates the app key, runs migrations and builds the frontend.

## Scripts

| Command             | Description                                 |
| ------------------- | ------------------------------------------- |
| `composer dev`      | Start the development environment           |
| `composer lint`     | Format PHP code with Pint                   |
| `composer test`     | Run Pint, PHPStan and the test suite        |
| `composer ci:check` | Run every check executed by the CI pipeline |
| `npm run check:fix` | Format and lint frontend code               |

## Conventions

All conventions are mandatory and described in [CLAUDE.md](CLAUDE.md). In short:

- Code in English. Portuguese is only allowed in user-facing text, stored in `lang/{en,es,pt_BR}/messages.php` and `lang/{en,es,pt_BR}/errors.php`, always in all three languages.
- Business logic lives in single-purpose Actions. Services are only for external integrations. Controllers have no business logic.
- Form Requests and API Resources are mandatory, and listings are always paginated.
- `declare(strict_types=1)` and full typing everywhere. No comments in code.
- Feature and unit tests are mandatory for every behavior change.

## Starting a new project

1. Copy or clone this boilerplate.
2. Rename the package in `composer.json` and set `APP_NAME` in `.env`.
3. Replace this README with your project's documentation.

## Credits

Created and maintained by [Diogo Lima](https://github.com/eidiogolima).
