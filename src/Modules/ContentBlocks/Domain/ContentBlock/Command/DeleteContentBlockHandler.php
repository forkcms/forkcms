<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Status;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use Exception;

final class DeleteContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ContentBlockRepository $contentBlockRepository
    ) {
    }

    public function __invoke(DeleteContentBlock $deleteContentBlock): void
    {
        $versions = $this->contentBlockRepository->getVersionsForRevisionId($deleteContentBlock->id);

        if (count($versions) === 0) {
            return;
        }

        $activeBlock = null;
        foreach ($versions as $version) {
            if ($version->getStatus() === Status::ACTIVE) {
                $activeBlock = $version;
            }
        }

        if ($this->contentBlockRepository->isContentBlockInUse($activeBlock)) {
            return;
        }

        $this->contentBlockRepository->removeMultiple($versions);
    }
}
