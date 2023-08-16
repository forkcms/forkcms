<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;

final class CreateContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ContentBlockRepository $contentBlockRepository)
    {
    }

    public function __invoke(CreateContentBlock $createContentBlock)
    {
        $contentBlock = ContentBlock::fromDataTransferObject($createContentBlock);
        $this->contentBlockRepository->save($contentBlock);
        $createContentBlock->setEntity($contentBlock);
    }
}
