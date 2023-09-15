<?php

namespace ForkCMS\Core\Domain\Form\Editor;

use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

interface EditorTypeImplementationInterface
{
    public function getLabel(): TranslationKey;

    public function parseContent(string $content): string;
}
