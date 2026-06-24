<?php

namespace App\EventSubscriber;

use App\Entity\ConsentLog;
use App\Entity\User;
use App\Service\ConsentLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DataAccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly ConsentLogger $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => 'onResponse'];
    }

    public function onResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $user = $this->security->getUser();

        if (!$user instanceof User || $request->getMethod() !== 'GET') {
            if ($user instanceof User) {
                $user->setLastActivityAt(new \DateTimeImmutable());
                $this->entityManager->flush();
            }
            return;
        }

        $user->setLastActivityAt(new \DateTimeImmutable());

        if (in_array($request->getPathInfo(), ['/api/me/export', '/api/me/consent-logs'], true)) {
            $this->logger->log($user, ConsentLog::DATA_ACCESSED, 'Acces RGPD: '.$request->getPathInfo());
        }

        $this->entityManager->flush();
    }
}
