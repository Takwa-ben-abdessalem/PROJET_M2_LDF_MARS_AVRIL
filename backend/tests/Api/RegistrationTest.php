<?php

namespace App\Tests\Api;

use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase
{
    public function testConfirmedRegistrationsCountIgnoresCancelled(): void
    {
        $organizer = (new User())->setEmail('orga-count@example.com');
        $event = (new Event())->setOrganizer($organizer);
        $user = (new User())->setEmail('user-count@example.com');

        $confirmed = (new Registration())->setUser($user)->setEvent($event)->setStatus(Registration::STATUS_CONFIRMED);
        $cancelled = (new Registration())->setUser($user)->setEvent($event)->setStatus(Registration::STATUS_CANCELLED);
        $event->getRegistrations()->add($confirmed);
        $event->getRegistrations()->add($cancelled);

        self::assertSame(1, $event->confirmedRegistrationsCount());
    }
}
