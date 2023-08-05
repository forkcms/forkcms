<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Translation\Command;

use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationDataTransferObject;

final class ChangeTranslation extends TranslationDataTransferObject
{
    public function __construct(Translation $translation)
    {
        parent::__construct($translation);
    }
}
