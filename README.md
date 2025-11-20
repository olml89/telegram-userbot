![PHPUnit](https://github.com/olml89/telegram-userbot/actions/workflows/phpunit.yml/badge.svg?branch=main)
[![codecov](https://codecov.io/gh/olml89/telegram-userbot/branch/main/graph/badge.svg)](https://codecov.io/gh/olml89/telegram-userbot)
![PHPStan level:max](https://github.com/olml89/telegram-userbot/actions/workflows/phpstan.yml/badge.svg?branch=main)
![Laravel Pint](https://github.com/olml89/telegram-userbot/actions/workflows/pint.yml/badge.svg?branch=main)

# Telegram Userbot

This application manages the lifecycle and behaviour of a Telegram userbot interacting directly 
with the MTProto API, similarly to an official app, using 
[MadelineProto 8](https://docs.madelineproto.xyz/index.html).

## Table of Contents

- [Architecture](#architecture)
- [Build Phase](#build-phase)
- [Application Management](#application-management)
    - [Starting the Application](#starting-the-application)
    - [Development](#development)
    - [Debugging](#debugging)

## Architecture

The application is fully containerised using Docker and follows a microservice architecture, 
running each service in its own Docker container. The entire system is organised in a single 
monorepo. The microservices include:

- **nginx**: the application’s entry point, acting as a reverse proxy that exposes the **backend** 
and **bot-manager** services and protects against CORS attacks.

- **redis** and **postgres**: data storage infrastructure. **postgres** serves as the primary 
persistence layer, while **redis** enables real-time communication between **bot-manager** 
and **bot** services through pub/sub.

- **loki**, **grafana**, and **alloy**: the centralized logging and monitoring stack, 
providing log collection, aggregation, and visualization.

- **backend**, **bot-manager**, **bot**: core business logic services.

    - **backend** is served behind **nginx**, providing a dashboard to manage the entire application. 
    It maintains a WebSocket connection to **bot-manager** (via **nginx** as a reverse proxy) to control the userbot.
    It runs on `php-fpm`, and **nginx** communicates with it over TCP using the FastCGI protocol.
  
    - **bot-manager** listens for commands from WebSocket clients, managing and controlling **bot** service 
    processes through Supervisor.
  
    - **bot** runs isolated processes that interact directly with the MTProto API; these processes do not 
    self-manage and report their status back to **bot-manager**. The **bot-manager** uses the status to validate 
    incoming commands and emits updates to the dashboard via WebSocket.
  
- **dev**: a container that only runs on development environment. It provides testing and debugging utilities
    for the core services. To do so, it installs the following dependencies: 
    - `composer`: to run composer scripts and to dynamically manage dependencies, as in the core containers on 
    development environment.
    - `phpunit`, `mockery` and `xdebug`: testing suite with code coverage analysis
    - `phpstan`: code static analysis tool
    - `laravel pint`: code linter and style enforcer
  

## Build Phase

Shared common code and dependencies used by core the business logic services reside in the **shared** domain. 
This shared code is not a standalone service but is installed inside the containers of the services that 
depend on it.

Each of the core business logic services has its own Dockerfile using a multi-stage build process.
**bot-manager** and **bot** are based on the `php:8.4-alpine` image, while **backend** is based on the
`php:8.4-fpm-alpine` image.

- **Base stage**: installs runtime dependencies and required PHP extensions.

- **Production stage**: copies service-specific code and shared code inside the container and runs `composer install` 
to install dependencies. Composer is then removed to keep the image lean. This produces an immutable image with 
all dependencies pre-installed, ensuring reproducible deployments.

- **Development stage**: installs `xdebug` and `composer` inside the container. Instead of copying code, volumes 
mount the local codebases via Docker Compose. On container start, `composer install` runs dynamically to allow 
live dependency management during development.

## Application Management

All services are managed via Docker Compose. For convenience, a `Makefile` with helpful recipes is provided in the 
project root.

### Starting the Application

This command builds (if needed) and starts all containers:

```bash
make upd
```

To build the images without running the containers:

```bash
make build
```

The environment is controlled by the `APP_ENV` variable.

- If set to `production`, the production stage of Dockerfiles is used, creating immutable containers with 
pre-installed dependencies.

- For other environments, the development stage is used, allowing dynamic dependency installation and live code updates.

To stop running containers:

```bash
make stop
```

To stop and remove containers:

```bash
make down
```

### Development

Restart a single service without impacting others:

```bash
make restart [service]
```

Run a service in a temporary container (deleted after exit) with an interactive shell, bypassing the usual entrypoint 
(useful if the container build is failing):

```bash
make debug [service]
```

Open an interactive shell inside a running service container:

```bash
make [service]
```

For **redis** and **postgres**, this command opens the respective CLI tools (`redis-cli` and `psql`) 
instead of a shell.

### Debugging

The following commands can target a specific service, or be run in all the core services if no service is specified.

As explained before, this will open the shell of the **dev** container, from where all the debugging commands
can be run.

```bash
make dev
```

But there's also this list of useful make commands to use the tools from outside the containers.
An optional service argument can be passed to apply them to a single service; if no service argument is
passed, they will be run in all the services.

Code static analysis:

```bash
make phpstan [?service]
```

Code linting:

```bash
make pint [?service] [?test]
```

Without the optional `test` flag it will only show the suggested code changes to follow `PSR-12` code style. 
Passing the flag will actually apply them.

Unit tests:

```bash
make phpunit [?service] [?filter] [?debug] [?coverage] [?--ci]
```

The optional `filter` flag will restrict the tests to be run to those that match it as a pattern.

The tests will normally be run with `Opcache` enabled. The `debug`, `coverage` and `ci` filters will disable
it and enable `XDebug`: The first one can be used to set breakpoints on tests, the second one will add
text coverage through the CLI and the third one will add clover coverage (useful during CI/CD pipelines).
