<?php

namespace ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use ForkCMS\Backend\Core\Engine\Model;
use ForkCMS\Backend\Core\Language\Locale;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use ForkCMS\Backend\Modules\ContentBlocks\Domain\ContentBlock\Status;
use ForkCMS\Common\ModuleExtraType;

final class CopyContentBlocksToOtherLocaleHandler
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    public function handle(CopyContentBlocksToOtherLocale $copyContentBlocksToOtherLocale): void
    {
        $contentBlocksToCopy = $this->getContentBlocksToCopy($copyContentBlocksToOtherLocale->fromLocale);
        $id = $this->contentBlockRepository->getNextIdForLanguage($copyContentBlocksToOtherLocale->toLocale);

        array_map(
            function (ContentBlock $contentBlock) use ($copyContentBlocksToOtherLocale, &$id) {
                $copyContentBlocksToOtherLocale->extraIdMap[$contentBlock->getExtraId()] = $this->getNewExtraId();
                $dataTransferObject = $contentBlock->getDataTransferObject();

                // Overwrite some variables
                $dataTransferObject->forOtherLocale(
                    $id++,
                    $copyContentBlocksToOtherLocale->extraIdMap[$contentBlock->getExtraId()],
                    $copyContentBlocksToOtherLocale->toLocale
                );

                $this->contentBlockRepository->add(ContentBlock::fromDataTransferObject($dataTransferObject));
            },
            $contentBlocksToCopy
        );
    }

    private function getContentBlocksToCopy(Locale $locale): array
    {
        return (array) $this->contentBlockRepository->findBy(
            [
                'locale' => $locale,
                'status' => Status::active()
            ]
        );
    }

    private function getNewExtraId(): int
    {
        return Model::insertExtra(
            ModuleExtraType::widget(),
            'ContentBlocks',
            'Detail'
        );
    }
}
