<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;

final class DeleteContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ContentBlockRepository $contentBlockRepository)
    {
    }

    public function __invoke(DeleteContentBlock $deleteContentBlock)
    {
        $contentBlock = $this->contentBlockRepository->find($deleteContentBlock->id) ?? throw new \InvalidArgumentException('Entity not found');
        $this->contentBlockRepository->remove($contentBlock);
        $deleteContentBlock->setEntity($contentBlock);
    }
}
