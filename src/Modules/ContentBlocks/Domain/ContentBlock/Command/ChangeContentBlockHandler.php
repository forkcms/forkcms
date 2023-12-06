<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Event\ContentBlockChangedEvent;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Status;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final readonly class ChangeContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(
        private ContentBlockRepository $contentBlockRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ChangeContentBlock $changeContentBlock): void
    {
        $contentBlock = ContentBlock::fromDataTransferObject($changeContentBlock);
        $contentBlock->activate();

        $previousActiveContentBlocks = $this->contentBlockRepository->findBy([
            'id' => $contentBlock->getId(),
            'status' => Status::ACTIVE
        ]);
        foreach ($previousActiveContentBlocks as $previousActiveContentBlock) {
            $previousActiveContentBlock->archive();
        }

        $this->contentBlockRepository->save($contentBlock);
        $this->eventDispatcher->dispatch(new ContentBlockChangedEvent($contentBlock));
    }
}
