<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Symfony;

use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
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
        yield new TwigBundle();

        if ($this->getEnvironment() === Environment::Development->value) {
            yield new WebProfilerBundle();
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        /**
         * Load framework configuration
         */
        $container->import(resource: $this->getConfigDir() . '/framework.yaml');

        /**
         * Load packages (Symfony related services)
         * Overwrite with current environment packages configuration
         */
        $container->import(resource: $this->getConfigDir() . '/packages/**/*.yaml');
        $container->import(resource: $this->getConfigDir() . '/packages/' . $this->getEnvironment() . '/**/*.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        /**
         * Load routes
         * Overwrite with current environment routes
         */
        $routes->import(resource: $this->getConfigDir() . '/routes/**/*.yaml');
        $routes->import(resource: $this->getConfigDir() . '/routes/' . $this->getEnvironment() . '/**/*.yaml');
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__, levels: 3);
    }
}
