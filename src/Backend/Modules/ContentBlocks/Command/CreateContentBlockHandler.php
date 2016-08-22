<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Event\ContentBlockCreated;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateContentBlockHandler
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
     * @param CreateContentBlock $createContentBlock
     *
     * @return ContentBlock
     */
    public function handle(CreateContentBlock $createContentBlock)
    {
        $contentBlock = ContentBlock::create(
            $this->contentBlockRepository->getNextIdForLanguage($createContentBlock->language),
            $this->getNewExtraId(),
            $createContentBlock->language,
            $createContentBlock->title,
            $createContentBlock->text,
            !$createContentBlock->isVisible,
            $createContentBlock->template
        );

        $this->contentBlockRepository->add($contentBlock);

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
