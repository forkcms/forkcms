<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Doctrine\ORM\EntityManager;

final class CopyContentBlocksToOtherLocaleHandler
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
     * @param CopyContentBlocksToOtherLocale $copyContentBlocksToOtherLocale
     *
     * @return ContentBlock
     */
    public function handle(CopyContentBlocksToOtherLocale $copyContentBlocksToOtherLocale)
    {
        $fromLocaleContentBlocks = (array) $this->entityManager
            ->getRepository(ContentBlock::class)
            ->findBy(['locale' => $copyContentBlocksToOtherLocale->fromLocale]);

        array_map(
            function (ContentBlock $contentBlock) use ($copyContentBlocksToOtherLocale) {
                $copyContentBlocksToOtherLocale->extraIdMap[$contentBlock->getExtraId()] = $this->getNewExtraId();

                $otherLocaleContentBlock = ContentBlock::create(
                    $this->entityManager
                        ->getRepository(ContentBlock::class)
                        ->getNextIdForLanguage($copyContentBlocksToOtherLocale->toLocale),
                    $copyContentBlocksToOtherLocale->extraIdMap[$contentBlock->getExtraId()],
                    $copyContentBlocksToOtherLocale->toLocale,
                    $contentBlock->getTitle(),
                    $contentBlock->getText(),
                    $contentBlock->isHidden(),
                    $contentBlock->getTemplate()
                );

                $this->entityManager->persist($otherLocaleContentBlock);
            },
            $fromLocaleContentBlocks
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
