<?php

namespace App\Service;

use App\Entity\ConsentLog;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAnonymizer
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ConsentLogger $consentLogger,
    ) {
    }

    public function anonymize(User $user): void
    {
        if ($user->isAnonymized()) {
            return;
        }

        $sourceEmail = $user->getEmail();
        $anonymousEmail = hash('sha256', $sourceEmail);
        $user
            ->setEmail($anonymousEmail)
            ->setFirstName('Utilisateur supprimé')
            ->setLastName('Utilisateur supprimé')
            ->setPhone(null)
            ->setRoles(['ROLE_USER'])
            ->setIsAnonymized(true)
            ->setPassword($this->passwordHasher->hashPassword($user, bin2hex(random_bytes(24))));

        $this->consentLogger->log($user, ConsentLog::DATA_DELETED, 'Compte anonymise a la demande utilisateur ou par retention.');
    }
}
