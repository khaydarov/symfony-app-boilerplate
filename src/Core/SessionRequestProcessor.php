<?php

declare(strict_types=1);

namespace App\Core;

use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionRequestProcessor
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request && $request->headers->has('X-Request-ID')) {
            $record->extra['request_id'] = $request->headers->get('X-Request-ID');
        }

        return $record;
    }
}
