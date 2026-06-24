<?php

namespace App\Tests\Api;

use App\Entity\ConsentLog;
use PHPUnit\Framework\TestCase;

class ConsentLogTest extends TestCase
{
    public function testConsentLogRejectsInvalidAction(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ConsentLog())->setAction('invalid');
    }
}
