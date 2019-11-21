<?php

namespace Backend\Modules\Pages\Domain\Page;

use Backend\Core\Language\Locale;

final class CopyPageDataTransferObject
{
    /** @var Locale */
    public $from;

    /** @var Locale */
    public $to;

    /** @var Page|null */
    public $pageToCopy;

    public function __construct(Locale $from = null, Page $pageToCopy = null)
    {
        if ($from !== null) {
            $this->from = $from;
        }
        if ($pageToCopy !== null) {
            $this->pageToCopy = $pageToCopy;
        }
    }
}
