<?php

namespace App\Entity;

use App\Repository\CookiePreferenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CookiePreferenceRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_cookie_preference_user', columns: ['user_id'])]
class CookiePreference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column]
    private bool $necessary = true;

    #[ORM\Column]
    private bool $statistics = false;

    #[ORM\Column]
    private bool $marketing = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    public function isNecessary(): bool { return $this->necessary; }
    public function setNecessary(bool $necessary): self { $this->necessary = $necessary; return $this; }
    public function isStatistics(): bool { return $this->statistics; }
    public function setStatistics(bool $statistics): self { $this->statistics = $statistics; return $this; }
    public function isMarketing(): bool { return $this->marketing; }
    public function setMarketing(bool $marketing): self { $this->marketing = $marketing; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
}
