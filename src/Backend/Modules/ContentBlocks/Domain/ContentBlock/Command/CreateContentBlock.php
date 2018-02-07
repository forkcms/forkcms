<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use App\Component\Locale\BackendLocale;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataTransferObject;

final class CreateContentBlock extends ContentBlockDataTransferObject
{
    public function __construct(BackendLocale $locale = null)
    {
        parent::__construct();

        if ($locale === null) {
            $locale = BackendLocale::workingLocale();
        }

        $this->locale = $locale;
    }

    public function setContentBlockEntity(ContentBlock $contentBlockEntity): void
    {
        $this->contentBlockEntity = $contentBlockEntity;
    }
}
