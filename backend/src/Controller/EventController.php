<?php

namespace App\Controller;

use App\Dto\EventRequest;
use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/events')]
class EventController extends ApiController
{
    #[Route('', methods: ['GET'])]
    public function list(EventRepository $events): JsonResponse
    {
        return $this->json(array_map([$this, 'serializeEvent'], $events->findPublished()));
    }

    #[Route('/my', methods: ['GET'])]
    #[IsGranted('ROLE_ORGANIZER')]
    public function mine(EventRepository $events): JsonResponse
    {
        $items = $events->findBy(['organizer' => $this->getUser()], ['eventDate' => 'ASC']);

        return $this->json(array_map(fn (Event $event) => $this->serializeEvent($event, true), $items));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function detail(Event $event): JsonResponse
    {
        if (!$event->isPublished() && !$this->isGranted('EDIT', $event)) {
            return $this->jsonError('Event not found.', 404);
        }

        return $this->json($this->serializeEvent($event, true));
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ORGANIZER')]
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?: [];
        $dto = EventRequest::fromPayload($payload);
        if ($error = $this->validationErrorResponse($validator->validate($dto))) {
            return $error;
        }

        try {
            $eventDate = new \DateTimeImmutable($dto->eventDate);
        } catch (\Exception) {
            return $this->jsonError('Invalid eventDate.', 422);
        }

        if (!$this->isFutureOrToday($eventDate)) {
            return $this->jsonError('La date de l evenement doit etre superieure ou egale a la date du jour.', 422);
        }

        $event = (new Event())
            ->setTitle($dto->title)
            ->setDescription($dto->description)
            ->setEventDate($eventDate)
            ->setLocation($dto->location)
            ->setMaxParticipants($dto->maxParticipants)
            ->setIsPublished($dto->isPublished)
            ->setOrganizer($this->getUser());

        $entityManager->persist($event);
        $entityManager->flush();

        return $this->json($this->serializeEvent($event, true), 201);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Event $event, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $this->denyAccessUnlessGranted('EDIT', $event);
        $payload = json_decode($request->getContent(), true) ?: [];
        $dto = EventRequest::fromPayload([
            'title' => $payload['title'] ?? $event->getTitle(),
            'description' => $payload['description'] ?? $event->getDescription(),
            'eventDate' => $payload['eventDate'] ?? $event->getEventDate()->format(DATE_ATOM),
            'location' => $payload['location'] ?? $event->getLocation(),
            'maxParticipants' => $payload['maxParticipants'] ?? $event->getMaxParticipants(),
            'isPublished' => $payload['isPublished'] ?? $event->isPublished(),
        ]);
        if ($error = $this->validationErrorResponse($validator->validate($dto))) {
            return $error;
        }

        if (isset($payload['title'])) {
            $event->setTitle($dto->title);
        }
        if (isset($payload['description'])) {
            $event->setDescription($dto->description);
        }
        if (isset($payload['eventDate'])) {
            try {
                $eventDate = new \DateTimeImmutable($dto->eventDate);
            } catch (\Exception) {
                return $this->jsonError('Invalid eventDate.', 422);
            }

            if (!$this->isFutureOrToday($eventDate)) {
                return $this->jsonError('La date de l evenement doit etre superieure ou egale a la date du jour.', 422);
            }

            $event->setEventDate($eventDate);
        }
        if (isset($payload['location'])) {
            $event->setLocation($dto->location);
        }
        if (isset($payload['maxParticipants'])) {
            $event->setMaxParticipants($dto->maxParticipants);
        }
        if (isset($payload['isPublished'])) {
            $event->setIsPublished($dto->isPublished);
        }

        $entityManager->flush();
        return $this->json($this->serializeEvent($event, true));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Event $event, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('DELETE', $event);
        $entityManager->remove($event);
        $entityManager->flush();

        return $this->json(null, 204);
    }

    private function isFutureOrToday(\DateTimeImmutable $eventDate): bool
    {
        $today = new \DateTimeImmutable('today');

        return $eventDate >= $today;
    }

    public function serializeEvent(Event $event, bool $detail = false): array
    {
        $data = [
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'eventDate' => $event->getEventDate()->format(DATE_ATOM),
            'location' => $event->getLocation(),
            'maxParticipants' => $event->getMaxParticipants(),
            'registeredCount' => $event->confirmedRegistrationsCount(),
            'isPublished' => $event->isPublished(),
            'organizer' => [
                'id' => $event->getOrganizer()->getId(),
                'firstName' => $event->getOrganizer()->getFirstName(),
                'lastName' => $event->getOrganizer()->getLastName(),
            ],
        ];

        if ($detail) {
            $data['description'] = $event->getDescription();
            $data['createdAt'] = $event->getCreatedAt()->format(DATE_ATOM);
        }

        return $data;
    }
}
