<?php

namespace Backend\Core\Tests\Engine;

use Backend\Core\Engine\PasswordStrengthChecker;
use PHPUnit\Framework\TestCase;

class PasswordStrengthCheckerTest extends TestCase
{
    public function testWeakPasswords(): void
    {
        self::assertEquals('weak', PasswordStrengthChecker::checkPassword('abc'));
        self::assertEquals('weak', PasswordStrengthChecker::checkPassword('azerty'));
        self::assertEquals('weak', PasswordStrengthChecker::checkPassword('12345'));
        self::assertEquals('weak', PasswordStrengthChecker::checkPassword('forkcms'));
    }

    public function testAveragePasswords(): void
    {
        self::assertEquals('average', PasswordStrengthChecker::checkPassword('forkcms777'));
        self::assertEquals('average', PasswordStrengthChecker::checkPassword('I<3ForkCMS'));
        self::assertEquals('average', PasswordStrengthChecker::checkPassword('ForkR0cks'));
        self::assertEquals('average', PasswordStrengthChecker::checkPassword('CorrectHorseBatteryStaple'));
    }

    public function testStrongPasswords(): void
    {
        self::assertEquals('strong', PasswordStrengthChecker::checkPassword('ThisIsMySuperLongPasswordThatIsSave!'));
        self::assertEquals('strong', PasswordStrengthChecker::checkPassword('CorrectHorseBatteryStap!e'));
        self::assertEquals('strong', PasswordStrengthChecker::checkPassword('1kaoKzkda($%$azdazdamùzd'));
        self::assertEquals('strong', PasswordStrengthChecker::checkPassword('Qàsd)àé"ùéù"dmé"ld'));
    }
}
