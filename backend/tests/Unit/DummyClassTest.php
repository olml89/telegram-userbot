<?php

declare(strict_types=1);

namespace Test\Backend\Unit;

use olml89\TelegramUserbot\Backend\DummyClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DummyClass::class)]
final class DummyClassTest extends TestCase
{
    public function testDummyClass(): void
    {
        $dummy = new DummyClass();

        $sum = $dummy->add(1, 2, 3, 4);

        self::assertEquals(10, $sum);
    }
}
