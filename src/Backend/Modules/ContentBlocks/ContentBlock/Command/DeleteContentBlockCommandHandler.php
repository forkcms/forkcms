<?php

namespace Backend\Modules\ContentBlocks\ContentBlock\Command;

use Backend\Modules\ContentBlocks\ContentBlock\ContentBlock;
use Doctrine\ORM\EntityManager;

final class DeleteContentBlockCommandHandler
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
        $this->entityManager->remove($updateContentBlock->contentBlock);
    }
}
