<?php

namespace App\EventListener;

use App\Exceptions\ApiException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
            $errorResponse = new JsonResponse(
                data: $exceptionData->toArray(),
                status: $exceptionData->getStatusCode()
            );

            $event->setResponse($errorResponse);
        } elseif ($ex instanceof HttpException) {
            $event->setResponse($this->makeErrorResponse(
                message: $ex->getMessage(),
                statusCode: $ex->getStatusCode()
            ));
        }
    }

    private function makeErrorResponse(string $message, string $statusCode): JsonResponse
    {
        return new JsonResponse([
            'message' => $message,
            'statusCode' => $statusCode
        ], $statusCode);
    }
}
