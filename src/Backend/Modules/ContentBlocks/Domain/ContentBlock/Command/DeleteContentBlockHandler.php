<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use App\Component\Model\BackendModel;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;

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

        BackendModel::deleteExtraById($deleteContentBlock->contentBlock->getExtraId());
    }
}
