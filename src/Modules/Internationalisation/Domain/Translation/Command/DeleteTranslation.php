<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Command;

final class DeleteTranslation
{
    public function __construct(private string $translationId)
    {
    }

    public function getTranslationId(): string
    {
        return $this->translationId;
    }
}
