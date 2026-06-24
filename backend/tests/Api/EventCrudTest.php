<?php

namespace App\Tests\Api;

use App\Entity\Event;
use App\Entity\User;
use App\Security\EventVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EventCrudTest extends TestCase
{
    public function testOrganizerOwnerCanEditEvent(): void
    {
        $user = (new User())->setEmail('orga@example.com')->setRoles(['ROLE_ORGANIZER']);
        $event = (new Event())->setOrganizer($user);
        $token = new UsernamePasswordToken($user, 'api', $user->getRoles());

        $voter = new EventVoter();

        self::assertSame(1, $voter->vote($token, $event, ['EDIT']));
    }
}
