<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('./Core/di/di.php');
        $container->import('../config/{packages}/*.yaml');
        $container->import("../config/{packages}/{$this->environment}/*.yaml");
        $container->import('./**/{di}.php');
        $container->import("./**/di/{di}_{$this->environment}.php");
    }
}
