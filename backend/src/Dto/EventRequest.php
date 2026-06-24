<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class EventRequest
{
    #[Assert\NotBlank(message: 'Titre obligatoire.')]
    #[Assert\Length(max: 180)]
    public ?string $title = null;

    #[Assert\NotBlank(message: 'Description obligatoire.')]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'Date obligatoire.')]
    public ?string $eventDate = null;

    #[Assert\NotBlank(message: 'Lieu obligatoire.')]
    #[Assert\Length(max: 180)]
    public ?string $location = null;

    #[Assert\NotBlank(message: 'Nombre de participants obligatoire.')]
    #[Assert\Positive(message: 'Le nombre de participants doit etre superieur a 0.')]
    public ?int $maxParticipants = null;

    public bool $isPublished = false;

    public static function fromPayload(array $payload): self
    {
        $dto = new self();
        $dto->title = $payload['title'] ?? null;
        $dto->description = $payload['description'] ?? null;
        $dto->eventDate = $payload['eventDate'] ?? null;
        $dto->location = $payload['location'] ?? null;
        $dto->maxParticipants = isset($payload['maxParticipants']) ? (int) $payload['maxParticipants'] : null;
        $dto->isPublished = (bool) ($payload['isPublished'] ?? false);

        return $dto;
    }
}
