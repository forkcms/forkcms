<?php

namespace Backend\Modules\Pages\Domain\Page\Command;

use Backend\Modules\Pages\Domain\Page\Page;
use Backend\Modules\Pages\Domain\Page\PageDataTransferObject;
use Backend\Modules\Pages\Domain\Page\Type;
use Common\Locale;

final class CreatePage extends PageDataTransferObject
{
    public function __construct(Locale $locale, int $templateId, Page $parent = null, Page $copiedFromPage = null)
    {
        parent::__construct($copiedFromPage, $copiedFromPage === null ? $templateId : null);

        $this->locale = $locale;

        if ($copiedFromPage === null) {
            $this->type = Type::root();
            $this->parentId = 0;
        }

        if ($parent instanceof Page && $parent->isAllowChildren()) {
            $this->parentId = $parent->getId();
            $this->type = Type::page();
        }
    }
}
