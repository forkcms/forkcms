<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Exporter;

use ForkCMS\Modules\Internationalisation\Domain\Translation\Translation;

interface ExporterInterface
{
    /** @param iterable<Translation> $translations */
    public function exportTranslations(iterable $translations): string;

    public static function forExtension(): string;
}
