<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Service;

use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Command\CreateContentBlock;
use ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Modules\ContentBlocks\Frontend\Widgets\Detail as DetailWidget;
use ForkCMS\Modules\Frontend\Domain\Block\Block;
use ForkCMS\Modules\Frontend\Domain\Block\BlockRepository;
use ForkCMS\Modules\Frontend\Domain\Block\ModuleBlock;
use ForkCMS\Modules\Internationalisation\Domain\Locale\Locale;
use ForkCMS\Modules\Internationalisation\Domain\Translation\TranslationKey;

class UpdateContentBlockWidget
{
    public function __construct(
        private readonly BlockRepository $blockRepository
    ) {
    }

    public function getNewExtraId(CreatecontentBlock $createcontentBlock): int
    {
        $block = new Block(
            ModuleBlock::fromFQCN(DetailWidget::class),
            TranslationKey::label('ContentBlocks'),
            locale: $createcontentBlock->locale
        );

        $this->blockRepository->save($block);

        return $block->getId();
    }

    public function updateWidget(ContentBlock $contentBlock): void
    {
        /** @var Block $block */
        $block = $this->blockRepository->findOneById($contentBlock->getExtraId());

        $block->getSettings()->add([
            'extra_label' => $contentBlock->getTitle(),
            'id' => $contentBlock->getId(),
            'language' => $contentBlock->getLocale(),
        ]);

        $this->blockRepository->save($block);
    }
}
