<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Importer;

use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;
use Generator;
use Symfony\Component\HttpFoundation\File\File;

interface ImporterInterface
{
    /** @return Generator<Translation> */
    public function getTranslations(File $translationFile): Generator;

    public static function forExtension(): string;
}
