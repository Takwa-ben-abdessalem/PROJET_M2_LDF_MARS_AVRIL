<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest
{
    #[Assert\NotBlank(message: 'Email obligatoire.')]
    #[Assert\Email(message: 'Email invalide.')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Mot de passe obligatoire.')]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caracteres.')]
    public ?string $password = null;

    #[Assert\NotBlank(message: 'Prenom obligatoire.')]
    #[Assert\Length(max: 100)]
    public ?string $firstName = null;

    #[Assert\NotBlank(message: 'Nom obligatoire.')]
    #[Assert\Length(max: 100)]
    public ?string $lastName = null;

    #[Assert\Length(max: 30)]
    public ?string $phone = null;

    #[Assert\IsTrue(message: 'Le consentement RGPD est obligatoire.')]
    public bool $consentAccepted = false;

    #[Assert\Length(max: 20)]
    public string $consentVersion = 'v1.0';

    public array $roles = ['ROLE_USER'];

    public ?string $organizerInviteCode = null;

    public static function fromPayload(array $payload): self
    {
        $dto = new self();
        $dto->email = $payload['email'] ?? null;
        $dto->password = $payload['password'] ?? null;
        $dto->firstName = $payload['firstName'] ?? null;
        $dto->lastName = $payload['lastName'] ?? null;
        $dto->phone = $payload['phone'] ?? null;
        $dto->consentAccepted = ($payload['consentAccepted'] ?? false) === true;
        $dto->consentVersion = $payload['consentVersion'] ?? 'v1.0';
        $dto->roles = is_array($payload['roles'] ?? null) ? $payload['roles'] : ['ROLE_USER'];
        $dto->organizerInviteCode = $payload['organizerInviteCode'] ?? null;

        return $dto;
    }
}
