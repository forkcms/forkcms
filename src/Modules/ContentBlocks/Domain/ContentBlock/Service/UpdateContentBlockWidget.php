<?php

namespace ForkCMS\Modules\ContentBlocks\Domain\ContentBlock\Service;

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

    public function getNewExtraId(string $title, ?Locale $locale = null): int
    {
        $block = new Block(
            ModuleBlock::fromFQCN(DetailWidget::class),
            TranslationKey::label('ContentBlocks')
        );

        // TODO set locale for frontend block

        $this->blockRepository->save($block);

        return $block->getId();
    }

    public function updateWidget(ContentBlock $contentBlock): void
    {
        /** @var Block $block */
        $block = $this->blockRepository->findOneById($contentBlock->getExtraId());

        /* if ($block->getSettings()->has('label')) {
            $block->getSettings()->remove('label');
        } */

        $block->getSettings()->add([
            'label' => $contentBlock->getTitle(),
            'id' => $contentBlock->getId(),
            'language' => $contentBlock->getLocale(),
        ]);

        $this->blockRepository->save($block);
    }
}
