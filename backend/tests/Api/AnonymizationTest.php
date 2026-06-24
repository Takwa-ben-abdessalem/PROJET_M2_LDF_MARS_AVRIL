<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Entity\ConsentLog;
use App\Service\ConsentLogger;
use App\Service\UserAnonymizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AnonymizationTest extends TestCase
{
    public function testAnonymizeMasksPersonalData(): void
    {
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->method('hashPassword')->willReturn('hashed');
        $logger = $this->createMock(ConsentLogger::class);
        $logger->expects(self::once())->method('log')->willReturn(new ConsentLog());

        $user = (new User())
            ->setEmail('person@example.com')
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setPhone('0600000000');

        (new UserAnonymizer($hasher, $logger))->anonymize($user);

        self::assertTrue($user->isAnonymized());
        self::assertSame('Utilisateur supprimé', $user->getFirstName());
        self::assertSame(hash('sha256', 'person@example.com'), $user->getEmail());
        self::assertNull($user->getPhone());
    }
}
