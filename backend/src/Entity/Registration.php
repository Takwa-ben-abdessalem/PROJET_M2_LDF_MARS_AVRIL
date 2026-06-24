<?php

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_registration_user_event', columns: ['user_id', 'event_id'])]
class Registration
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    private Event $event;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $registeredAt;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_CONFIRMED;

    public function __construct()
    {
        $this->registeredAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    public function getEvent(): Event { return $this->event; }
    public function setEvent(Event $event): self { $this->event = $event; return $this; }
    public function getRegisteredAt(): \DateTimeImmutable { return $this->registeredAt; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_CANCELLED], true)) {
            throw new \InvalidArgumentException('Invalid registration status.');
        }

        $this->status = $status;
        return $this;
    }
}
