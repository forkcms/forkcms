<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockDataTransferObject;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Status;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;

final class CreateContentBlock extends ContentBlockDataTransferObject
{
    public function __construct(Locale $locale)
    {
        parent::__construct();

        $this->locale = $locale;
        $this->status = Status::ACTIVE;
    }

    public function setEntity(ContentBlock $contentBlock): void
    {
        $this->contentBlockEntity = $contentBlock;
    }
}
