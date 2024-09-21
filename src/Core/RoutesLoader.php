<?php

declare(strict_types=1);

namespace App\Core;

use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Loader\AttributeFileLoader;
use Symfony\Component\Routing\RouteCollection;

class RoutesLoader
{
    public function load(): RouteCollection
    {
        $routes = new RouteCollection();
        $dir = dirname(__DIR__);
        $finder = new Finder();
        $finder
            ->files()
            ->in($dir . '*/')
            ->name("*Controller.php");

        $loader = new AttributeFileLoader(
            new FileLocator($dir),
            new AttributeRouteControllerLoader()
        );

        foreach ($finder as $file) {
            $routes->addCollection($loader->load($file->getPathname()));
        }

        return $routes;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'custom' === $type;
    }
}
