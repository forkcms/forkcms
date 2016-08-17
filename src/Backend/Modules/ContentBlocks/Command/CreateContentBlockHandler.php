<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Event\ContentBlockCreated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateContentBlockHandler
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
     * @param CreateContentBlock $createContentBlock
     *
     * @return ContentBlock
     */
    public function handle(CreateContentBlock $createContentBlock)
    {
        $contentBlock = ContentBlock::create(
            $this->entityManager
                ->getRepository(ContentBlock::class)
                ->getNextIdForLanguage($createContentBlock->language),
            $this->getNewExtraId(),
            $createContentBlock->language,
            $createContentBlock->title,
            $createContentBlock->text,
            !$createContentBlock->isVisible,
            $createContentBlock->template
        );

        $this->entityManager->persist($contentBlock);

        $this->eventDispatcher->dispatch(
            ContentBlockCreated::EVENT_NAME,
            new ContentBlockCreated($contentBlock)
        );
    }

    /**
     * @return int
     */
    private function getNewExtraId()
    {
        return Model::insertExtra(
            'widget',
            'ContentBlocks',
            'Detail'
        );
    }
}
