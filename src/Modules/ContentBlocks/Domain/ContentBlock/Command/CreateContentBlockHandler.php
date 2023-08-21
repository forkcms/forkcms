<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Core\Domain\MessageHandler\CommandHandlerInterface;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Modules\ContentBlocks\Frontend\Widgets\Detail;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

final class CreateContentBlockHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly BlockRepository $blockRepository,
        private readonly ContentBlockRepository $contentBlockRepository
    ) {
    }

    public function __invoke(CreateContentBlock $createContentBlock): void
    {
        $createContentBlock->widget = $this->createWidget($createContentBlock->title, $createContentBlock->locale);
        $createContentBlock->id = $this->contentBlockRepository->getNextIdForLocale($createContentBlock->locale);

        $contentBlock = ContentBlock::fromDataTransferObject($createContentBlock);
        $this->contentBlockRepository->save($contentBlock);
        $createContentBlock->setEntity($contentBlock);
    }

    private function createWidget(string $title, Locale $locale): Block
    {
        $block = new Block(
            ModuleBlock::fromFQCN(Detail::class),
            TranslationKey::label('ContentBlock'),
            locale: $locale
        );
        $block->getSettings()->set('label', $title);
        $this->blockRepository->save($block);

        return $block;
    }
}
