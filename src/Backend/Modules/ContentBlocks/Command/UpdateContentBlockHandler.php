<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Event\ContentBlockUpdated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateContentBlockHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
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

        $this->entityManager->persist($updateContentBlock->contentBlock);

        $this->eventDispatcher->dispatch(
            ContentBlockUpdated::EVENT_NAME,
            new ContentBlockUpdated($updateContentBlock->contentBlock)
        );
    }
}
