<?php

namespace ForkCMS\Core\Domain\Header\FlashMessage;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Type;
use Stringable;
use Symfony\Contracts\Translation\TranslatableInterface;

final class FlashMessage
{
    public function __construct(private string|TranslatableInterface $message, private FlashMessageType $type)
    {
    }

    public function getMessage(): string|TranslatableInterface
    {
        return $this->message;
    }

    public function getType(): FlashMessageType
    {
        return $this->type;
    }

    /** @param array<string, string|int|float|Stringable> $parameters */
    public static function success(string $successMessage, array $parameters = []): self
    {
        return new self(
            TranslationKey::message($successMessage)->withParameters($parameters),
            FlashMessageType::SUCCESS
        );
    }

    /** @param array<string, string|int|float|Stringable> $parameters */
    public static function info(string $infoMessage, array $parameters = []): self
    {
        return new self(TranslationKey::message($infoMessage)->withParameters($parameters), FlashMessageType::INFO);
    }

    /**
     * @param Type|null $translationType defaults to error
     * @param array<string, string|int|float|Stringable> $parameters
     */
    public static function warning(string $warningMessage, array $parameters = [], Type $translationType = null): self
    {
        return new self(
            TranslationKey::forType($translationType ?? Type::ERROR, $warningMessage)->withParameters($parameters),
            FlashMessageType::WARNING
        );
    }

    /** @param array<string, string|int|float|Stringable> $parameters */
    public static function error(string $errorMessage, array $parameters = []): self
    {
        return new self(TranslationKey::error($errorMessage)->withParameters($parameters), FlashMessageType::ERROR);
    }
}
