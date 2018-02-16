<?php

namespace Backend\Modules\ContentBlocks\Domain\ContentBlock\Command;

use Backend\Core\Engine\Model;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlock;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\ContentBlockRepository;
use Backend\Modules\ContentBlocks\Domain\ContentBlock\Status;
use Common\Locale;
use Common\ModuleExtraType;
use ForkCMS\Component\Module\CopyModuleToOtherLocaleHandlerInterface;
use ForkCMS\Component\Module\CopyModuleToOtherLocaleInterface;

final class CopyContentBlocksToOtherLocaleHandler implements CopyModuleToOtherLocaleHandlerInterface
{
    /** @var ContentBlockRepository */
    private $contentBlockRepository;

    public function __construct(ContentBlockRepository $contentBlockRepository)
    {
        $this->contentBlockRepository = $contentBlockRepository;
    }

    public function handle(CopyModuleToOtherLocaleInterface $command): void
    {
        if (!$command instanceof CopyContentBlocksToOtherLocale) {
            throw new \Exception('The class should be ' . CopyContentBlocksToOtherLocale::class);
        }

        $contentBlocksToCopy = $this->getContentBlocksToCopy($command->getFromLocale());
        $id = $this->contentBlockRepository->getNextIdForLanguage($command->getToLocale());

        array_map(
            function (ContentBlock $contentBlock) use ($command, &$id) {
                $command->setExtraId($contentBlock->getExtraId(), $this->getNewExtraId());
                $dataTransferObject = $contentBlock->getDataTransferObject();

                // Overwrite some variables
                $dataTransferObject->forOtherLocale(
                    $id++,
                    $command->getExtraId($contentBlock->getExtraId()),
                    $command->getToLocale()
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
