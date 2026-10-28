# Contributing

Thanks for your interest in contributing! This project is a boilerplate meant to stay simple, consistent and easy to maintain, so contributions are reviewed against the conventions below.

By participating, you agree to follow the [Code of Conduct](CODE_OF_CONDUCT.md). Security issues must be reported privately, as described in the [Security Policy](SECURITY.md).

## Ways to contribute

- Report bugs and suggest improvements through [issues](https://github.com/eidiogolima/laravel-vue-boilerplate-pt-br-claude/issues/new/choose).
- Improve the documentation or add translations.
- Fix a bug or implement a feature. For anything non-trivial, please open an issue first so we can agree on the approach before you invest time.

## Development setup

Requirements: PHP 8.5, Composer 2 and Node.js 22.

```bash
git clone https://github.com/<your-user>/laravel-vue-boilerplate-pt-br-claude.git
cd laravel-vue-boilerplate-pt-br-claude
composer setup
composer dev
```

## Workflow

1. Fork the repository and create a branch from `main`, named after the change (for example `feat/export-users` or `fix/locale-cookie`).
2. Make your change, following the conventions below.
3. Run the checks locally (see [Quality checks](#quality-checks)).
4. Commit using [Conventional Commits](https://www.conventionalcommits.org/) in English, for example `feat(users): add CSV export` or `fix(auth): keep intended URL after login`.
5. Open a pull request against `main` and fill in the template. Keep it focused: one concern per pull request.

## Conventions

These rules are mandatory.

### Language

- All code, commit messages and documentation are written in **English**: classes, methods, variables, routes, tables and columns.
- User-facing text is never hardcoded. It lives in translation files and must be added to **every supported locale** (`en`, `es` and `pt_BR`):
  - `lang/{locale}/messages.php` for success and informational messages.
  - `lang/{locale}/errors.php` for custom error messages.
  - `lang/{locale}.json` for frontend texts, keyed by the English text passed to `trans()`.
- Reference messages through translation keys, such as `__('messages.user.created')`.

### Architecture

- Keep it simple. Do not add layers, patterns or abstractions that are not needed.
- Follow SOLID and DRY.
- Business logic lives in Actions (`app/Actions`). Each Action does one thing; compose Actions when more behavior is needed.
- Services (`app/Services`) are only for integrations with external systems.
- Controllers contain no business logic: they receive a validated request, call Actions and return a response.

### Requests and responses

- Always use a Form Request for validation and authorization.
- Always shape returned data with API Resources. Never return models or raw arrays.
- Every listing is paginated.

### Strict typing

- Every PHP file starts with `declare(strict_types=1);`.
- Every parameter, return type and property is explicitly typed. Avoid `mixed` when a more specific type is possible.

### Code style

- Do not write comments in the code. Use descriptive names instead. PHPDoc type annotations are allowed only where PHPStan requires them, without descriptive text.
- Follow Laravel best practices and the existing conventions of the codebase.

### Frontend layout

- Every authenticated page wraps its content in `PageContainer`. Do not write page-level padding or wrappers by hand.
- Content is left-aligned next to the sidebar. Never center page content with `mx-auto`. Use `<PageContainer size="narrow">` for forms.
- Spacing scale: page padding `p-4 md:p-6`, `gap-6` between page sections, `gap-4` inside cards and between related groups, `gap-2` between a label, its input and its error.
- Use `gap-*` on flex/grid parents for spacing. Do not add outer margins to reusable components.

## Testing

Every behavior or logic change must include tests:

- **Feature tests** cover HTTP endpoints end to end: success, validation errors, authorization and pagination.
- **Unit tests** cover each Action in isolation.

## Quality checks

Run everything the CI pipeline runs before opening a pull request:

```bash
composer ci:check
```

Useful individual commands:

| Command               | Description                          |
| --------------------- | ------------------------------------ |
| `composer lint`       | Format PHP code with Pint            |
| `composer test`       | Run Pint, PHPStan and the test suite |
| `npm run check:fix`   | Format and lint frontend code        |
| `npm run types:check` | Type-check the frontend              |

All checks and tests must pass before a pull request can be merged.

## Using AI assistants

AI-assisted contributions are welcome, and the project is set up for it. The same rules apply: you are responsible for reviewing, testing and understanding every line you submit.

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE).
