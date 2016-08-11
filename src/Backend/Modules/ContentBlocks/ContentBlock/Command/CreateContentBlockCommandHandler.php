<?php

namespace Backend\Modules\ContentBlocks\ContentBlock\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;
use Doctrine\ORM\EntityManager;

final class CreateContentBlockCommandHandler
{
    /** @var EntityManager */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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

        $createContentBlock->contentBlock = $contentBlock;
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
