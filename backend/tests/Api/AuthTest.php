<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    public function testRegisterRequiresConsent(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/auth/register', server: ['CONTENT_TYPE' => 'application/json'], content: json_encode([
            'email' => 'no-consent@example.com',
            'password' => 'Password123!',
            'firstName' => 'No',
            'lastName' => 'Consent',
            'consentAccepted' => false,
        ]));

        self::assertResponseStatusCodeSame(422);
    }
}
