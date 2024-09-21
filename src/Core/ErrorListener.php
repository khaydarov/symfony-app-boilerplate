<?php

declare(strict_types=1);

namespace App\Core;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class ErrorListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(
                new JsonResponse([
                    'message' => 'Route not found',
                ])
            );
        } elseif ($exception instanceof JWTDecodeFailureException) {
            $event->setResponse(
                new JsonResponse([
                    'message' => 'JWT Decode error. Please, log out and auth again',
                ], 401)
            );
        } else {
            $message = sprintf(
                'Error: %s with code: %s',
                $exception->getMessage(),
                $exception->getCode()
            );

            $event->setResponse(
                new JsonResponse([
                    'message' => $message,
                ])
            );

            $this->logger->error($message, [
                'exception' => $exception,
            ]);
        }
    }
}
