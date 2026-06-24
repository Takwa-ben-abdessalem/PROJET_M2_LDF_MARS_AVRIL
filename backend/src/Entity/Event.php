<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $description = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $eventDate;

    #[ORM\Column(length: 180)]
    private string $location = '';

    #[ORM\Column]
    private int $maxParticipants = 1;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private User $organizer;

    #[ORM\Column]
    private bool $isPublished = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Registration::class, orphanRemoval: true)]
    private Collection $registrations;

    public function __construct()
    {
        $this->eventDate = new \DateTimeImmutable('+7 days');
        $this->createdAt = new \DateTimeImmutable();
        $this->registrations = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = trim($title); return $this; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->description = trim($description); return $this; }
    public function getEventDate(): \DateTimeImmutable { return $this->eventDate; }
    public function setEventDate(\DateTimeImmutable $eventDate): self { $this->eventDate = $eventDate; return $this; }
    public function getLocation(): string { return $this->location; }
    public function setLocation(string $location): self { $this->location = trim($location); return $this; }
    public function getMaxParticipants(): int { return $this->maxParticipants; }
    public function setMaxParticipants(int $maxParticipants): self { $this->maxParticipants = max(1, $maxParticipants); return $this; }
    public function getOrganizer(): User { return $this->organizer; }
    public function setOrganizer(User $organizer): self { $this->organizer = $organizer; return $this; }
    public function isPublished(): bool { return $this->isPublished; }
    public function setIsPublished(bool $isPublished): self { $this->isPublished = $isPublished; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getRegistrations(): Collection { return $this->registrations; }

    public function confirmedRegistrationsCount(): int
    {
        return $this->registrations
            ->filter(fn (Registration $registration) => $registration->getStatus() !== Registration::STATUS_CANCELLED)
            ->count();
    }
}
