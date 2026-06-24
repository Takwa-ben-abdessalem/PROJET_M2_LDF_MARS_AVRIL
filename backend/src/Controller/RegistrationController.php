<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Registration;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends ApiController
{
    #[Route('/api/events/{id}/register', methods: ['POST'])]
    public function register(Event $event, RegistrationRepository $registrations, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$event->isPublished()) {
            return $this->jsonError('Event not found.', 404);
        }

        $existing = $registrations->findOneBy(['user' => $this->getUser(), 'event' => $event]);
        if ($existing && $existing->getStatus() !== Registration::STATUS_CANCELLED) {
            return $this->jsonError('Already registered.', 409);
        }

        if ($event->confirmedRegistrationsCount() >= $event->getMaxParticipants()) {
            return $this->jsonError('Event is full.', 409);
        }

        $registration = $existing ?? (new Registration())->setUser($this->getUser())->setEvent($event);
        $registration->setStatus(Registration::STATUS_CONFIRMED);
        $entityManager->persist($registration);
        $entityManager->flush();

        return $this->json($this->serializeRegistration($registration), 201);
    }

    #[Route('/api/me/registrations', methods: ['GET'])]
    public function mine(RegistrationRepository $registrations): JsonResponse
    {
        return $this->json(array_map([$this, 'serializeRegistration'], $registrations->findActiveForUser($this->getUser())));
    }

    #[Route('/api/registrations/{id}', methods: ['DELETE'])]
    public function cancel(Registration $registration, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($registration->getUser() !== $this->getUser()) {
            return $this->jsonError('Forbidden.', 403);
        }

        $registration->setStatus(Registration::STATUS_CANCELLED);
        $entityManager->flush();

        return $this->json($this->serializeRegistration($registration));
    }

    private function serializeRegistration(Registration $registration): array
    {
        $event = $registration->getEvent();

        return [
            'id' => $registration->getId(),
            'registeredAt' => $registration->getRegisteredAt()->format(DATE_ATOM),
            'status' => $registration->getStatus(),
            'event' => [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'eventDate' => $event->getEventDate()->format(DATE_ATOM),
                'location' => $event->getLocation(),
            ],
        ];
    }
}
