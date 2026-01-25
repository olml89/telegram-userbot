<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Configuration;

use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final readonly class ServiceConfigurator
{
    public function __construct(
        private string $configDirectory,
        private Environment $environment,
    ) {
    }

    public function configure(ContainerConfigurator $containerConfigurator): void
    {
        $containerConfigurator->import(resource: $this->configDirectory . '/services/*.yaml');

        $containerConfigurator->import(
            resource: $this->configDirectory . '/services/' . $this->environment->value . '/*.yaml',
            ignoreErrors: true,
        );
    }
}
