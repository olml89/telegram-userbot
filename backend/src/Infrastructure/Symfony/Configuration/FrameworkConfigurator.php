<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony\Configuration;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final readonly class FrameworkConfigurator
{
    public function __construct(
        private string $configDirectory,
    ) {
    }

    public function configure(ContainerConfigurator $containerConfigurator): void
    {
        $containerConfigurator->import(resource: $this->configDirectory . '/framework.yaml');
    }
}
