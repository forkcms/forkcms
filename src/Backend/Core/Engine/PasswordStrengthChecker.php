<?php

namespace Backend\Core\Engine;

final class PasswordStrengthChecker
{
    const WEAK = 'weak';
    const AVERAGE = 'average';
    const STRONG = 'strong';

    /** @var int */
    private $score;

    /**
     * @param string $password
     */
    public function __construct(string $password)
    {
        $this->score = 0;

        if ($this->passwordIsTooShort($password) || $this->hasLessThan3UniqueCharacters($password)) {
            return;
        }

        $this->scoreStringLength($password);
        $this->scoreCharacterTypes($password);
    }

    /**
     * @param string $password
     */
    private function scoreCharacterTypes(string $password)
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

    /**
     * @param string $password
     */
    private function scoreStringLength(string $password)
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

    /**
     * @param string $password
     *
     * @return string
     */
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

    /**
     * @param string $password
     *
     * @return bool
     */
    private function passwordIsTooShort(string $password): bool
    {
        return mb_strlen($password) <= 4;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    private function hasLessThan3UniqueCharacters(string $password): bool
    {
        $uniqueChars = [];

        foreach (str_split($password) as $char) {
            $uniqueChars[$char] = $char;
        }

        return count($uniqueChars) < 3;
    }
}
