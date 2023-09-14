<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;

final readonly class ChangeContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(
        private ContentBlockRepository $contentBlockRepository,
        private BlockRepository $blockRepository
    ) {
    }

    public function __invoke(ChangeContentBlock $changeContentBlock): void
    {
        $contentBlock = ContentBlock::fromDataTransferObject($changeContentBlock);

        $this->updateFrontendBlock($contentBlock);

        $previousContentBlock = $changeContentBlock->getEntity();
        $previousContentBlock->archive();

        $this->contentBlockRepository->save($previousContentBlock);
        $this->contentBlockRepository->save($contentBlock);
    }

    private function updateFrontendBlock(ContentBlock $contentBlock): void
    {
        $widget = $contentBlock->getWidget();
        $widget->getSettings()->set('label', $contentBlock->getTitle());
        $this->blockRepository->save($widget);
    }
}
