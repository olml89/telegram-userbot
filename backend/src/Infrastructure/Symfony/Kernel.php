<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @return iterable<int, BundleInterface>
     */
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(resource: $this->getProjectDir() . '/config/framework.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(resource: $this->getProjectDir() . '/config/routes.yaml');
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__, levels: 3);
    }
}
