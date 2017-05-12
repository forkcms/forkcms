<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;

final class DeleteContentBlockHandler
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    public function handle(DeleteContentBlock $deleteContentBlock): void
    {
        $this->contentBlockRepository->removeByIdAndLocale(
            $deleteContentBlock->contentBlock->getId(),
            $deleteContentBlock->contentBlock->getLocale()
        );

        Model::deleteExtraById($deleteContentBlock->contentBlock->getExtraId());
    }
}
