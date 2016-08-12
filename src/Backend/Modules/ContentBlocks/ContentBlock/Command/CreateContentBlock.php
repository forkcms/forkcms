<?php

namespace Backend\Modules\ContentBlocks\ContentBlock\Command;

use Backend\Core\Language\Locale;

final class CreateContentBlock extends ContentBlockCommand
{
    /** @var Locale */
    public $language;

    /**
     * @param Locale|null $language
     */
    public function __construct(Locale $language = null)
    {
        if ($language === null) {
            $language = Locale::workingLocale();
        }

        $this->language = $language;
    }
}
