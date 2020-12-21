<?php

declare(strict_types=1);

namespace Gtd\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * {@inheritdoc}
     */
    public function getProjectDir()
    {
        return \dirname(__DIR__, 2);
    }

    protected function build(ContainerBuilder $container)
    {
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container
            ->parameters()
            ->set('container.autowiring.strict_mode', true)
            ->set('container.dumper.inline_class_loader', false)
            ->set('container.dumper.inline_factories', true)
        ;

        $confDir = $this->getProjectDir() . '/config';

        $container->import($confDir . '/{packages}/*.yaml');
        $container->import($confDir . '/{packages}/' . $this->environment . '/*.yaml');
        $container->import($confDir . '/{services}.yaml');
        // @todo importing the whole folder doesn't ensure ordering.
        // $container->import($confDir . '/{services}/*.yaml');
        $container->import($confDir . '/{services}/corebus.core.yaml');
        $container->import($confDir . '/{services}/corebus.makinacorpus-goat-adapter.yaml');
        $container->import($confDir . '/{services}_' . $this->environment . '.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir() . '/config';

        $routes->import($confDir . '/{routes}/' . $this->environment . '/*.yaml');
        $routes->import($confDir . '/routes.yaml');
    }
}
