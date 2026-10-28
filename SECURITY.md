# Security Policy

## Supported versions

This project is a boilerplate that is copied or cloned into new projects. Security fixes are applied to the `main` branch only. If you started a project from this boilerplate, port the fixes to your own codebase.

| Version | Supported |
| ------- | --------- |
| `main`  | Yes       |
| Others  | No        |

## Reporting a vulnerability

**Please do not open a public issue for security vulnerabilities.**

Report them privately using [GitHub private vulnerability reporting](https://github.com/eidiogolima/laravel-vue-boilerplate-pt-br-claude/security/advisories/new) (the **Security** tab of the repository, then **Report a vulnerability**).

Please include:

- A description of the vulnerability and its impact.
- Steps to reproduce, or a proof of concept.
- The affected files, routes or versions.
- Any suggested fix, if you have one.

## What to expect

- Acknowledgement of your report within **5 business days**.
- An assessment and an estimated timeline for a fix within **10 business days**.
- Credit in the release notes or advisory once the fix is published, unless you prefer to stay anonymous.

Please give us reasonable time to fix the issue before disclosing it publicly.

## Scope

In scope: the code in this repository, including authentication (Fortify, passkeys, 2FA), authorization policies, the admin panel, error log redaction and the Docker setup.

Out of scope: vulnerabilities in third-party dependencies that have no impact on this project (report those upstream), and issues that require a misconfigured production environment, such as leaving `APP_DEBUG=true` or using the placeholder `APP_KEY` from the Dockerfile.

## Hardening checklist for projects built from this boilerplate

- Generate a real `APP_KEY` and keep it secret. The one in `docker/prod/Dockerfile` is a build-time placeholder.
- Set `APP_DEBUG=false` and `APP_ENV=production` in production.
- Never commit `.env` files or credentials.
- Keep dependencies updated (Dependabot is enabled for GitHub Actions).
