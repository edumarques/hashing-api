<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use App\Service\HashingService;
use App\DTO\AbstractCustomHash;

final class HashingServiceTest extends TestCase
{
    private HashingService $hashingService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->hashingService = new HashingService();
    }


    public function testGenerateCustomMd5(): void
    {
        $actual = $this->hashingService->generateCustomMd5('randomString');

        self::assertStringStartsWith('0000', $actual->getHash());
        self::assertSame(8, mb_strlen($actual->getKey()));
        self::assertSame(32, mb_strlen($actual->getHash()));
        self::assertSame(AbstractCustomHash::MD5, $actual->getAlgorithm());
    }
}