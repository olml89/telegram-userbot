<?php

declare(strict_types=1);

namespace Test\Shared\Unit;

use Mockery;
use Mockery\Matcher\Closure;
use Mockery\MockInterface;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
trait UsesMockery
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private function mockeryMock(string $class): MockInterface
    {
        return Mockery::mock($class);
    }

    private function expectedArgument(object $expected): Closure
    {
        return Mockery::on(
            fn (object $actual): bool => new IsEqual($expected)->evaluate($actual, '', true),
        );
    }
}
