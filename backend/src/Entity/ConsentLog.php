<?php

namespace App\Entity;

use App\Repository\ConsentLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConsentLogRepository::class)]
class ConsentLog
{
    public const CONSENT_GIVEN = 'consent_given';
    public const CONSENT_WITHDRAWN = 'consent_withdrawn';
    public const DATA_ACCESSED = 'data_accessed';
    public const DATA_DELETED = 'data_deleted';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 40)]
    private string $action = self::DATA_ACCESSED;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $timestamp;

    #[ORM\Column(length: 64)]
    private string $ipAddress = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $details = '';

    public function __construct()
    {
        $this->timestamp = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getAction(): string { return $this->action; }
    public function setAction(string $action): self
    {
        if (!in_array($action, [self::CONSENT_GIVEN, self::CONSENT_WITHDRAWN, self::DATA_ACCESSED, self::DATA_DELETED], true)) {
            throw new \InvalidArgumentException('Invalid consent action.');
        }

        $this->action = $action;
        return $this;
    }
    public function getTimestamp(): \DateTimeImmutable { return $this->timestamp; }
    public function getIpAddress(): string { return $this->ipAddress; }
    public function setIpAddress(string $ipAddress): self { $this->ipAddress = $ipAddress; return $this; }
    public function getDetails(): string { return $this->details; }
    public function setDetails(string $details): self { $this->details = $details; return $this; }
}
