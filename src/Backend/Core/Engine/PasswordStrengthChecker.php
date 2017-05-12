<?php

namespace Backend\Core\Engine;

final class PasswordStrengthChecker
{
    private const WEAK = 'weak';
    private const AVERAGE = 'average';
    private const STRONG = 'strong';

    /** @var int */
    private $score;

    private function __construct(string $password)
    {
        $this->score = 0;

        if ($this->passwordIsTooShort($password) || $this->hasLessThan3UniqueCharacters($password)) {
            return;
        }

        $this->scoreStringLength($password);
        $this->scoreCharacterTypes($password);
    }

    private function scoreCharacterTypes(string $password): void
    {
        // upper and lowercase?
        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            $this->score += 2;
        }

        // number?
        if (preg_match('/\d+/', $password)) {
            ++$this->score;
        }

        // special char?
        if (preg_match('/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/', $password)) {
            ++$this->score;
        }
    }

    private function scoreStringLength(string $password): void
    {
        if (mb_strlen($password) >= 6) {
            ++$this->score;
        }

        if (mb_strlen($password) >= 8) {
            ++$this->score;
        }

        if (mb_strlen($password) >= 12) {
            ++$this->score;
        }
    }

    public static function checkPassword(string $password): string
    {
        $checker = new self($password);

        switch (true) {
            case $checker->score >= 6:
                return self::STRONG;
            case $checker->score >= 2:
                return self::AVERAGE;
            default:
                return self::WEAK;
        }
    }

    private function passwordIsTooShort(string $password): bool
    {
        return mb_strlen($password) <= 4;
    }

    private function hasLessThan3UniqueCharacters(string $password): bool
    {
        $uniqueChars = [];

        foreach (str_split($password) as $char) {
            $uniqueChars[$char] = $char;
        }

        return count($uniqueChars) < 3;
    }
}
