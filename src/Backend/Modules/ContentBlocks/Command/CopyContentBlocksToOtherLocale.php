<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Language\Locale;

final class CopyContentBlocksToOtherLocale
{
    /** @var Locale */
    public $toLocale;

    /** @var Locale */
    public $fromLocale;

    /**
     * @param Locale $toLocale
     * @param Locale|null $fromLocale
     */
    public function __construct(Locale $toLocale, Locale $fromLocale = null)
    {
        if ($fromLocale === null) {
            $fromLocale = Locale::workingLocale();
        }

        $this->toLocale = $toLocale;
        $this->fromLocale = $fromLocale;
    }
}
