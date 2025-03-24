# Deploy-It

A complete software package to manage, control and deploy virtual machines.

## 🧱 Stack
EXAMPLE:
The stack is the [_VILT_](https://viltstack.dev/) stack.

- [Vue.js](https://vuejs.org/)
- [Inertia.js](https://inertiajs.com/)
- [Laravel](https://laravel.com/)
- [TailwindCSS](https://tailwindcss.com/)

## 💻 Technical

The project requires the following dependencies to get up and running:
* PHP >= 8.2
* MySQL >= 8
* Node >= v23

You can use [php.new](https://php.new) to quickly get an environment up and running.

## 🏃 Installation & Running

1. To get started using the application, clone down the repository

```bash
git clone git@github.com:SebastianSlangerup/deploy-it.git && cd deploy-it
```

2. Copy and set environment variables

```bash
cp .env.example .env
```

3. Install the required dependencies

```bash
composer install && npm install
```

4. Migrate and seed the test data

```bash
php artisan migrate --seed
```

5. Spin up the development server

```bash
composer run dev
```

### ESLint and Prettier

ESLint and Prettier is configured, to ensure code quality and consistency across the Vue application and TypeScript.  
To setup ESLint and Prettier in PHPStorm, follow the instructions in
the [PHPStorm documentation for Prettier](https://www.jetbrains.com/help/phpstorm/prettier.html#ws_prettier_configure)
and
the [PHPStorm documentation for ESLint](https://www.jetbrains.com/help/phpstorm/eslint.html#ws_js_eslint_activate).  
Be sure to enable both on `Save`.

For VSCode, have a look at these extensions:

- [ESLint](https://marketplace.visualstudio.com/items?itemName=dbaeumer.vscode-eslint)
- [Prettier](https://marketplace.visualstudio.com/items?itemName=esbenp.prettier-vscode)

### Commands

Start the development server:

```bash
composer run dev
```

Build the production assets:

```bash
npm run build
```

#### ESLint (linting)

Lint the code:

```bash
npm run lint
```

Validate the code:

```bash
npm run lint:check
```

#### Prettier (formating)

Format the code:

```bash
npm run format
```

Validate the formatting:

```bash
npm run format:check
```

## 🤖 CI/CD

The CI/CD pipeline is using [GitHub Actions](https://github.com/features/actions) and are only running when merged
into `develop` or to `main`.

The following pipelines is configured (the code can be found in `.github/workflows`):

- **ESLint** - Check and validation
- **Prettier** - Check and validation
- **Pint** - Formatting the PHP code and automatically fixing it
- **Pest** - Runs the Laravel tests
- **Pest** Architecture - Runs the Laravel tests with the architecture flag
- **PHPStan** - Static analysis of the PHP code
- **[SonarCloud](https://www.sonarsource.com/products/sonarcloud/)** (only running on `main`) - Reviews the code quality
  and security
- **[Snyk](https://snyk.io/)** (only running on `main`) - Checks for security vulnerabilities in the code

## 🔀 Git

The branching strategy is [`git-flow`](https://nvie.com/posts/a-successful-git-branching-model/) and commit messages is
following [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) with
the [Angular flavour](https://github.com/angular/angular/blob/22b96b9/CONTRIBUTING.md#-commit-message-guidelines).

### Commit message format

`<type>(<scope>): <subject>`

Examples:  
`feat: add login form`  
`fix: remove unused imports`  
`docs: update README.md`  
`build(composer): add spatie/laravel-data`

#### Types

```text
build: Changes that affect the build system or external dependencies (example scopes: composer, npm)
ci: Changes to our CI configuration files and scripts
docs: Documentation only changes
feat: A new feature
fix: A bug fix
perf: A code change that improves performance
refactor: A code change that neither fixes a bug nor adds a feature
style: Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc)
test: Adding missing tests or correcting existing tests
```

## 🔗 Handy links

### Package

- Link
    - To
- Package
- Documentation
