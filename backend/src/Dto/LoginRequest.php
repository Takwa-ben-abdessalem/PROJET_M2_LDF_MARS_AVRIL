<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest
{
    #[Assert\NotBlank(message: 'Email obligatoire.')]
    #[Assert\Email(message: 'Email invalide.')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Mot de passe obligatoire.')]
    public ?string $password = null;

    public static function fromPayload(array $payload): self
    {
        $dto = new self();
        $dto->email = $payload['email'] ?? null;
        $dto->password = $payload['password'] ?? null;

        return $dto;
    }
}
