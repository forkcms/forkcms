<?php

namespace Backend\Modules\ContentBlocks\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\Repository\ContentBlockRepository;
use Backend\Modules\ContentBlocks\ValueObject\ContentBlockStatus;
use Common\ModuleExtraType;

final class CopyContentBlocksToOtherLocaleHandler
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    /**
     * @param ContentBlockRepository $contentBlockRepository
     */
    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    /**
     * @param CopyContentBlocksToOtherLocale $copyContentBlocksToOtherLocale
     *
     * @return ContentBlock
     */
    public function handle(CopyContentBlocksToOtherLocale $copyContentBlocksToOtherLocale)
    {
        $fromLocaleContentBlocks = (array) $this->contentBlockRepository->findBy(
            ['locale' => $copyContentBlocksToOtherLocale->fromLocale, 'status' => ContentBlockStatus::active()]
        );

        array_map(
            function (ContentBlock $contentBlock) use ($copyContentBlocksToOtherLocale) {
                $copyContentBlocksToOtherLocale->extraIdMap[$contentBlock->getExtraId()] = $this->getNewExtraId();

                $otherLocaleContentBlock = ContentBlock::create(
                    $this->contentBlockRepository->getNextIdForLanguage($copyContentBlocksToOtherLocale->toLocale),
                    $copyContentBlocksToOtherLocale->extraIdMap[$contentBlock->getExtraId()],
                    $copyContentBlocksToOtherLocale->toLocale,
                    $contentBlock->getTitle(),
                    $contentBlock->getText(),
                    $contentBlock->isHidden(),
                    $contentBlock->getTemplate()
                );

                $this->contentBlockRepository->add($otherLocaleContentBlock);
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
            ModuleExtraType::widget(),
            'ContentBlocks',
            'Detail'
        );
    }
}
