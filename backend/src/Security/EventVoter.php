<?php

namespace App\Security;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['EDIT', 'DELETE'], true) && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return in_array('ROLE_ORGANIZER', $user->getRoles(), true)
            && $subject->getOrganizer()->getId() === $user->getId();
    }
}
