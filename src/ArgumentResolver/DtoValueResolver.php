<?php

namespace App\ArgumentResolver;

use App\DTO\DtoResolvedInterface;
use App\Exceptions\ApiException;
use App\Exceptions\ValidationFailedApiExceptionData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsTargetedValueResolver('dto')]
readonly class DtoValueResolver implements ValueResolverInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface    $validator
    )
    {
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() && str_starts_with($argument->getType(), DtoResolvedInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        try {
            $dtoValue = $this->denormalizer->denormalize($request->getPayload()->all(), $argument->getType(), 'json');
        } catch (NotNormalizableValueException $e) {
            throw new BadRequestHttpException("Error when normalizing input data");
        }

        $violations = $this->validator->validate($dtoValue);
        if ($violations->count() > 0) {
            $exceptionData = new ValidationFailedApiExceptionData(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                violations: $violations
            );

            throw new ApiException($exceptionData);
        }

        yield $dtoValue;
    }

}