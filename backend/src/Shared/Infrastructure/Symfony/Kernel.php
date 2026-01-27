<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Configuration\ServiceConfigurator;
use olml89\TelegramUserbot\Backend\Shared\Infrastructure\Symfony\Configuration\RouteConfigurator;
use olml89\TelegramUserbot\Shared\App\Environment\Environment;
use Sentry\SentryBundle\SentryBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private readonly Environment $env;

    public function __construct(Environment $env, ?bool $debug = null)
    {
        $this->env = $env;

        parent::__construct(environment: $env->value, debug: $debug ?? $env->isDebuggable());
    }

    /**
     * @return iterable<int, BundleInterface>
     */
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new MonologBundle();
        yield new TwigBundle();
        yield new DoctrineBundle();
        yield new DoctrineMigrationsBundle();

        if ($this->env === Environment::Development) {
            yield new WebProfilerBundle();
        }

        if ($this->env === Environment::Production) {
            yield new SentryBundle();
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        /**
         * Register all classes in src as services, setting up autowiring by default
         */
        $container
            ->services()
            ->load(
                namespace: 'olml89\\TelegramUserbot\\Backend\\',
                resource: $this->getProjectDir() . '/src/*',
            )
            ->autowire()
            ->autoconfigure();

        /**
         * Load framework, packages, services and contexts configuration
         */
        $container->import(resource: $this->getConfigDir() . '/framework.yaml');
        new ServiceConfigurator($this->getConfigDir() . '/packages', $this->env)->configure($container);
        new ServiceConfigurator($this->getConfigDir() . '/services', $this->env)->configure($container);
        new ServiceConfigurator($this->getConfigDir() . '/contexts', $this->env)->configure($container);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        new RouteConfigurator($this->getConfigDir(), $this->env)->configure($routes);
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__, levels: 4);
    }
}
