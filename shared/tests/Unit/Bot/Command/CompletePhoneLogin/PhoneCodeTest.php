<?php

declare(strict_types=1);

namespace Test\Shared\Unit\Bot\Command\CompletePhoneLogin;

use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\InvalidPhoneCodeException;
use olml89\TelegramUserbot\Shared\Bot\Command\CompletePhoneLogin\PhoneCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PhoneCode::class)]
final class PhoneCodeTest extends TestCase
{
    public function testItThrowsInvalidCompletePhoneLoginCommandExceptionIfCodeIsNull(): void
    {
        $this->expectExceptionObject(InvalidPhoneCodeException::missingCode());

        new PhoneCode(code: null);
    }

    public function testItThrowsInvalidCompletePhoneLoginCommandExceptionIfCodeIsNotAString(): void
    {
        $this->expectExceptionObject(InvalidPhoneCodeException::notString());

        new PhoneCode(code: 12345);
    }

    public function testItThrowsInvalidCompletePhoneLoginCommandExceptionIfCodeIsNot5CharactersLength(): void
    {
        $invalidCode = '123456';
        $this->expectExceptionObject(InvalidPhoneCodeException::invalidCode($invalidCode));

        new PhoneCode(code: $invalidCode);
    }

    public function testItThrowsInvalidCompletePhoneLoginCommandExceptionIfCodeIsNotNumeric(): void
    {
        $invalidCode = 'abcde';
        $this->expectExceptionObject(InvalidPhoneCodeException::invalidCode($invalidCode));

        new PhoneCode(code: $invalidCode);
    }

    public function testItCreatesPhoneCode(): void
    {
        $code = '12345';
        $phoneCode = new PhoneCode($code);

        self::assertEquals($code, (string)$phoneCode);
    }
}
