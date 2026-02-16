<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Configuration;

use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final readonly class RouteConfigurator
{
    public function __construct(
        private string $configDirectory,
        private Environment $environment,
    ) {}

    public function configure(RoutingConfigurator $routingConfigurator): void
    {
        $routingConfigurator->import(resource: $this->configDirectory . '/routes/*.yaml');

        $routingConfigurator->import(
            resource: $this->configDirectory . '/routes/' . $this->environment->value . '/*.yaml',
            ignoreErrors: true,
        );
    }
}
