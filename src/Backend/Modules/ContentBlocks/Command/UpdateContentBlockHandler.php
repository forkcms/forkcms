<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Event\ContentBlockUpdated;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateContentBlockHandler
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ContentBlockRepository $contentBlockRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ContentBlockRepository $contentBlockRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
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
            $updateContentBlock->template
        );

        $this->contentBlockRepository->add($updateContentBlock->contentBlock);

        $this->eventDispatcher->dispatch(
            ContentBlockUpdated::EVENT_NAME,
            new ContentBlockUpdated($updateContentBlock->contentBlock)
        );
    }
}
