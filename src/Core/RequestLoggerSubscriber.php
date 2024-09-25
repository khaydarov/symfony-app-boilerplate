<?php

declare(strict_types=1);

namespace App\Core;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class RequestLoggerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

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
        $this->logger->info(
            sprintf('Request: %s %s?%s from %s (%s)',
                $request->getMethod(),
                $request->getPathInfo(),
                $request->getQueryString(),
                $request->getClientIp(),
                $request->headers->get('User-Agent'),
            ),
        );
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $this->logger->info(
            sprintf('Response: %s %s',
                $response->getStatusCode(),
                $response->getContent(),
            )
        );
    }
}
