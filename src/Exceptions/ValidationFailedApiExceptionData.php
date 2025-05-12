<?php

namespace App\Exceptions;

use Symfony\Component\Validator\ConstraintViolationList;

class ValidationFailedApiExceptionData extends ApiExceptionData
{
    private ConstraintViolationList $violations;

    public function __construct(
        int $statusCode,
        ConstraintViolationList $violations,
        string $type = 'ConstraintViolationList',
    )
    {
        parent::__construct($statusCode, $type);

        $this->violations = $violations;
    }

    public function getViolationsArray(): array
    {
        $violations = [];

        foreach ($this->getViolations() as $violation) {

            $violations[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ];
        }

        return $violations;
    }

    public function toArray(): array
    {
        return [
            'type' => 'ConstraintViolationList',
            'violations' => $this->getViolationsArray()
        ];
    }

    public function getViolations(): ConstraintViolationList
    {
        return $this->violations;
    }
}