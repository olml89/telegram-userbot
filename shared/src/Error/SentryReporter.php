<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Shared\Error;

use olml89\TelegramUserbot\Shared\App\AppConfig;
use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Sentry\ClientBuilder;
use Sentry\State\Hub;
use Sentry\State\HubInterface;
use Throwable;

final readonly class SentryReporter
{
    private Environment $environment;
    private HubInterface $sentry;

    public function __construct(AppConfig $appConfig, SentryConfig $sentryConfig)
    {
        $this->environment = $appConfig->environment;

        $clientBuilder = ClientBuilder::create([
            'dsn' => $sentryConfig->dsn,
        ]);

        $this->sentry = new Hub($clientBuilder->getClient());
    }

    public function report(Throwable $exception): void
    {
        if ($this->environment === Environment::Production) {
            $this->sentry->captureException($exception);
        }
    }
}
