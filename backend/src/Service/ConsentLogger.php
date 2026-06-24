<?php

namespace App\Service;

use App\Entity\ConsentLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ConsentLogger
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        private readonly string $appSecret,
    ) {
    }

    public function log(?User $user, string $action, string $details): ConsentLog
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request?->getClientIp() ?? 'unknown';

        $log = (new ConsentLog())
            ->setUser($user)
            ->setAction($action)
            ->setIpAddress(hash('sha256', $ip.$this->appSecret))
            ->setDetails($details);

        $this->entityManager->persist($log);
        return $log;
    }
}
