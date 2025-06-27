<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

class ExceptionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger, private string $env)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => [['onException']]];
    }

    public function onException(ExceptionEvent $event): void
    {
        if (false === $event->isMainRequest() || 'json' !== $event->getRequest()->getContentTypeFormat()) {
            return;
        }

        $event->setResponse($this->getResponse($event->getThrowable()));
    }

    private function getResponse(Throwable $exception): Response
    {
        while ($exception instanceof HandlerFailedException && null !== $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }

        if ($exception instanceof MessengerValidationFailedException || $exception instanceof ValidationFailedException) {
            return new JsonResponse(['error' => ['message' => 'Validation error']], 400);
        }

        if ($exception instanceof HttpExceptionInterface) {
            return new JsonResponse(['error' => ['message' => $exception->getMessage()]], $exception->getStatusCode());
        }

        if ('dev' === $this->env) {
            return new JsonResponse([
                'error' => [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace()
                ]
            ], 500);
        }

        return new JsonResponse(['error' => ['message' => 'Internal server error']], 500);
    }
}
