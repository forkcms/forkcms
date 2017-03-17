<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;

final class UpdateContentBlockHandler
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    /**
     * @param ContentBlockRepository $contentBlockRepository
     */
    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    /**
     * @param UpdateContentBlock $updateContentBlock
     *
     * @return ContentBlock
     */
    public function handle(UpdateContentBlock $updateContentBlock)
    {
        $updateContentBlock->contentBlock = $updateContentBlock->contentBlock->update(
            $updateContentBlock->title,
            $updateContentBlock->text,
            !$updateContentBlock->isVisible,
            $updateContentBlock->template,
            $updateContentBlock->userId
        );

        $this->contentBlockRepository->add($updateContentBlock->contentBlock);
    }
}
