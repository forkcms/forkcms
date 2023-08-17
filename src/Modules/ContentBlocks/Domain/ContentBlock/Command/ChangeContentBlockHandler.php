<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;

final class ChangeContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ContentBlockRepository $contentBlockRepository)
    {
    }

    public function __invoke(ChangeContentBlock $changeContentBlock)
    {
        $contentBlock = ContentBlock::fromDataTransferObject($changeContentBlock);

        $previousContentBlock = $changeContentBlock->getEntity();
        $previousContentBlock->archive();

        $this->contentBlockRepository->save($previousContentBlock);
        $this->contentBlockRepository->save($contentBlock);
    }
}
