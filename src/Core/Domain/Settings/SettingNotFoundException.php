<?php

namespace ForkCMS\Core\Domain\Settings;

use InvalidArgumentException;
use Throwable;

use function count;

final class SettingNotFoundException extends InvalidArgumentException
{
    /**
     * @param string $key The requested setting key
     * @param Throwable|null $previous The previous exception
     * @param string[] $alternatives Some setting name alternatives
     */
    public function __construct(
        private readonly string $key,
        Throwable|null $previous = null,
        private readonly array $alternatives = [],
    ) {
        parent::__construct($this->createMessage(), 0, $previous);
    }

    private function createMessage(): string
    {
        $message = sprintf('You have requested a non-existent setting "%s".', $this->key);

        $alternativesCount = count($this->alternatives);

        if ($alternativesCount === 0) {
            return $message;
        }

        if ($alternativesCount === 1) {
            $message .= ' Did you mean this: "';
        } else {
            $message .= ' Did you mean one of these: "';
        }

        $message .= implode('", "', $this->alternatives) . '"?';

        return $message;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
