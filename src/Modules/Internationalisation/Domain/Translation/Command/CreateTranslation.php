<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Command;

use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationDataTransferObject;

final class CreateTranslation extends TranslationDataTransferObject
{
    public function __construct(?Translation $translation = null)
    {
        parent::__construct($translation);

        // only used to load for copying the translation
        if ($translation !== null) {
            $this->translationEntity = null;
        }
    }

    public function setEntity(Translation $translation): void
    {
        $this->translationEntity = $translation;
    }
}
