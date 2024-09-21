<?php

declare(strict_types=1);

namespace App\Core;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(ErrorListener::class)
        ->tag('kernel.event_listener', ['event' => 'kernel.exception']);

    $services->set(RoutesLoader::class)->tag('routing.route_loader');
};