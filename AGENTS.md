# Repository Guidelines

## Project Structure & Module Organization

This PHP portfolio uses a small MVC structure in `/var/www/html/portifolio`. Application code lives in `app/`; the web entry point and public assets live in `public/`.

Put request handlers in `app/Controllers/`, data logic in `app/Models/`, shared MVC infrastructure in `app/Core/`, and templates in `app/Views/`. Keep public CSS, JavaScript, and images under `public/assets/{css,js,images}/`. `public/index.php` is the front controller; routes are registered with `App\Core\Router`.

## Build, Test, and Development Commands

No dependency manager, build step, or automated test framework is configured. PHP files run directly through the PHP runtime.

When Apache is running and its document root is `/var/www/html`, open `http://localhost/portifolio/` to inspect the site. Check the response with:

```sh
curl -I http://localhost/portifolio/
```

Run `find app public -name '*.php' -print0 | xargs -0 -n1 php -l` to check PHP syntax. This does not verify runtime behavior. Record future dependency, build, and test commands in `README.md`.

## Coding Style & Naming Conventions

Use four spaces for PHP and CSS indentation, and two spaces for JavaScript. Use descriptive lowercase, hyphen-separated asset names such as `project-card.css`. Prefer semantic HTML, accessible form labels, and meaningful image alternative text. Keep scripts and styles in separate files when they grow beyond small snippets. No formatter or linter is currently configured.

## Testing Guidelines

No test framework or coverage threshold exists. Before submitting changes, check PHP syntax and verify navigation, responsive layouts, missing assets, and browser console errors. Test modified interactions with keyboard input. When automated tests are added, document their location and execution command alongside the chosen framework.

## Commit & Pull Request Guidelines

There is no Git history to establish existing conventions. Once Git is initialized, use concise imperative commit messages, such as `Add portfolio project gallery`. Keep commits focused. Pull requests should describe the change, list verification performed, link relevant issues, and include screenshots for visual changes.

## Security & Configuration

Do not store credentials, private configuration, or database exports under the web document root. Avoid changing Apache configuration or filesystem permissions as part of ordinary content edits.

The user `heitor` already has filesystem permissions to manage this repository at `/var/www/html/portifolio`. Perform ordinary repository file operations as this user without `sudo`; do not change ownership or permissions unnecessarily. Execution sandbox restrictions are separate from these filesystem permissions.
