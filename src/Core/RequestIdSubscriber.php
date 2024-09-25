<?php

declare(strict_types=1);

namespace App\Core;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Uid\Uuid;

class RequestIdSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest'],
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->headers->has('X-Request-ID')) {
            $uuid = Uuid::v4();
            $request->headers->set('X-Request-ID', $uuid->toString());
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();
        if (!$response->headers->has('X-Request-ID') && $request->headers->has('X-Request-ID')) {
            $response->headers->set('X-Request-ID', $request->headers->get('X-Request-ID'));
        }
    }
}
