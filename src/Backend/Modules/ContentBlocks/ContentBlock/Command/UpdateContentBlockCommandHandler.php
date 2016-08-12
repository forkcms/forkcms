<?php

namespace Backend\Modules\ContentBlocks\ContentBlock\Command;

use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;
use Doctrine\ORM\EntityManager;

final class UpdateContentBlockCommandHandler
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
    }
}
