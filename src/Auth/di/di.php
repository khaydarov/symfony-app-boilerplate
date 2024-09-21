<?php

declare(strict_types=1);

namespace App\User;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Auth\\', '../../Auth/*')
        ->exclude('../di/*');
};
