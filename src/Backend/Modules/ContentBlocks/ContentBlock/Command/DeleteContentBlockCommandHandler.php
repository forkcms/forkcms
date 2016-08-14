<?php

namespace Backend\Modules\ContentBlocks\ContentBlock\Command;

use Backend\Core\Engine\Model;
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
    }
}
