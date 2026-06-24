<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class ApiController extends AbstractController
{
    protected function jsonError(string $message, int $status, array $extra = []): JsonResponse
    {
        return $this->json(['error' => $message] + $extra, $status);
    }

    protected function requireFields(array $payload, array $fields): ?JsonResponse
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $payload) || $payload[$field] === '' || $payload[$field] === null) {
                return $this->jsonError(sprintf('Field "%s" is required.', $field), 422);
            }
        }

        return null;
    }

    protected function validationErrorResponse(ConstraintViolationListInterface $violations): ?JsonResponse
    {
        if ($violations->count() === 0) {
            return null;
        }

        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $this->jsonError('Validation failed.', 422, ['fields' => $errors]);
    }
}
