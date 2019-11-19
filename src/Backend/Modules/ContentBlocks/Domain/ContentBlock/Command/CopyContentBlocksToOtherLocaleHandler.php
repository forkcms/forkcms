<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Status;
use Backend\Modules\Pages\Domain\ModuleExtra\ModuleExtraType;
use Backend\Modules\Pages\Domain\Page\Page;
use Common\Locale;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleHandlerInterface;
use ForkCMS\Utility\Module\CopyContentToOtherLocale\CopyModuleContentToOtherLocaleInterface;
use RuntimeException;

final class CopyContentBlocksToOtherLocaleHandler implements CopyModuleContentToOtherLocaleHandlerInterface
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    public function handle(CopyModuleContentToOtherLocaleInterface $command): void
    {
        if (!$command instanceof CopyContentBlocksToOtherLocale) {
            throw new RuntimeException('The class should be ' . CopyContentBlocksToOtherLocale::class);
        }

        $contentBlocksToCopy = $this->getContentBlocksToCopy($command->getFromLocale(), $command->getPageToCopy());
        $id = $this->contentBlockRepository->getNextIdForLanguage($command->getToLocale());

        array_map(
            function (ContentBlock $contentBlock) use ($command, &$id) {
                $command->setModuleExtraId($contentBlock->getModuleExtraId(), $this->getNewExtraId());
                $dataTransferObject = $contentBlock->getDataTransferObject();

                // Overwrite some variables
                $dataTransferObject->forOtherLocale(
                    $id++,
                    $command->getModuleExtraId($contentBlock->getModuleExtraId()),
                    $command->getToLocale()
                );

                $this->contentBlockRepository->add(ContentBlock::fromDataTransferObject($dataTransferObject));
            },
            $contentBlocksToCopy
        );
    }

    private function getContentBlocksToCopy(Locale $locale, ?Page $filterByPage): array
    {
        $filter = [
            'locale' => $locale,
            'status' => Status::active(),
        ];

        if ($filterByPage instanceof Page) {
            $filter['revisionId'] = $filterByPage->getRevisionId();
        }

        return $this->contentBlockRepository->findBy($filter);
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
