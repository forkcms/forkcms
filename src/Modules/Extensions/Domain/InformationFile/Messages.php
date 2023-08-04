<?php

namespace ForkCMS\Modules\Extensions\Domain\InformationFile;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;
use ForkCMS\Modules\Internationalisation\Domain\Translation\Type;

final class Messages
{
    /** @param TranslationKey[] $messages */
    public function __construct(private array $messages = [])
    {
    }

    public function addMessage(TranslationKey $message): void
    {
        $this->messages[(string) $message->getName()] = $message;
    }

    /** @return array<string, TranslationKey> */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function hasErrors(): bool
    {
        return count(
            array_filter(
                $this->messages,
                static fn (TranslationKey $translationKey): bool => $translationKey->getType() === Type::err
            )
        ) === 0;
    }
}
