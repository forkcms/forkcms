<?php

namespace Backend\Core\Tests\Engine;

use Backend\Core\Engine\PasswordStrengthChecker;
use PHPUnit\Framework\TestCase;

class PasswordStrengthCheckerTest extends TestCase
{
    public function testWeakPasswords(): void
    {
        $this->assertEquals('weak', PasswordStrengthChecker::checkPassword('abc'));
        $this->assertEquals('weak', PasswordStrengthChecker::checkPassword('azerty'));
        $this->assertEquals('weak', PasswordStrengthChecker::checkPassword('12345'));
        $this->assertEquals('weak', PasswordStrengthChecker::checkPassword('forkcms'));
    }

    public function testAveragePasswords(): void
    {
        $this->assertEquals('average', PasswordStrengthChecker::checkPassword('forkcms777'));
        $this->assertEquals('average', PasswordStrengthChecker::checkPassword('I<3ForkCMS'));
        $this->assertEquals('average', PasswordStrengthChecker::checkPassword('ForkR0cks'));
        $this->assertEquals('average', PasswordStrengthChecker::checkPassword('CorrectHorseBatteryStaple'));
    }

    public function testStrongPasswords(): void
    {
        $this->assertEquals('strong', PasswordStrengthChecker::checkPassword('ThisIsMySuperLongPasswordThatIsSave!'));
        $this->assertEquals('strong', PasswordStrengthChecker::checkPassword('CorrectHorseBatteryStap!e'));
        $this->assertEquals('strong', PasswordStrengthChecker::checkPassword('1kaoKzkda($%$azdazdamùzd'));
        $this->assertEquals('strong', PasswordStrengthChecker::checkPassword('Qàsd)àé"ùéù"dmé"ld'));
    }
}
