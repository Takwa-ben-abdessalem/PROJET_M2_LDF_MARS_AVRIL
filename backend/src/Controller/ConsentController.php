<?php

namespace App\Controller;

use App\Entity\ConsentLog;
use App\Service\ConsentLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ConsentController extends ApiController
{
    #[Route('/api/consent', methods: ['POST'])]
    public function store(Request $request, ConsentLogger $logger, EntityManagerInterface $entityManager): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?: [];
        $action = $payload['action'] ?? null;

        if (!in_array($action, [ConsentLog::CONSENT_GIVEN, ConsentLog::CONSENT_WITHDRAWN], true)) {
            return $this->jsonError('Invalid consent action.', 422);
        }

        $log = $logger->log($this->getUser(), $action, $payload['details'] ?? 'Mise a jour du consentement.');

        if ($action === ConsentLog::CONSENT_GIVEN) {
            $this->getUser()->setConsentDate(new \DateTimeImmutable());
            $this->getUser()->setConsentVersion($payload['version'] ?? 'v1.0');
        }

        $entityManager->flush();

        return $this->json([
            'id' => $log->getId(),
            'action' => $log->getAction(),
            'timestamp' => $log->getTimestamp()->format(DATE_ATOM),
        ], 201);
    }
}
