<?php

namespace App\Controller;

use App\Entity\ConsentLog;
use App\Entity\CookiePreference;
use App\Entity\User;
use App\Repository\ConsentLogRepository;
use App\Repository\CookiePreferenceRepository;
use App\Service\ConsentLogger;
use App\Service\UserAnonymizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/me')]
class MeController extends ApiController
{
    #[Route('', methods: ['GET'])]
    public function show(ConsentLogger $logger, EntityManagerInterface $entityManager): JsonResponse
    {
        $logger->log($this->getUser(), ConsentLog::DATA_ACCESSED, 'Consultation des donnees personnelles.');
        $entityManager->flush();

        return $this->json($this->serializeUser($this->getUser()));
    }

    #[Route('', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $entityManager, ConsentLogger $logger): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?: [];
        $user = $this->getUser();

        if (isset($payload['firstName'])) {
            $user->setFirstName($payload['firstName']);
        }
        if (isset($payload['lastName'])) {
            $user->setLastName($payload['lastName']);
        }
        if (array_key_exists('phone', $payload)) {
            $user->setPhone($payload['phone']);
        }

        $logger->log($user, ConsentLog::DATA_ACCESSED, 'Rectification des donnees personnelles.');
        $entityManager->flush();
        return $this->json($this->serializeUser($user));
    }

    #[Route('', methods: ['DELETE'])]
    public function anonymize(UserAnonymizer $anonymizer, EntityManagerInterface $entityManager): JsonResponse
    {
        $anonymizer->anonymize($this->getUser());
        $entityManager->flush();

        return $this->json(['message' => 'Account anonymized.']);
    }

    #[Route('/consent-logs', methods: ['GET'])]
    public function logs(ConsentLogRepository $logs): JsonResponse
    {
        $items = $logs->findBy(['user' => $this->getUser()], ['timestamp' => 'DESC']);

        return $this->json(array_map(fn (ConsentLog $log) => [
            'id' => $log->getId(),
            'action' => $log->getAction(),
            'timestamp' => $log->getTimestamp()->format(DATE_ATOM),
            'ipAddress' => $log->getIpAddress(),
            'details' => $log->getDetails(),
        ], $items));
    }

    #[Route('/cookie-preferences', methods: ['GET'])]
    public function cookiePreferences(CookiePreferenceRepository $preferences): JsonResponse
    {
        $preference = $preferences->findOneBy(['user' => $this->getUser()]);

        return $this->json($this->serializeCookiePreference($preference));
    }

    #[Route('/cookie-preferences', methods: ['PUT'])]
    public function updateCookiePreferences(
        Request $request,
        CookiePreferenceRepository $preferences,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true) ?: [];
        /** @var User $user */
        $user = $this->getUser();
        $preference = $preferences->findOneBy(['user' => $user]) ?? (new CookiePreference())->setUser($user);

        $preference
            ->setNecessary(true)
            ->setStatistics((bool) ($payload['statistics'] ?? $payload['stats'] ?? false))
            ->setMarketing((bool) ($payload['marketing'] ?? false))
            ->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($preference);
        $entityManager->flush();

        return $this->json($this->serializeCookiePreference($preference));
    }

    #[Route('/export', methods: ['GET'])]
    public function export(ConsentLogRepository $logs): JsonResponse
    {
        return $this->json([
            'profile' => $this->serializeUser($this->getUser()),
            'consentLogs' => array_map(fn (ConsentLog $log) => [
                'action' => $log->getAction(),
                'timestamp' => $log->getTimestamp()->format(DATE_ATOM),
                'details' => $log->getDetails(),
            ], $logs->findBy(['user' => $this->getUser()], ['timestamp' => 'DESC'])),
        ]);
    }

    private function serializeUser($user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phone' => $user->getPhone(),
            'roles' => $user->getRoles(),
            'consentDate' => $user->getConsentDate()->format(DATE_ATOM),
            'consentVersion' => $user->getConsentVersion(),
            'isAnonymized' => $user->isAnonymized(),
            'createdAt' => $user->getCreatedAt()->format(DATE_ATOM),
            'lastActivityAt' => $user->getLastActivityAt()?->format(DATE_ATOM),
        ];
    }

    private function serializeCookiePreference(?CookiePreference $preference): array
    {
        if (!$preference) {
            return [
                'necessary' => true,
                'statistics' => false,
                'marketing' => false,
                'updatedAt' => null,
            ];
        }

        return [
            'necessary' => true,
            'statistics' => $preference->isStatistics(),
            'marketing' => $preference->isMarketing(),
            'updatedAt' => $preference->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}
