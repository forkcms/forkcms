<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use Exception;

final class DeleteContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ContentBlockRepository $contentBlockRepository,
        private readonly BlockRepository $blockRepository
    ) {
    }

    public function __invoke(DeleteContentBlock $deleteContentBlock)
    {
        $versions = $this->contentBlockRepository->getVersionsForRevisionId($deleteContentBlock->id);

        if (count($versions) === 0) {
            return;
        }

        $this->contentBlockRepository->removeMultiple($versions);

        try {
            $extraId = $versions[0]->getExtraId();
            $block = $this->blockRepository->findOneBy(['id' => $extraId]);
            if ($block instanceof Block) {
                $this->blockRepository->remove($block);
            }
        } catch (Exception) {
            // do nothing
        }
    }
}