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

- [Prerequisites](#architecture)
- [Architecture](#architecture)
- [Build phases](#build-phases)
- [Application Management](#application-management)
    - [Installation](#installation-and-setup)
    - [Container lifecycle](#container-lifecycle)
    - [Debugging](#debugging)
    - [Code quality](#code-quality)

## Prerequisites

This project uses a `Justfile` as the main task runner for local development and common workflows and on 
CI/CD pipelines to automate the deployment process.

Before getting started, make sure the following tools are installed on your system:

- [just](https://github.com/casey/just) — command runner used throughout the project

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
just install
```

This will freshly start the containers in a clean state. After that, run:

```bash
just setup
```

This will run the necessary database migrations and clear the Symfony cache.

There's also a deployment recipe for production environments:

```bash
just deploy
```

This command fetches the latest changes from the remote repository, checks out the target branch, 
and hard-resets the local repository to match origin/<branch>.

⚠️ Any uncommitted local changes will be permanently lost after running this command.

After that it runs `just install` and `just setup` as shown above to rebuild and configure the application.

### Container lifecycle

This command builds all the containers or a specified one:

```bash
just build [?container]
```

This commands and start all the containers or a specified one. The first one does it in foreground mode,
the second one in background detached mode:

```bash
just upd [?container]
just upd [?container]
```

To stop running containers or a specified one:

```bash
just stop [?container]
```

To stop and remove containers or a specified one:

```bash
just down [?container]
```

### Debugging

Restart a single service without impacting others:

```bash
just restart [service]
```

Run a service in a temporary container (deleted after exit) with an interactive shell, bypassing the usual entrypoint 
(useful if the container build is failing):

```bash
just debug [service]
```

Open an interactive shell inside a running service container:

```bash
just [service]
```

[loki](#loki) cannot be accessed using a `just loki` command because loki is a distroless service, so it doesn't have a 
built-in shell.

For [redis](#redis) there's also the `just redis-cli` command, and for [postgres](#postgres) there's also the
`just postgres-psql` command. Those helpers are just shortcuts for running the respective CLI tools inside the container,
instead of a shell.

### Code quality

The following commands can target a specific service or be run in all the core services if no service is specified.

Code static analysis:

```bash
just phpstan [?service] [?--no-progress]
```

The `--no-progress` flag will disable the progress bar and show only the errors.

Code linting:

```bash
just pint [?service] [?--test]
```

Without the optional `--test` flag it will only show the suggested code changes to follow `PSR-12` code style. 
Passing the flag will actually apply them.

Code refactoring:

```bash
just rector [?service] [?--dry-run]
```

Without the optional `--dry-run` flag it will only show the suggested refactorings.
Passing the flag will actually apply them.

Unit tests:

```bash
just phpunit [?service] [?--filter] [?--debug] [?--coverage-text] [?--coverage-clover]
```

The optional `--filter` flag will restrict the tests to be run to those that match it as a pattern in 
a given service.

The tests will normally be run with `Opcache` enabled, but the following flags will disable it and enable `XDebug`:
- `--debug` can be used to set breakpoints on tests
- `--coverage-text` will add text coverage through the CLI
- `--coverage-clover` will add clover coverage (useful during CI/CD pipelines)
