<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class CreateContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly BlockRepository $blockRepository,
        private readonly ContentBlockRepository $contentBlockRepository
    ) {
    }

    public function __invoke(CreateContentBlock $createContentBlock)
    {
        $createContentBlock->extraId = $this->getNewExtraId();
        $createContentBlock->id = $this->contentBlockRepository->getNextIdForLanguage($createContentBlock->locale);

        $contentBlock = ContentBlock::fromDataTransferObject($createContentBlock);
        $this->contentBlockRepository->save($contentBlock);
        $createContentBlock->setEntity($contentBlock);
    }

    private function getNewExtraId(): int
    {
        $block = new Block(
            ModuleBlock::fromFQCN('ForkCMS\Modules\ContentBlocks\Frontend\Widgets\Detail'),
            TranslationKey::label('ContentBlocks')
        );
        $this->blockRepository->save($block);

        return $block->getId();
    }
}
