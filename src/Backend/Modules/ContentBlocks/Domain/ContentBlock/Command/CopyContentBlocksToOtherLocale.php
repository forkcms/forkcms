<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use App\Component\Locale\BackendLocale;

final class CopyContentBlocksToOtherLocale
{
    /** @var BackendLocale */
    public $toLocale;

    /** @var BackendLocale */
    public $fromLocale;

    /** @var array this is used to be able to convert the old ids to the new ones if used in other places */
    public $extraIdMap;

    public function __construct(BackendLocale $toLocale, BackendLocale $fromLocale = null)
    {
        if ($fromLocale === null) {
            $fromLocale = BackendLocale::workingLocale();
        }

        $this->toLocale = $toLocale;
        $this->fromLocale = $fromLocale;
        $this->extraIdMap = [];
    }
}
