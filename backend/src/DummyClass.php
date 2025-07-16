<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend;

final readonly class DummyClass
{
    public function add(int ...$values): int
    {
        $sum = 0;

        foreach ($values as $value) {
            $sum += $value;
        }

        return $sum;
    }
}
