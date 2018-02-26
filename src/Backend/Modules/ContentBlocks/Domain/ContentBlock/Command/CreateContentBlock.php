<?php

namespace ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Backend\Core\Language\Locale;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataTransferObject;

final class CreateContentBlock extends ContentBlockDataTransferObject
{
    public function __construct(Locale $locale = null)
    {
        parent::__construct();

        if ($locale === null) {
            $locale = Locale::workingLocale();
        }

        $this->locale = $locale;
    }

    public function setContentBlockEntity(ContentBlock $contentBlockEntity): void
    {
        $this->contentBlockEntity = $contentBlockEntity;
    }
}
