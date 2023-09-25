<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Service\UpdateContentBlockWidget;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Status;

final readonly class ChangeContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(
        private ContentBlockRepository $contentBlockRepository
    ) {
    }

    public function __invoke(ChangeContentBlock $changeContentBlock): void
    {
        $contentBlock = ContentBlock::fromDataTransferObject($changeContentBlock);
        $contentBlock->activate();

        $previousActiveContentBlocks = $this->contentBlockRepository->findBy([
            'id' => $contentBlock->getId(),
            'status' => Status::Active
        ]);
        foreach ($previousActiveContentBlocks as $previousActiveContentBlock) {
            $previousActiveContentBlock->archive();
        }

        $this->contentBlockRepository->save($contentBlock);
    }
}
