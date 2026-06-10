# Telegram Userbot

[![CI](https://github.com/olml89/telegram-userbot/actions/workflows/ci.yml/badge.svg)](https://github.com/olml89/telegram-userbot/actions/workflows/ci.yml)
[![Deploy](https://github.com/olml89/telegram-userbot/actions/workflows/deploy.yml/badge.svg)](https://github.com/olml89/telegram-userbot/actions/workflows/deploy.yml)
[![codecov](https://codecov.io/gh/olml89/telegram-userbot/branch/main/graph/badge.svg)](https://codecov.io/gh/olml89/telegram-userbot)

<details>
<summary>📊 Detailed Quality Checks Status</summary>

![PHPStan](https://img.shields.io/github/actions/workflow/status/olml89/telegram-userbot/quality-checks.yml?label=PHPStan&job=phpstan&branch=main)
![PHPUnit](https://img.shields.io/github/actions/workflow/status/olml89/telegram-userbot/quality-checks.yml?label=PHPUnit&job=phpunit&branch=main)
![Pint](https://img.shields.io/github/actions/workflow/status/olml89/telegram-userbot/quality-checks.yml?label=Pint&job=pint&branch=main)
![Rector](https://img.shields.io/github/actions/workflow/status/olml89/telegram-userbot/quality-checks.yml?label=Rector&job=rector&branch=main)

</details>

This application manages the lifecycle and behaviour of a Telegram userbot interacting directly 
with the MTProto API, similarly to an official app, using 
[MadelineProto 8](https://docs.madelineproto.xyz/index.html).

## Table of Contents

- [Prerequisites](#prerequisites)
  - [Required tools](#required-tools)
  - [Local environment configuration](#local-environment-configuration)
  - [direnv setup (recommended)](#direnv-setup-recommended)

- [Architecture](#architecture)
  - [Infrastructure services](#infrastructure-services)
    - nginx
    - tusd
    - redis
    - postgres
  - [Core application services](#core-application-logic-services)
    - backend
    - bot-manager
    - bot
  - [Logging and monitoring services](#logging-and-monitoring-services)
    - alloy
    - loki
    - grafana
  - [Development environment services](#development-environment-services)
    - dev
    - vite

- [Build phases](#build-phases)

- [Application Management](#application-management)
  - [Installation and setup](#installation-and-setup)
  - [Container lifecycle](#container-lifecycle)
  - [Shell Access](#shell-access)
  - [Code quality](#code-quality)
    - [phpstan](#phpstan)
    - [pint](#pint)
    - [rector](#rector)
    - [phpunit](#phpunit)
    - [tsc](#tsc)
  - [Dependency integrity checking](#dependency-integrity-checking)
  - [Repository status checking](#repository-status-checking)

## Prerequisites

### Required tools

This project uses a `Justfile` as the main task runner for local development and common workflows and on 
CI/CD pipelines to automate the deployment process.

Before getting started, make sure the following tools are installed on your system:

- [just](https://github.com/casey/just) — command runner used throughout the project
- [direnv](https://direnv.net/) *(optional but recommended)* — automatically loads the project environment variables from `.envrc`

### Local environment configuration

Before running any `just` command, local environment files must be created from the provided `.env.example` templates.

Copy and adjust the environment files according to your local setup:

```bash
cp .env.example .env
cp backend/.env.example backend/.env
cp bot-runtime/.env.example bot-runtime/.env
```

These are the required `.env` files to run the just commands successfully. Additionally, there are other 
`.env.example` files in [bot](#bot) and [bot-manager](#bot-manager). 
Those services also need their own .env configuration to run properly. 
Make sure all required environment variables are properly configured before continuing.

### direnv setup (recommended)

If you use `direnv`, allow the project environment from the repository root:

```bash
direnv allow
```

Otherwise, you can manually load the environment variables before running any `just` command, but this will
pollute your current environment:

```bash
source .env
source backend/.env
source bot-runtime/.env
```

## Architecture

The application is fully containerised using Docker and follows a microservice architecture, 
running each service in its own Docker container. The entire system is organised in a single 
monorepo. The microservices include:

### Infrastructure services

#### nginx
The application’s entry point, acting as a reverse proxy that exposes the
[backend](#backend), the
[bot-manager](#bot-manager), the
[tusd](#tusd) and the
[grafana](#grafana) services and protects against CORS attacks.

#### tusd
A file upload service that handles user uploads based on 
[tusd](https://github.com/tus/tusd), the official reference implementation
of tus, a protocol based on HTTP for resumable file uploads, written in Go.

#### redis
A key-value store used for caching and storing session data. It enables real-time communication between
[bot-manager](#bot-manager) and
[bot](#bot) services through pub/sub.

#### postgres
A relational database used for storing persistent data. It serves as the primary persistence layer.

### Core application logic services

#### backend
It provides a dashboard to manage the entire application using 
[Symfony](https://symfony.com/). It runs on `php-fpm`, and is served behind
[nginx](#nginx) over TCP using FastCGI protocol, and communicates through it using a reverse proxy with
[bot-manager](#bot-manager) establishing a WebSocket connection to control the userbot.

#### bot-manager
It listens for commands from WebSocket clients, managing and controlling
[bot](#bot) service processes through
[supervisor](https://supervisord.org/).

#### bot
It runs isolated processes that interact directly with the MTProto API; these processes do not
self-manage and report their status back to
[bot-manager](#bot-manager), who uses the status to validate
incoming commands and emits updates back again to the dashboard via WebSocket.

### Logging and monitoring services

#### alloy
A telemetry agent responsible for collecting and forwarding logs from the core services to
[loki](#loki) for centralized aggregation and analysis.

Shared common code used across multiple services resides in the **application** domain. Also, the
[bot](#bot) and the
[bot-manager](#bot-manager) services share a common **bot-runtime** domain.

Those are not standalone services but are installed inside the containers of the services that
depend on them.

#### loki
A log aggregation backend used to store, index, and query logs collected from the application's services.

#### grafana
A dashboarding service that visualizes and analyzes the logs collected by
[loki](#loki)

### Development environment services

#### dev
It provides testing and code linting utilities for the core services. To do so, it installs the following dependencies: 
- [composer](https://getcomposer.org/): to run composer scripts and to dynamically manage dependencies, as in the core containers on the development environment.
- [phpunit](https://phpunit.de/index.html), [mockery](https://github.com/mockery/mockery) and [xdebug](https://xdebug.org/): testing suite with code coverage analysis
- [phpstan](https://phpstan.org/): code static analysis tool
- [pint](https://laravel.com/docs/13.x/pint): code linter and style enforcer
- [rector](https://getrector.com/): code refactoring tool
- [npm](https://www.npmjs.com/): to run [tsc](https://www.typescriptlang.org/docs/handbook/compiler-options.html) and run TypeScript compiler type checks

#### vite
it compiles the frontend assets using [Vite](https://vitejs.dev/) and provides Hot Module Replacement (HMR) for development.
It installs [npm](https://www.npmjs.com/)
  
## Build phases

Each of the core business logic services has its own Dockerfile using a multi-stage build process.
[bot-manager](#bot-manager) and
[bot](#bot) are based on the `php:8.5-alpine` image, while
[backend](#backend) is based on the `php:8.5-fpm-alpine` image.

### Base stage
It installs runtime dependencies and required PHP extensions.

### Production stage
It copies service-specific code and shared code inside the container, installs composer and runs `composer install` 
to install dependencies. Composer is then removed to keep the image lean. This produces an immutable image with 
all dependencies pre-installed, ensuring reproducible deployments.

On the [backend](#backend) service image, there's also an intermediate previous stage that compiles the frontend assets using 
[Vite](https://vitejs.dev/) so they can be directly copied in the final production stage.

### Development stage
It installs `xdebug` and `composer` inside the container. Instead of copying code, volumes
mount the local codebases via Docker Compose. On container start, `composer install` runs dynamically to allow
live dependency management during development.


The decision of whether to use the production or the development images is based on the `APP_ENV` environment variable.
If it is set to `prod`, the production stage of Dockerfiles is used, creating immutable containers with pre-installed dependencies.
Otherwise, the development image is used, allowing dynamic dependency installation and live code updates.

## Application Management

All services are managed via Docker Compose. For convenience, a `Justfile` with helpful recipes is provided in the 
project root.

### Installation and setup

After git cloning the repository, run on the root of the project:

```bash
just init [--build] [--reset-deps] [--reset-cache]
```

It reinitialises the application by recreating containers and required runtime directories.

It will:
- Destroy existing containers
- Create required runtime directories
- Start the containers again

Options:
- `--reset-deps` (only on development): remove mounted node_modules and vendor directories
- `--reset-cache` (only on development): remove mounted var directory
- `--build`: rebuild containers before starting them

After that, run:

```bash
just setup
```

This will run the necessary database migrations and clear the Symfony cache.

There's also a deployment recipe, only runnable on production environments:

> 🔴 **PRODUCTION ONLY**
> This command cannot be used in development
```bash
just deploy [BRANCH=main]
```

This command fetches the latest changes from the remote repository, checks out the specified branch, 
and hard-resets the local repository to match origin/<branch>. If no branch is specified, the main branch is used.
Any uncommitted local changes will be permanently lost after running this command.

After that it runs:

```bash
just init --build
just setup
```

### Container lifecycle

Build containers (all of them if no service is specified):

```bash
just build [SERVICES...]
```

Start containers (all of them if no service is specified):

```bash
just up [SERVICES...]
just upd [SERVICES...]
```

The first one does it in foreground mode, while the second one does it in background detached mode.

Stop running containers (all of them if no service is specified):

```bash
just stop [SERVICES...]
```

Stop and remove containers (all of them if no service is specified):

```bash
just down [SERVICES...]
```

Restart containers (all of them if no service is specified):

```bash
just restart [SERVICES...]
```

### Debugging

Run a service in a temporary container (deleted after exit) with an interactive shell, bypassing the usual entrypoint 
(useful if the container build is failing):

```bash
just debug SERVICE
```

Open an interactive shell inside a running service container:

```bash
just sh [SERVICE]
```

[loki](#loki) cannot be accessed using a `just ssh loki` command because loki is a distroless service, so it doesn't have a 
built-in shell.

For [redis](#redis) there's also the `just redis-cli` command, and for [postgres](#postgres) there's also the
`just psql` command. Those helpers are just shortcuts for running the respective CLI tools inside the container,
instead of a shell.

### Code quality

> 🧪 **DEVELOPMENT ONLY**
> This command cannot be used in production
```bash
just code-quality [SERVICES...]
```
This command is the equivalent of running all the following commands:

```bash
just phpunit [SERVICES...]
just phpstan [SERVICES...]
just pint --test [SERVICES...]
just rector --dry-run [SERVICES...]
just tsc [SERVICES...]
```

The following sections describe each command in detail.

#### phpstan
Code static analysis:

> 🧪 **DEVELOPMENT ONLY**
> This command cannot be used in production
```bash
just phpstan [SERVICES...] [--no-progress]
```
Services
- application (runtime library)
- bot-runtime (runtime library)
- [bot](#bot)
- [bot-manager](#bot-manager)
- [backend](#backend)
- [dev](#dev)

(Default: all of them if none is specified)

Options:
- `--no-progress`: disable the progress bar and show only the errors

#### pint
Code linting:

> 🧪 **DEVELOPMENT ONLY**
> This command cannot be used in production
```bash
just pint [SERVICES...] [--test]
```
Services
- application (runtime library)
- bot-runtime (runtime library)
- [bot](#bot)
- [bot-manager](#bot-manager)
- [backend](#backend)
- [dev](#dev)

(Default: all of them if none is specified)

Options:
- `--test`: only show the suggested code changes to follow the [PER](https://www.php-fig.org/per/coding-style/) 
coding style, without applying them.

#### rector
Code refactoring:

> 🧪 **DEVELOPMENT ONLY**
> This command cannot be used in production
```bash
just rector [SERVICES...] [--dry-run]
```
Services
- application (runtime library)
- bot-runtime (runtime library)
- [bot](#bot)
- [bot-manager](#bot-manager)
- [backend](#backend)
- [dev](#dev)

(Default: all of them if none is specified)

Options:
- `--dry-run`: only show the suggested refactorings, without applying them

#### phpunit
PHP tests:

> 🧪 **DEVELOPMENT ONLY**
> This command cannot be used in production
```bash
just phpunit [SERVICES...] [--filter EXPRESSION] [--debug] [--coverage-text] [--coverage-clover]
```
Services
- application (runtime library)
- bot-runtime (runtime library)
- [bot](#bot)
- [bot-manager](#bot-manager)
- [backend](#backend)
- [dev](#dev)

(Default: all of them if none is specified)

Options:
- `--filter EXPRESSION`: run only tests that match the given expression in the given services
- `--debug`: enable the ability to set breakpoints on tests
- `--coverage-text`: add text coverage through the CLI
- `--coverage-clover`: add clover coverage (useful during CI/CD pipelines)

The tests will normally run with `Opcache` enabled, but the `--debug`, `--coverage-text` and `--coverage-clover` flags 
will disable it and enable `XDebug`.

#### tsc
TypeScript compiler type checks:

> 🧪 **DEVELOPMENT ONLY**
> This command cannot be used in production
```bash
just tsc [SERVICES...]
```
Services
- [backend](#backend)

(Default: all of them if none is specified)

### Dependency integrity checking

```bash
just check-dependencies [SERVICES...]
```
It will check that composer.json and composer.lock are in sync in:
- application (runtime library)
- bot-runtime (runtime library)
- [bot](#bot)
- [bot-manager](#bot-manager)
- [backend](#backend)
- [dev](#dev)

It will check that package.json and package-json.lock are in sync in:
- [backend](#backend)

### Repository status checking
```bash
just check-repository [-f]
```
It checks:
- if staged files have CRLF line endings
- if dependencies are in sync in:
  - application (runtime library)
  - bot-runtime (runtime library)
  - [bot](#bot)
  - [bot-manager](#bot-manager)
  - [backend](#backend)
- if package.json and package-json.lock are in sync in:
  - [backend](#backend)
- if the require section of the dev/composer.json is in sync with required php extensions from the services
- if dependencies are in sync in:
    - [dev](#dev)

Options:
- `-f`: Automatically convert CRLF line endings to LF and git add the modified files.
        Force update the dev/composer.json with the missing php extensions from services.
        Automatically update composer.lock and add composer.json and composer.lock to the git staged files

