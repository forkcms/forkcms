<?php

namespace Backend\Modules\ContentBlocks\ContentBlock;

use Backend\Core\Engine\Model;
use Doctrine\ORM\EntityManager;

class ContentBlockHandler
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
    public function create(CreateContentBlock $createContentBlock)
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
        $this->entityManager->flush($contentBlock);

        return $contentBlock;
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
