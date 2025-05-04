<?php

namespace App\EventListener;

use App\Events\TicketStatusNotFoundEvent;
use App\Exceptions\ApiException;
use App\Exceptions\UndefinedEntityFieldException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;


final class ExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $ex = $event->getThrowable();

        if ($ex instanceof ApiException) {
            $exceptionData = $ex->getExceptionData();
            $errorResponse = new JsonResponse($exceptionData->toArray(), $exceptionData->getStatusCode());

            $event->setResponse($errorResponse);
        } elseif ($ex instanceof UnprocessableEntityHttpException) {
            $errorResponse = new JsonResponse([
                'type' => 'BadValidationMessage',
                'message' => $ex->getMessage()
            ], $ex->getStatusCode());

            $event->setResponse($errorResponse);
        }
    }

    #[AsEventListener(event: TicketStatusNotFoundEvent::class)]
    public function onTicketStatusNotFoundEvent(TicketStatusNotFoundEvent $event): void
    {
        $exceptionData = new UndefinedEntityFieldException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'BadTicketField',
            'The status field of ticket is not defined'
        );

        throw new ApiException($exceptionData);
    }
}
