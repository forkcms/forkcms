<?php

namespace App\Backend\Modules\Location\Command;

use App\Backend\Core\Language\Locale;

final class CopyLocationWidgetsToOtherLocale
{
    /** @var Locale */
    public $toLocale;

    /** @var Locale */
    public $fromLocale;

    /** @var array this is used to be able to convert the old ids to the new ones if used in other places */
    public $extraIdMap;

    public function __construct(Locale $toLocale, Locale $fromLocale = null)
    {
        if ($fromLocale === null) {
            $fromLocale = Locale::workingLocale();
        }

        $this->toLocale = $toLocale;
        $this->fromLocale = $fromLocale;
        $this->extraIdMap = [];
    }
}
