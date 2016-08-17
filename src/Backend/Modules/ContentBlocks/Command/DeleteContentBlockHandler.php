<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Event\ContentBlockDeleted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteContentBlockHandler
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
     * @param DeleteContentBlock $deleteContentBlock
     *
     * @return ContentBlock
     */
    public function handle(DeleteContentBlock $deleteContentBlock)
    {
        $this->entityManager->getRepository(ContentBlock::class)->removeByIdAndLocale(
            $deleteContentBlock->contentBlock->getId(),
            $deleteContentBlock->contentBlock->getLocale()
        );

        Model::deleteExtraById($deleteContentBlock->contentBlock->getExtraId());

        $this->eventDispatcher->dispatch(
            ContentBlockDeleted::EVENT_NAME,
            new ContentBlockDeleted($deleteContentBlock->contentBlock)
        );
    }
}
